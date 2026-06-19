<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Order;
use common\models\OrderItem;
use common\services\CartService;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class DashboardController extends Controller
{
    public $layout = 'customer-app';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['live-data' => ['GET']],
            ],
        ];
    }

    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            $navMap = [
                'index' => 'dashboard',
                'inventory' => 'inventory',
            ];
            $this->view->params['customerNav'] = $navMap[$action->id] ?? 'dashboard';

            return true;
        }

        return false;
    }

    public function actionIndex(): string
    {
        $this->view->title = 'Live Insights';
        $userId = (int) Yii::$app->user->id;
        $snapshot = $this->buildSnapshot($userId);

        return $this->render('index', [
            'snapshot' => $snapshot,
            'displayName' => Yii::$app->params['restaurant.displayName'],
            'username' => Yii::$app->user->identity->username,
        ]);
    }

    public function actionInventory(): string
    {
        $this->view->title = 'Inventory';

        return $this->render('inventory');
    }

    public function actionLiveData(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $userId = (int) Yii::$app->user->id;
        $range = Yii::$app->request->get('range', 'today');

        return [
            'success' => true,
            'range' => $range,
            ...$this->buildSnapshot($userId, $range),
        ];
    }

    /**
     * @return array{avg_order_value: float, total_orders: int, recent_orders: list<array>, top_items: list<array>}
     */
    private function buildSnapshot(int $userId, string $range = 'today'): array
    {
        $query = Order::find()->where(['user_id' => $userId]);
        $this->applyDateRange($query, $range);

        $orders = (clone $query)->all();
        $totalOrders = count($orders);
        $avgOrder = $totalOrders > 0
            ? array_sum(array_map(static fn (Order $o) => (float) $o->total, $orders)) / $totalOrders
            : 0.0;

        $recentOrders = Order::find()
            ->where(['user_id' => $userId])
            ->with('items')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(6)
            ->all();

        $recent = array_map(static function (Order $order): array {
            $names = array_map(static fn (OrderItem $i) => $i->food_name, $order->items);
            $summary = $names !== []
                ? implode(', ', array_slice($names, 0, 2)) . (count($names) > 2 ? '…' : '')
                : '—';

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'items_summary' => $summary,
                'total' => 'TZS ' . number_format((float) $order->total, 0, '.', ','),
                'status' => $order->status,
                'status_label' => Order::statuses()[$order->status] ?? ucfirst($order->status),
                'date' => Yii::$app->formatter->asDatetime($order->created_at, 'php:M j, H:i'),
            ];
        }, $recentOrders);

        $topRows = OrderItem::find()
            ->alias('oi')
            ->select([
                'name' => 'oi.food_name',
                'qty' => new Expression('SUM(oi.quantity)'),
                'revenue' => new Expression('SUM(oi.total_price)'),
            ])
            ->innerJoin(['o' => Order::tableName()], 'o.id = oi.order_id')
            ->where(['o.user_id' => $userId])
            ->groupBy(['oi.food_name'])
            ->orderBy(['qty' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        $topItems = array_map(static fn (array $row): array => [
            'name' => (string) $row['name'],
            'qty' => (int) $row['qty'],
            'revenue' => 'TZS ' . number_format((float) $row['revenue'], 0, '.', ','),
        ], $topRows);

        return [
            'avg_order_value' => round($avgOrder, 0),
            'total_orders' => $totalOrders,
            'cart_count' => (new CartService())->getCount($userId),
            'recent_orders' => $recent,
            'top_items' => $topItems,
        ];
    }

    private function applyDateRange(\yii\db\ActiveQuery $query, string $range): void
    {
        $start = match ($range) {
            'week' => strtotime('monday this week 00:00:00'),
            'month' => strtotime('first day of this month 00:00:00'),
            default => strtotime('today 00:00:00'),
        };

        if ($start !== false) {
            $query->andWhere(['>=', 'created_at', $start]);
        }
    }
}

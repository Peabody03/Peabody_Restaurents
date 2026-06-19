<?php

declare(strict_types=1);

namespace common\services;

use common\models\Food;
use common\models\Order;
use common\models\OrderItem;
use common\models\User;

class DashboardStatsService
{
    public function getStats(string|null $dateFrom = null, string|null $dateTo = null): array
    {
        $from = $dateFrom ? strtotime($dateFrom . ' 00:00:00') : strtotime('today 00:00:00');
        $to = $dateTo ? strtotime($dateTo . ' 23:59:59') : strtotime('today 23:59:59');

        $metrics = $this->aggregateMetrics($from, $to);

        $daySpan = max(1, (int) ceil(($to - $from + 1) / 86400));
        $prevTo = $from - 1;
        $prevFrom = $prevTo - ($daySpan * 86400) + 1;
        $previous = $this->aggregateMetrics($prevFrom, $prevTo);

        return [
            'totalRevenue' => $metrics['revenue'],
            'totalOrders' => $metrics['orders'],
            'itemsSold' => $metrics['items'],
            'avgOrder' => $metrics['orders'] > 0 ? $metrics['revenue'] / $metrics['orders'] : 0,
            'revenueGrowth' => $this->growthPercent($metrics['revenue'], $previous['revenue']),
            'ordersGrowth' => $this->growthPercent($metrics['orders'], $previous['orders']),
            'itemsGrowth' => $this->growthPercent($metrics['items'], $previous['items']),
            'avgGrowth' => $this->growthPercent(
                $metrics['orders'] > 0 ? $metrics['revenue'] / $metrics['orders'] : 0,
                $previous['orders'] > 0 ? $previous['revenue'] / $previous['orders'] : 0,
            ),
        ];
    }

    /**
     * @return array{revenue: float, orders: int, items: int}
     */
    private function aggregateMetrics(int $from, int $to): array
    {
        $query = Order::find()
            ->where(['between', 'created_at', $from, $to])
            ->andWhere(['!=', 'status', Order::STATUS_CANCELLED]);

        $itemsSold = (int) OrderItem::find()
            ->innerJoin('{{%order}}', '{{%order}}.id = {{%order_item}}.order_id')
            ->where(['between', '{{%order}}.created_at', $from, $to])
            ->andWhere(['!=', '{{%order}}.status', Order::STATUS_CANCELLED])
            ->sum('quantity');

        return [
            'revenue' => (float) ((clone $query)->sum('total') ?: 0),
            'orders' => (int) (clone $query)->count(),
            'items' => $itemsSold,
        ];
    }

    private function growthPercent(float|int $current, float|int $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function getRecentOrders(int $limit = 8, string|null $date = null): array
    {
        $query = Order::find()->with(['user'])->orderBy(['created_at' => SORT_DESC]);

        if ($date !== null) {
            $from = strtotime($date . ' 00:00:00');
            $to = strtotime($date . ' 23:59:59');
            $query->andWhere(['between', 'created_at', $from, $to]);
        }

        return $query->limit($limit)->all();
    }

    public function getTopSellingItems(int $limit = 6, string|null $date = null): array
    {
        $query = OrderItem::find()
            ->select(['food_name', 'SUM(quantity) as total_qty', 'SUM(total_price) as total_sales'])
            ->innerJoin('{{%order}}', '{{%order}}.id = {{%order_item}}.order_id')
            ->andWhere(['!=', '{{%order}}.status', Order::STATUS_CANCELLED])
            ->groupBy(['food_name'])
            ->orderBy(['total_qty' => SORT_DESC])
            ->limit($limit);

        if ($date !== null) {
            $from = strtotime($date . ' 00:00:00');
            $to = strtotime($date . ' 23:59:59');
            $query->andWhere(['between', '{{%order}}.created_at', $from, $to]);
        }

        return $query->asArray()->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function getLivePayload(string $date): array
    {
        $stats = $this->getStats($date, $date);
        $recentOrders = $this->getRecentOrders(8, $date);
        $topItems = $this->getTopSellingItems(6, $date);

        return [
            'stats' => $stats,
            'recentOrders' => array_map(static function (Order $order): array {
                return [
                    'id' => $order->id,
                    'orderNumber' => $order->order_number,
                    'customer' => $order->user->username ?? '—',
                    'status' => $order->status,
                    'statusLabel' => Order::statuses()[$order->status] ?? $order->status,
                    'total' => $order->getFormattedTotal(),
                    'time' => \Yii::$app->formatter->asDatetime($order->created_at, 'short'),
                    'viewUrl' => \yii\helpers\Url::to(['order/view', 'id' => $order->id]),
                ];
            }, $recentOrders),
            'topItems' => array_map(static fn (array $row): array => [
                'name' => $row['food_name'],
                'qty' => (int) $row['total_qty'],
                'sales' => (float) $row['total_sales'],
            ], $topItems),
            'updatedAt' => date('H:i:s'),
        ];
    }

    public function getCustomerCount(): int
    {
        return (int) User::find()->where(['role' => User::ROLE_CUSTOMER, 'status' => User::STATUS_ACTIVE])->count();
    }

    public function getFoodCount(): int
    {
        return (int) Food::find()->where(['is_available' => true])->count();
    }
}

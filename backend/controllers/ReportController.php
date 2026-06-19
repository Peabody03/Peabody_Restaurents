<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Order;
use common\services\DashboardStatsService;
use Yii;

class ReportController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $from = Yii::$app->request->get('from', date('Y-m-01'));
        $to = Yii::$app->request->get('to', date('Y-m-d'));
        $statsService = new DashboardStatsService();

        $orders = Order::find()
            ->where(['between', 'created_at', strtotime($from . ' 00:00:00'), strtotime($to . ' 23:59:59')])
            ->andWhere(['!=', 'status', Order::STATUS_CANCELLED])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'stats' => $statsService->getStats($from, $to),
            'topItems' => $statsService->getTopSellingItems(10),
            'orders' => $orders,
            'from' => $from,
            'to' => $to,
        ]);
    }
}

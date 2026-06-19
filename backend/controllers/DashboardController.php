<?php

declare(strict_types=1);

namespace backend\controllers;

use common\services\DashboardStatsService;
use Yii;
use yii\web\Response;

class DashboardController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $statsService = new DashboardStatsService();

        return $this->render('index', [
            'stats' => $statsService->getStats($date, $date),
            'recentOrders' => $statsService->getRecentOrders(8, $date),
            'topItems' => $statsService->getTopSellingItems(6, $date),
            'selectedDate' => $date,
        ]);
    }

    public function actionStatsApi(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $date = Yii::$app->request->get('date', date('Y-m-d'));

        return (new DashboardStatsService())->getStats($date, $date);
    }

    public function actionLiveApi(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $date = Yii::$app->request->get('date', date('Y-m-d'));

        return (new DashboardStatsService())->getLivePayload($date);
    }
}

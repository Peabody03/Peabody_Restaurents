<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Food;
use common\models\Order;
use common\models\User;
use Yii;
use yii\web\Response;

class SearchController extends BaseAdminController
{
    public function actionIndex(): Response
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        if ($q === '') {
            return $this->redirect(['dashboard/index']);
        }

        $orders = Order::find()
            ->with('user')
            ->where(['or', ['like', 'order_number', $q], ['like', 'notes', $q]])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        $customers = User::find()
            ->where(['role' => User::ROLE_CUSTOMER])
            ->andWhere(['or', ['like', 'username', $q], ['like', 'email', $q], ['like', 'phone', $q]])
            ->limit(10)
            ->all();

        $foods = Food::find()
            ->where(['or', ['like', 'food_name', $q], ['like', 'menu_type', $q]])
            ->limit(10)
            ->all();

        return $this->render('index', [
            'q' => $q,
            'orders' => $orders,
            'customers' => $customers,
            'foods' => $foods,
        ]);
    }
}

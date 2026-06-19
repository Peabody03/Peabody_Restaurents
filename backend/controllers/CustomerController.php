<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;

class CustomerController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $query = User::find()->where(['role' => User::ROLE_CUSTOMER])->andWhere(['!=', 'status', User::STATUS_DELETED]);
        $search = trim((string) Yii::$app->request->get('q', ''));
        if ($search !== '') {
            $query->andWhere(['or', ['like', 'username', $search], ['like', 'email', $search], ['like', 'phone', $search]]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider, 'search' => $search]);
    }
}

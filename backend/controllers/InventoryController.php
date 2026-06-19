<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Food;
use Yii;
use yii\data\ActiveDataProvider;

class InventoryController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $query = Food::find();
        $search = trim((string) Yii::$app->request->get('q', ''));
        if ($search !== '') {
            $query->andWhere(['or', ['like', 'food_name', $search], ['like', 'menu_type', $search]]);
        }

        $menuFilter = Yii::$app->request->get('menu_type');
        if ($menuFilter) {
            $query->andWhere(['menu_type' => $menuFilter]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['menu_type' => SORT_ASC, 'food_name' => SORT_ASC]),
            'pagination' => ['pageSize' => 25],
        ]);

        $available = (int) Food::find()->where(['is_available' => true])->count();
        $unavailable = (int) Food::find()->where(['is_available' => false])->count();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'menuTypes' => Food::menuTypes(),
            'search' => $search,
            'available' => $available,
            'unavailable' => $unavailable,
        ]);
    }
}

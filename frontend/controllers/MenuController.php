<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Food;
use Yii;
use yii\web\Controller;

class MenuController extends Controller
{
    use CustomerAppLayoutTrait;

    protected function getCustomerNavKey(): ?string
    {
        return 'menu';
    }

    public function actionIndex(): string
    {
        $counts = [];
        foreach (array_keys(Food::menuTypes()) as $type) {
            $counts[$type] = (int) Food::find()->where(['menu_type' => $type, 'is_available' => true])->count();
        }

        return $this->render('index', [
            'menuTypes' => Food::menuTypes(),
            'counts' => $counts,
        ]);
    }

    public function actionCategory(string $type): string
    {
        if (!isset(Food::menuTypes()[$type])) {
            return $this->redirect(['index']);
        }

        $foods = Food::find()
            ->where(['menu_type' => $type, 'is_available' => true])
            ->orderBy(['food_name' => SORT_ASC])
            ->all();

        return $this->render('category', [
            'type' => $type,
            'label' => Food::menuTypes()[$type],
            'foods' => $foods,
        ]);
    }
}

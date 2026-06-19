<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\services\CartService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class CartController extends Controller
{
    use CustomerAppLayoutTrait;

    protected function getCustomerNavKey(): ?string
    {
        return 'cart';
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['post'],
                    'update' => ['post'],
                    'remove' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $cart = new CartService();
        $userId = Yii::$app->user->id;

        return $this->render('index', [
            'items' => $cart->getItems($userId),
            'subtotal' => $cart->getSubtotal($userId),
            'taxRate' => (float) Yii::$app->params['restaurant.taxRate'],
        ]);
    }

    public function actionAdd(): Response
    {
        $foodId = (int) Yii::$app->request->post('food_id');
        $qty = max(1, (int) Yii::$app->request->post('quantity', 1));
        $cart = new CartService();

        if ($cart->add(Yii::$app->user->id, $foodId, $qty)) {
            Yii::$app->session->setFlash('success', 'Item added to cart.');
        } else {
            Yii::$app->session->setFlash('error', 'Could not add item to cart.');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['menu/index']);
    }

    public function actionUpdate(): Response
    {
        $cart = new CartService();
        $cart->updateQuantity(
            Yii::$app->user->id,
            (int) Yii::$app->request->post('food_id'),
            (int) Yii::$app->request->post('quantity'),
        );

        return $this->redirect(['index']);
    }

    public function actionRemove(): Response
    {
        $cart = new CartService();
        $cart->remove(Yii::$app->user->id, (int) Yii::$app->request->post('food_id'));

        return $this->redirect(['index']);
    }
}

<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Order;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $query = Order::find()->with('user');
        $status = Yii::$app->request->get('status');
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 15],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'statuses' => Order::statuses(),
        ]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionUpdateStatus(int $id): Response
    {
        $model = $this->findModel($id);
        $status = Yii::$app->request->post('status');
        if ($status && isset(Order::statuses()[$status])) {
            $model->status = $status;
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Order status updated.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    private function findModel(int $id): Order
    {
        $model = Order::find()->where(['id' => $id])->with(['items', 'user'])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Order not found.');
        }

        return $model;
    }
}

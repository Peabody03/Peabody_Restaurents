<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Reservation;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ReservationController extends BaseAdminController
{
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Reservation::find()->orderBy(['reservation_date' => SORT_DESC, 'reservation_time' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionUpdateStatus(int $id): Response
    {
        $model = $this->findModel($id);
        $status = Yii::$app->request->post('status');
        if (in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
            $model->status = $status;
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Reservation status updated.');
        }

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Reservation
    {
        $model = Reservation::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Reservation not found.');
        }

        return $model;
    }
}

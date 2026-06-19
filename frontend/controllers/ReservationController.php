<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Reservation;
use frontend\models\ReservationForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class ReservationController extends Controller
{
    use CustomerAppLayoutTrait;

    protected function getCustomerNavKey(): ?string
    {
        return 'menu';
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'my-reservations'],
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionCreate(): string|Response
    {
        $model = new ReservationForm();
        $user = Yii::$app->user->identity;
        $model->guest_name = $user->username;
        $model->guest_email = $user->email;
        $model->guest_phone = $user->phone;

        if ($model->load(Yii::$app->request->post()) && $model->save($user->id)) {
            Yii::$app->session->setFlash('success', 'Your table reservation has been submitted. We will confirm shortly.');

            return $this->redirect(['my-reservations']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionMyReservations(): string
    {
        $reservations = Reservation::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['reservation_date' => SORT_DESC, 'reservation_time' => SORT_DESC])
            ->all();

        return $this->render('my-reservations', ['reservations' => $reservations]);
    }
}

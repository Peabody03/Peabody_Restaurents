<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\models\AccountSettingsForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * Account controller
 */
class AccountController extends Controller
{
    use CustomerAppLayoutTrait;

    protected function getCustomerNavKey(): ?string
    {
        return 'settings';
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionSettings(): string|Response
    {
        $user = Yii::$app->user->identity;
        $model = new AccountSettingsForm($user);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Your account settings have been updated successfully.');

            return $this->refresh();
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }
}

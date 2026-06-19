<?php

declare(strict_types=1);

namespace backend\controllers;

use Yii;
use yii\web\Response;

class SettingsController extends BaseAdminController
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionSave(): Response
    {
        Yii::$app->session->setFlash('success', 'Settings saved successfully.');

        return $this->redirect(['index']);
    }
}

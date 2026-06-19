<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\LoginForm;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['actions' => ['login', 'error'], 'allow' => true],
                    ['actions' => ['logout', 'index'], 'allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['logout' => ['post']],
            ],
        ];
    }

    public function actions(): array
    {
        return ['error' => ['class' => ErrorAction::class]];
    }

    public function actionIndex(): Response
    {
        return $this->redirect(['dashboard/index']);
    }

    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            if ($user->isAdmin()) {
                return $this->redirect(['dashboard/index']);
            }
            Yii::$app->user->logout();
        }

        $this->layout = 'blank';
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            /** @var User $user */
            $user = Yii::$app->user->identity;
            if (!$user->isAdmin()) {
                Yii::$app->user->logout();
                $model->addError('username', 'Access denied. Admin credentials required.');

                return $this->render('login', ['model' => $model]);
            }

            return $this->redirect(['dashboard/index']);
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(['site/login']);
    }
}

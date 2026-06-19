<?php

declare(strict_types=1);

namespace backend\filters;

use common\models\User;
use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Restricts backend access to admin users only.
 */
class AdminAccessFilter extends ActionFilter
{
    public function beforeAction($action): bool
    {
        if (Yii::$app->user->isGuest) {
            return parent::beforeAction($action);
        }

        /** @var User|null $user */
        $user = Yii::$app->user->identity;
        if ($user !== null && $user->isAdmin()) {
            return parent::beforeAction($action);
        }

        Yii::$app->user->logout();
        Yii::$app->session->setFlash('error', 'Access denied. Admin credentials required.');

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 403;
            Yii::$app->response->data = ['error' => 'Forbidden'];

            return false;
        }

        throw new ForbiddenHttpException('You are not authorized to access the admin panel.');
    }
}

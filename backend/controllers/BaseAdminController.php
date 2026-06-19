<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\filters\AdminAccessFilter;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

abstract class BaseAdminController extends Controller
{
    public $layout = 'admin';

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'admin' => [
                'class' => AdminAccessFilter::class,
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace frontend\controllers;

use Yii;

/**
 * Applies the customer sidebar layout for logged-in non-admin users.
 */
trait CustomerAppLayoutTrait
{
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->applyCustomerAppLayout();

        return true;
    }

    protected function getCustomerNavKey(): ?string
    {
        return null;
    }

    protected function applyCustomerAppLayout(): void
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $user = Yii::$app->user->identity;
        if ($user !== null && method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return;
        }

        $this->layout = 'customer-app';
        $navKey = $this->getCustomerNavKey();
        if ($navKey !== null) {
            $this->view->params['customerNav'] = $navKey;
        }
    }
}

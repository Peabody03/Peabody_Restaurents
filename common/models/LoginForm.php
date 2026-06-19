<?php

declare(strict_types=1);

namespace common\models;

use common\components\LoginRateLimiter;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = true;
    private User|null $_user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['username', 'validateAccountStatus'],
            ['password', 'validatePassword'],
        ];
    }

    public function validateAccountStatus(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $rateLimiter = LoginRateLimiter::create();
        $ip = Yii::$app->request->userIP ?? 'unknown';

        if ($rateLimiter->isBlocked($this->username, $ip)) {
            $seconds = $rateLimiter->getRemainingLockoutSeconds($this->username, $ip);
            $minutes = (int) ceil($seconds / 60);
            $this->addError(
                $attribute,
                "Too many failed login attempts. Please try again in about {$minutes} minute(s).",
            );
        }
    }

    public function validatePassword(string $attribute, array|null $params): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        $ip = Yii::$app->request->userIP ?? 'unknown';
        $rateLimiter = LoginRateLimiter::create();

        if ($user === null || !$user->validatePassword($this->password)) {
            $rateLimiter->recordFailure($this->username, $ip);
            $this->addError($attribute, 'Incorrect username or password.');

            return;
        }

        $rateLimiter->clear($this->username, $ip);
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }

        return false;
    }

    protected function getUser(): User|null
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}

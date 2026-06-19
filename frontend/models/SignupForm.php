<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\validators\StrongPasswordValidator;
use Yii;
use yii\base\Model;

/**
 * Signup form — instant activation, no verification required.
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            [['username', 'email', 'phone', 'password'], 'required'],
            ['username', 'trim'],
            ['username', 'unique', 'targetClass' => User::class, 'filter' => ['!=', 'status', User::STATUS_DELETED], 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username may only contain letters, numbers, and underscores.'],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => ['!=', 'status', User::STATUS_DELETED], 'message' => 'This email address has already been registered.'],

            ['phone', 'trim'],
            ['phone', 'match', 'pattern' => '/^\+?[0-9\s\-()]{7,20}$/', 'message' => 'Please enter a valid phone number.'],
            ['phone', 'unique', 'targetClass' => User::class, 'filter' => ['!=', 'status', User::STATUS_DELETED], 'message' => 'This phone number has already been registered.'],

            ['password', StrongPasswordValidator::class, 'minLength' => (int) Yii::$app->params['user.passwordMinLength']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'password' => 'Password',
        ];
    }

    public function signup(): User|null
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->status = User::STATUS_ACTIVE;
        $user->role = User::ROLE_CUSTOMER;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}

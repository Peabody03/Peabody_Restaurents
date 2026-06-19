<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use common\validators\StrongPasswordValidator;
use Yii;
use yii\base\Model;

/**
 * Account settings form
 */
class AccountSettingsForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirm = '';

    private User|null $_user = null;

    public function __construct(User $user, array $config = [])
    {
        $this->_user = $user;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['username', 'email', 'phone'], 'required'],
            ['username', 'trim'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username may only contain letters, numbers, and underscores.'],
            ['username', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                $query->andWhere(['!=', 'id', $this->_user->id])->andWhere(['!=', 'status', User::STATUS_DELETED]);
            }, 'message' => 'This username has already been taken.'],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => function ($query) {
                $query->andWhere(['!=', 'id', $this->_user->id])->andWhere(['!=', 'status', User::STATUS_DELETED]);
            }, 'message' => 'This email address has already been registered.'],

            ['phone', 'trim'],
            ['phone', 'match', 'pattern' => '/^\+?[0-9\s\-()]{7,20}$/', 'message' => 'Please enter a valid phone number.'],

            [['currentPassword', 'newPassword', 'newPasswordConfirm'], 'string'],
            ['newPassword', StrongPasswordValidator::class, 'minLength' => (int) Yii::$app->params['user.passwordMinLength'], 'skipOnEmpty' => true],
            ['newPasswordConfirm', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Passwords do not match.', 'skipOnEmpty' => true],
            ['currentPassword', 'validatePasswordChange'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'currentPassword' => 'Current Password',
            'newPassword' => 'New Password',
            'newPasswordConfirm' => 'Confirm New Password',
        ];
    }

    public function validatePasswordChange(string $attribute): void
    {
        if ($this->newPassword === '' && $this->newPasswordConfirm === '') {
            return;
        }

        if ($this->currentPassword === '') {
            $this->addError($attribute, 'Current password is required to set a new password.');

            return;
        }

        if (!$this->_user->validatePassword($this->currentPassword)) {
            $this->addError($attribute, 'Current password is incorrect.');
        }
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;

        if ($this->newPassword !== '') {
            $user->setPassword($this->newPassword);
            $user->generateAuthKey();
        }

        return $user->save();
    }
}

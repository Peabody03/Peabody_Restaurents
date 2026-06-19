<?php

declare(strict_types=1);

namespace common\validators;

use yii\validators\Validator;

/**
 * Validates password strength requirements.
 */
class StrongPasswordValidator extends Validator
{
    public int $minLength = 8;

    public function validateAttribute($model, $attribute): void
    {
        $value = (string) $model->$attribute;

        if (strlen($value) < $this->minLength) {
            $this->addError(
                $model,
                $attribute,
                'Password must be at least {min} characters long.',
                ['min' => $this->minLength],
            );

            return;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->addError($model, $attribute, 'Password must contain at least one lowercase letter.');

            return;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->addError($model, $attribute, 'Password must contain at least one uppercase letter.');

            return;
        }

        if (!preg_match('/\d/', $value)) {
            $this->addError($model, $attribute, 'Password must contain at least one number.');

            return;
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $value)) {
            $this->addError($model, $attribute, 'Password must contain at least one special character.');
        }
    }
}

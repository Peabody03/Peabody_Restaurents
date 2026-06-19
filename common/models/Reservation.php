<?php

declare(strict_types=1);

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $guest_name
 * @property string $guest_email
 * @property string $guest_phone
 * @property int $guests
 * @property string $reservation_date
 * @property string $reservation_time
 * @property string $status
 * @property string|null $notes
 * @property int $created_at
 * @property int $updated_at
 */
class Reservation extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%reservation}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['guest_name', 'guest_email', 'guest_phone', 'guests', 'reservation_date', 'reservation_time'], 'required'],
            ['guest_email', 'email'],
            ['guests', 'integer', 'min' => 1, 'max' => 50],
            ['status', 'in', 'range' => ['pending', 'confirmed', 'cancelled', 'completed']],
            ['notes', 'string'],
        ];
    }
}

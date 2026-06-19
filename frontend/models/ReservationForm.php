<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Reservation;
use yii\base\Model;

class ReservationForm extends Model
{
    public string $guest_name = '';
    public string $guest_email = '';
    public string $guest_phone = '';
    public int $guests = 2;
    public string $reservation_date = '';
    public string $reservation_time = '';
    public string $notes = '';

    public function rules(): array
    {
        return [
            [['guest_name', 'guest_email', 'guest_phone', 'guests', 'reservation_date', 'reservation_time'], 'required'],
            ['guest_email', 'email'],
            ['guest_phone', 'match', 'pattern' => '/^\+?[0-9\s\-()]{7,20}$/'],
            ['guests', 'integer', 'min' => 1, 'max' => 50],
            ['reservation_date', 'date', 'format' => 'php:Y-m-d'],
            ['reservation_date', 'validateFutureDate'],
            ['reservation_time', 'match', 'pattern' => '/^\d{2}:\d{2}(:\d{2})?$/'],
            ['notes', 'string', 'max' => 500],
        ];
    }

    public function validateFutureDate(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }
        if (strtotime($this->reservation_date) < strtotime('today')) {
            $this->addError($attribute, 'Reservation date cannot be in the past.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'guest_name' => 'Full Name',
            'guest_email' => 'Email',
            'guest_phone' => 'Phone',
            'guests' => 'Number of Guests',
            'reservation_date' => 'Date',
            'reservation_time' => 'Time',
            'notes' => 'Special Requests',
        ];
    }

    public function save(int|null $userId = null): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new Reservation();
        $model->user_id = $userId;
        $model->guest_name = $this->guest_name;
        $model->guest_email = $this->guest_email;
        $model->guest_phone = $this->guest_phone;
        $model->guests = $this->guests;
        $model->reservation_date = $this->reservation_date;
        $model->reservation_time = $this->reservation_time;
        $model->notes = $this->notes;
        $model->status = 'pending';

        return $model->save();
    }
}

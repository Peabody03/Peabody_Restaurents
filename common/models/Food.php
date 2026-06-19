<?php

declare(strict_types=1);

namespace common\models;

use common\services\ImageUploadService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $food_name
 * @property string $menu_type
 * @property float $price
 * @property string|null $image
 * @property string|null $description
 * @property bool $is_available
 * @property int $created_at
 * @property int $updated_at
 */
class Food extends ActiveRecord
{
    public const MENU_BREAKFAST = 'breakfast';
    public const MENU_LUNCH = 'lunch';
    public const MENU_DINNER = 'dinner';
    public const MENU_BITS = 'bits';

    public static function tableName(): string
    {
        return '{{%food}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['food_name', 'menu_type', 'price'], 'required'],
            ['food_name', 'string', 'max' => 255],
            ['menu_type', 'in', 'range' => array_keys(self::menuTypes())],
            ['price', 'number', 'min' => 0],
            ['image', 'string', 'max' => 500],
            ['description', 'string'],
            ['is_available', 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'food_name' => 'Food Name',
            'menu_type' => 'Menu Category',
            'price' => 'Price (TZS)',
            'image' => 'Image',
            'description' => 'Description',
            'is_available' => 'Available',
        ];
    }

    public static function menuTypes(): array
    {
        return [
            self::MENU_BREAKFAST => 'Breakfast',
            self::MENU_LUNCH => 'Lunch',
            self::MENU_DINNER => 'Dinner',
            self::MENU_BITS => 'Bits',
        ];
    }

    public function getImageUrl(): string
    {
        $service = new ImageUploadService();
        $placeholder = $service->getPublicUrl('foods/placeholder.svg');

        if ($this->image === null || $this->image === '') {
            return $placeholder;
        }

        $path = Yii::getAlias('@frontend/web/uploads/' . ltrim($this->image, '/'));
        if (is_file($path)) {
            return $service->getPublicUrl($this->image);
        }

        return $placeholder;
    }

    public function getFormattedPrice(): string
    {
        return 'TZS ' . number_format((float) $this->price, 0, '.', ',');
    }
}

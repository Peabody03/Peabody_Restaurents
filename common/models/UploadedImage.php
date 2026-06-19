<?php

declare(strict_types=1);

namespace common\models;

use common\services\ImageUploadService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $filename
 * @property string $original_name
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property int|null $uploaded_by
 * @property int $created_at
 */
class UploadedImage extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%uploaded_image}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['filename', 'original_name', 'path', 'mime_type', 'size'], 'required'],
            [['size', 'width', 'height', 'uploaded_by'], 'integer'],
        ];
    }

    public function getPublicUrl(): string
    {
        return (new ImageUploadService())->getPublicUrl($this->path);
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}

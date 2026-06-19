<?php

declare(strict_types=1);

namespace common\services;

use common\models\UploadedImage;
use Yii;
use yii\web\UploadedFile;

/**
 * Handles secure image upload, resize, and optimization.
 */
class ImageUploadService
{
    private const MAX_SIZE = 5242880; // 5MB
    private const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    private const MAX_WIDTH = 1200;
    private const MAX_HEIGHT = 1200;
    private const JPEG_QUALITY = 85;

    public function getUploadPath(): string
    {
        return Yii::getAlias('@frontend/web/uploads');
    }

    public function getPublicUrl(string $relativePath): string
    {
        return $this->resolveUploadsBaseUrl() . '/' . ltrim($relativePath, '/');
    }

    public function resolveUploadsBaseUrl(): string
    {
        if (!empty(Yii::$app->params['uploads.publicBaseUrl'])) {
            $configured = Yii::$app->params['uploads.publicBaseUrl'];
            if (is_string($configured) && str_starts_with($configured, '@')) {
                return rtrim((string) Yii::getAlias($configured), '/');
            }

            return rtrim((string) $configured, '/');
        }

        $base = Yii::$app->params['uploads.baseUrl'] ?? '/uploads';
        if ($base === '@web/uploads') {
            return $this->resolveSharedUploadsBaseUrl();
        }

        if (is_string($base) && str_starts_with($base, '@')) {
            return rtrim((string) Yii::getAlias($base), '/');
        }

        return rtrim((string) $base, '/');
    }

    /**
     * Files are stored under @frontend/web/uploads; always expose that public URL.
     */
    private function resolveSharedUploadsBaseUrl(): string
    {
        if (Yii::$app->has('request') && Yii::$app->request instanceof \yii\web\Request) {
            $baseUrl = rtrim((string) Yii::$app->request->baseUrl, '/');
            if ($baseUrl !== '') {
                if (str_ends_with($baseUrl, '/backend/web')) {
                    $baseUrl = (string) preg_replace('#/backend/web$#', '/frontend/web', $baseUrl);
                }

                return $baseUrl . '/uploads';
            }
        }

        $frontendWeb = realpath(Yii::getAlias('@frontend/web'));
        $docRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
        if ($frontendWeb !== false && $docRoot !== false && str_starts_with($frontendWeb, $docRoot)) {
            $relative = str_replace('\\', '/', substr($frontendWeb, strlen($docRoot)));

            return rtrim($relative, '/') . '/uploads';
        }

        return '/frontend/web/uploads';
    }

    /**
     * @return array{success: bool, model?: UploadedImage, error?: string}
     */
    public function upload(UploadedFile $file, int|null $uploadedBy = null, string $subdir = 'gallery'): array
    {
        if (!$file->hasError) {
            if ($file->size > self::MAX_SIZE) {
                return ['success' => false, 'error' => 'File size must not exceed 5 MB.'];
            }

            $mime = @mime_content_type($file->tempName) ?: $file->type;
            if ($mime === '' || $mime === 'application/octet-stream') {
                $mime = match (strtolower($file->extension)) {
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    default => 'image/jpeg',
                };
            }
            if (!in_array($mime, self::ALLOWED, true)) {
                return ['success' => false, 'error' => 'Only JPG, PNG, WEBP, and GIF images are allowed.'];
            }
        } else {
            return ['success' => false, 'error' => 'Upload failed. Please try again.'];
        }

        $extension = strtolower($file->extension);
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return ['success' => false, 'error' => 'Invalid file extension.'];
        }

        $filename = Yii::$app->security->generateRandomString(24) . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
        $relativeDir = trim($subdir, '/');
        $absoluteDir = $this->getUploadPath() . '/' . $relativeDir;

        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0755, true) && !is_dir($absoluteDir)) {
            return ['success' => false, 'error' => 'Could not create upload directory.'];
        }

        $absolutePath = $absoluteDir . '/' . $filename;
        $relativePath = $relativeDir . '/' . $filename;

        if (!$file->saveAs($absolutePath)) {
            return ['success' => false, 'error' => 'Failed to save uploaded file.'];
        }

        [$width, $height] = $this->optimizeImage($absolutePath, $mime);

        $model = new UploadedImage();
        $model->filename = $filename;
        $model->original_name = $file->name;
        $model->path = $relativePath;
        $model->mime_type = $mime;
        $model->size = (int) filesize($absolutePath);
        $model->width = $width;
        $model->height = $height;
        $model->uploaded_by = $uploadedBy;

        if (!$model->save()) {
            @unlink($absolutePath);

            return ['success' => false, 'error' => 'Failed to save image record.'];
        }

        return ['success' => true, 'model' => $model];
    }

    public function delete(UploadedImage $model): bool
    {
        $absolutePath = $this->getUploadPath() . '/' . $model->path;
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }

        return (bool) $model->delete();
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function optimizeImage(string $path, string $mime): array
    {
        if (!extension_loaded('gd')) {
            $info = @getimagesize($path);

            return [$info[0] ?? null, $info[1] ?? null];
        }

        try {
            return $this->optimizeWithGd($path, $mime);
        } catch (\Throwable) {
            $info = @getimagesize($path);

            return [$info[0] ?? null, $info[1] ?? null];
        }
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function optimizeWithGd(string $path, string $mime): array
    {
        $info = @getimagesize($path);
        if ($info === false) {
            return [null, null];
        }

        [$width, $height] = $info;
        if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
            return [$width, $height];
        }

        $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
        $newW = (int) round($width * $ratio);
        $newH = (int) round($height * $ratio);

        $src = match ($mime) {
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => @imagecreatefromjpeg($path),
        };

        if ($src === false) {
            return [$width, $height];
        }

        $dst = imagecreatetruecolor($newW, $newH);
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

        match ($mime) {
            'image/png' => imagepng($dst, $path, 8),
            'image/gif' => imagegif($dst, $path),
            'image/webp' => function_exists('imagewebp') ? imagewebp($dst, $path, self::JPEG_QUALITY) : imagejpeg($dst, $path, self::JPEG_QUALITY),
            default => imagejpeg($dst, $path, self::JPEG_QUALITY),
        };

        imagedestroy($src);
        imagedestroy($dst);

        return [$newW, $newH];
    }
}

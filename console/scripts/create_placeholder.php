<?php

declare(strict_types=1);

$dir = __DIR__ . '/../../frontend/web/uploads/foods';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$img = imagecreatetruecolor(400, 300);
$bg = imagecolorallocate($img, 241, 245, 249);
$fg = imagecolorallocate($img, 249, 115, 22);
imagefill($img, 0, 0, $bg);
imagestring($img, 5, 155, 140, 'Food', $fg);
imagejpeg($img, $dir . '/placeholder.jpg', 85);
imagedestroy($img);

echo "Placeholder created at {$dir}/placeholder.jpg\n";

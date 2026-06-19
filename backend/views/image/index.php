<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $search */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Image Gallery';
$uploadUrl = Url::to(['image/upload']);
?>
<h1 class="page-title mb-4"><?= Html::encode($this->title) ?></h1>

<div class="admin-panel p-4 mb-4">
    <div class="upload-zone" id="gallery-upload-zone">
        <i class="bi bi-cloud-arrow-up fs-1"></i>
        <h2 class="h6 fw-bold">Upload Image</h2>
        <p class="text-muted mb-2">Drag & drop or click to select — JPG, PNG, WEBP, GIF up to 5 MB</p>
        <button type="button" class="btn btn-admin" id="gallery-upload-btn">Choose File</button>
        <input type="file" id="gallery-file" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
    </div>
    <img src="" id="gallery-preview" class="upload-preview d-none" alt="">
    <div class="progress upload-progress d-none" id="gallery-progress">
        <div class="progress-bar bg-warning" id="gallery-progress-bar" style="width:0%"></div>
    </div>
    <div id="gallery-upload-msg" class="mt-2"></div>
</div>

<form class="mb-3" method="get">
    <input type="search" name="q" value="<?= Html::encode($search) ?>" class="form-control" placeholder="Search images..." style="max-width:320px">
</form>

<div class="gallery-grid">
    <?php foreach ($dataProvider->getModels() as $image): ?>
        <div class="gallery-item">
            <img src="<?= Html::encode($image->getPublicUrl()) ?>" alt="<?= Html::encode($image->original_name) ?>">
            <div class="gallery-meta">
                <div class="text-truncate"><?= Html::encode($image->original_name) ?></div>
                <div class="text-muted"><?= $image->getFormattedSize() ?></div>
                <?= Html::a('Delete', ['delete', 'id' => $image->id], [
                    'class' => 'btn btn-sm btn-outline-danger mt-1',
                    'data' => ['method' => 'post', 'confirm' => 'Delete this image?'],
                ]) ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-3"><?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?></div>

<?php
$this->registerJs(<<<JS
(function() {
    const zone = document.getElementById('gallery-upload-zone');
    const input = document.getElementById('gallery-file');
    const btn = document.getElementById('gallery-upload-btn');
    const progress = document.getElementById('gallery-progress');
    const bar = document.getElementById('gallery-progress-bar');
    const msg = document.getElementById('gallery-upload-msg');
    const csrfParam = document.querySelector('meta[name="csrf-param"]').content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function upload(file) {
        const fd = new FormData();
        fd.append('image', file);
        fd.append(csrfParam, csrfToken);
        progress.classList.remove('d-none');
        bar.style.width = '30%';
        fetch('{$uploadUrl}', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                bar.style.width = '100%';
                if (data.success) {
                    msg.innerHTML = '<div class="alert alert-success">Uploaded: ' + data.name + '</div>';
                    setTimeout(() => location.reload(), 800);
                } else {
                    msg.innerHTML = '<div class="alert alert-danger">' + (data.error || 'Upload failed') + '</div>';
                }
            }).catch(() => {
                msg.innerHTML = '<div class="alert alert-danger">Upload failed.</div>';
            }).finally(() => {
                setTimeout(() => { progress.classList.add('d-none'); bar.style.width = '0'; }, 1000);
            });
    }

    btn.addEventListener('click', () => input.click());
    zone.addEventListener('click', (e) => { if (e.target === zone || e.target.closest('.upload-zone')) input.click(); });
    zone.addEventListener('dragover', (e) => { e.preventDefault(); zone.classList.add('dragover'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
    zone.addEventListener('drop', (e) => {
        e.preventDefault(); zone.classList.remove('dragover');
        Array.from(e.dataTransfer.files).forEach(upload);
    });
    input.addEventListener('change', () => Array.from(input.files).forEach(upload));
})();
JS);

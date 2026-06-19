<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Food $model */
/** @var \common\models\UploadedImage[] $galleryImages */

use common\models\Food;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->isNewRecord ? 'Add Food Item' : 'Edit Food Item';
$hasImage = !$model->isNewRecord && $model->image;
$currentUrl = $hasImage ? $model->getImageUrl() : '';
?>
<div class="admin-panel p-4" style="max-width:800px">
    <h1 class="h4 fw-bold mb-4"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'food-form']]); ?>

    <?= $form->field($model, 'food_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'menu_type')->dropDownList(Food::menuTypes(), ['prompt' => 'Select category…']) ?>
    <?= $form->field($model, 'price')->input('number', ['step' => '0.01', 'min' => 0]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'is_available')->checkbox() ?>

    <div class="mb-4">
        <label class="form-label fw-semibold d-block">Food Photo</label>
        <?= Html::error($model, 'image', ['class' => 'text-danger small d-block mb-2']) ?>

        <div class="food-image-preview-wrap mb-3<?= $hasImage ? '' : ' d-none' ?>" id="preview-wrap">
            <img src="<?= Html::encode($currentUrl) ?>" class="upload-preview food-photo-preview" id="image-preview" alt="Food preview">
            <div class="d-flex gap-2 mt-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-danger" id="remove-preview">Remove Photo</button>
                <?php if ($hasImage): ?>
                    <span class="text-muted small align-self-center">Current photo will be replaced if you upload or pick another.</span>
                <?php endif ?>
            </div>
        </div>

        <input type="hidden" name="existingImagePath" id="existing-image-path" value="">
        <input type="hidden" name="removeImage" id="remove-image-flag" value="0">

        <div class="upload-zone" id="upload-zone">
            <i class="bi bi-camera fs-1 text-muted"></i>
            <p class="mb-1 fw-semibold">Upload a new photo</p>
            <p class="mb-0 small text-muted">Drag & drop here or click to browse — JPG, PNG, WEBP, GIF (max 5 MB)</p>
            <input type="file" name="imageFile" id="image-file" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>

        <?php if ($galleryImages !== []): ?>
            <div class="mt-4">
                <p class="fw-semibold mb-2">Or choose from gallery</p>
                <div class="food-gallery-picker">
                    <?php foreach ($galleryImages as $image): ?>
                        <button type="button"
                                class="food-gallery-thumb"
                                data-path="<?= Html::encode($image->path) ?>"
                                data-url="<?= Html::encode($image->getPublicUrl()) ?>"
                                title="<?= Html::encode($image->original_name) ?>">
                            <img src="<?= Html::encode($image->getPublicUrl()) ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
                <p class="small text-muted mt-2 mb-0">
                    <?= Html::a('Manage all images →', ['/image/index'], ['class' => 'text-decoration-none']) ?>
                </p>
            </div>
        <?php else: ?>
            <p class="small text-muted mt-3 mb-0">
                No gallery images yet.
                <?= Html::a('Upload images to gallery', ['/image/index']) ?>
            </p>
        <?php endif ?>
    </div>

    <div class="d-flex gap-2">
        <?= Html::submitButton('Save Food', ['class' => 'btn btn-admin']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerCss(<<<CSS
.food-image-preview-wrap {
    max-width: 360px;
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid #e8ecf1;
    background: #f8f9fb;
}
.food-photo-preview {
    display: block;
    width: 100%;
    height: 220px;
    max-height: 220px;
    object-fit: cover;
    object-position: center;
}
.food-gallery-picker {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
    gap: 0.5rem;
    max-width: 100%;
}
.food-gallery-thumb {
    border: 2px solid transparent;
    border-radius: 10px;
    padding: 0;
    width: 100%;
    aspect-ratio: 1;
    overflow: hidden;
    cursor: pointer;
    background: #f1f5f9;
    transition: border-color .2s, transform .2s;
}
.food-gallery-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.food-gallery-thumb:hover, .food-gallery-thumb.selected {
    border-color: var(--admin-orange, #FF6B00);
    transform: scale(1.03);
}
CSS);

$this->registerJs(<<<JS
(function () {
    const zone = document.getElementById('upload-zone');
    const input = document.getElementById('image-file');
    const preview = document.getElementById('image-preview');
    const previewWrap = document.getElementById('preview-wrap');
    const removeBtn = document.getElementById('remove-preview');
    const existingPath = document.getElementById('existing-image-path');
    const removeFlag = document.getElementById('remove-image-flag');
    const thumbs = document.querySelectorAll('.food-gallery-thumb');

    function showPreview(url) {
        preview.src = url;
        previewWrap.classList.remove('d-none');
        removeFlag.value = '0';
    }

    function clearSelection() {
        if (input) input.value = '';
        existingPath.value = '';
        preview.src = '';
        previewWrap.classList.add('d-none');
        removeFlag.value = '1';
        thumbs.forEach(t => t.classList.remove('selected'));
    }

    if (zone && input) {
        zone.addEventListener('click', () => input.click());
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
            if (e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                showPreview(URL.createObjectURL(e.dataTransfer.files[0]));
                existingPath.value = '';
                thumbs.forEach(t => t.classList.remove('selected'));
            }
        });
        input.addEventListener('change', () => {
            if (input.files[0]) {
                showPreview(URL.createObjectURL(input.files[0]));
                existingPath.value = '';
                thumbs.forEach(t => t.classList.remove('selected'));
            }
        });
    }

    removeBtn?.addEventListener('click', clearSelection);

    thumbs.forEach(btn => {
        btn.addEventListener('click', () => {
            thumbs.forEach(t => t.classList.remove('selected'));
            btn.classList.add('selected');
            existingPath.value = btn.dataset.path;
            if (input) input.value = '';
            showPreview(btn.dataset.url);
        });
    });
})();
JS);
?>

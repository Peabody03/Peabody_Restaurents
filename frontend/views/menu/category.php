<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $type */
/** @var string $label */
/** @var \common\models\Food[] $foods */

use yii\helpers\Html;
use frontend\assets\MenuAsset;

MenuAsset::register($this);

$this->title = $label . ' Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menu', 'url' => ['menu/index']];
$this->params['breadcrumbs'][] = $label;
?>
<div class="menu-category-page animate-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h2 fw-bold mb-1"><?= Html::encode($label) ?></h1>
            <p class="text-muted mb-0"><?= count($foods) ?> delicious options</p>
        </div>
        <?= Html::a('&larr; All Menus', ['menu/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="menu-food-list">
        <?php foreach ($foods as $food): ?>
            <article class="menu-food-item"
                     data-food-id="<?= (int) $food->id ?>"
                     data-food-name="<?= Html::encode($food->food_name) ?>"
                     data-food-price="<?= Html::encode($food->getFormattedPrice()) ?>"
                     data-food-desc="<?= Html::encode($food->description ?? 'No description available.') ?>"
                     data-food-image="<?= Html::encode($food->getImageUrl()) ?>"
                     data-food-category="<?= Html::encode($label) ?>">
                <div class="menu-food-item__thumb">
                    <img
                        src="<?= Html::encode($food->getImageUrl()) ?>"
                        alt="<?= Html::encode($food->food_name) ?>"
                        loading="lazy"
                    >
                </div>
                <div class="menu-food-item__info">
                    <h2 class="menu-food-item__name"><?= Html::encode($food->food_name) ?></h2>
                    <p class="menu-food-item__price"><?= $food->getFormattedPrice() ?></p>
                    <p class="menu-food-item__desc"><?= Html::encode(mb_strimwidth($food->description ?? '', 0, 80, '…')) ?></p>
                    <button type="button" class="btn btn-menu-view-outline w-100 js-food-view">View</button>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a('Login to Order', ['site/login'], ['class' => 'btn btn-menu-view w-100']) ?>
                    <?php else: ?>
                        <?= Html::beginForm(['cart/add'], 'post', ['class' => 'menu-food-item__action'])
                            . Html::hiddenInput('food_id', $food->id)
                            . Html::submitButton('Add to Cart', ['class' => 'btn btn-menu-view w-100'])
                            . Html::endForm() ?>
                    <?php endif ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<!-- Food detail modal -->
<div class="modal fade" id="foodViewModal" tabindex="-1" aria-labelledby="foodViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content menu-food-modal">
            <button type="button" class="menu-food-modal__close" data-bs-dismiss="modal" aria-label="Close">&times;</button>

            <div class="menu-food-modal__hero">
                <img src="" alt="" id="foodViewModalImage" class="menu-food-modal__img">
                <div class="menu-food-modal__hero-overlay"></div>
                <span class="menu-food-modal__badge" id="foodViewModalCategory"></span>
            </div>

            <div class="menu-food-modal__body">
                <p class="menu-food-modal__eyebrow">Food Details</p>
                <h2 class="menu-food-modal__title" id="foodViewModalLabel"></h2>
                <p class="menu-food-modal__price" id="foodViewModalPrice"></p>
                <div class="menu-food-modal__divider"></div>
                <p class="menu-food-modal__desc" id="foodViewModalDesc"></p>

                <div class="menu-food-modal__actions">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a('Login to Order', ['site/login'], ['class' => 'btn btn-menu-view w-100']) ?>
                    <?php else: ?>
                        <?= Html::beginForm(['cart/add'], 'post', ['id' => 'foodViewModalCartForm', 'class' => 'w-100'])
                            . Html::hiddenInput('food_id', '', ['id' => 'foodViewModalFoodId'])
                            . Html::submitButton('Add to Cart', ['class' => 'btn btn-menu-view w-100'])
                            . Html::endForm() ?>
                    <?php endif ?>
                    <button type="button" class="btn btn-menu-view-outline w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

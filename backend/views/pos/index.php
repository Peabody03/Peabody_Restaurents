<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Food[] $foods */

use common\helpers\PaymentMethod;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Point of Sale';
$taxRate = (float) Yii::$app->params['restaurant.taxRate'] * 100;
?>
<div class="pos-layout">
    <div class="pos-products">
        <div class="pos-search-bar mb-3">
            <i class="bi bi-search"></i>
            <input type="search" id="pos-search" class="form-control" placeholder="Search products or scan barcode..." autofocus>
        </div>
        <div class="pos-product-grid" id="pos-product-grid">
            <?php foreach ($foods as $food): ?>
                <button type="button" class="pos-product-card" data-id="<?= $food->id ?>" data-name="<?= Html::encode($food->food_name) ?>" data-price="<?= (float) $food->price ?>">
                    <div class="pos-product-img" style="background-image:url('<?= Html::encode($food->getImageUrl()) ?>')"></div>
                    <div class="pos-product-info">
                        <strong><?= Html::encode($food->food_name) ?></strong>
                        <span><?= $food->getFormattedPrice() ?></span>
                    </div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pos-cart-panel">
        <h2 class="h5 fw-bold mb-3">Current Sale</h2>
        <div id="pos-cart-items" class="pos-cart-items mb-3"></div>
        <div class="pos-totals mb-3">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span id="pos-subtotal">TZS 0</span></div>
            <div class="d-flex justify-content-between"><span>Tax (<?= $taxRate ?>%)</span><span id="pos-tax">TZS 0</span></div>
            <div class="d-flex justify-content-between mb-2">
                <span>Discount</span>
                <input type="number" id="pos-discount" class="form-control form-control-sm w-50" value="0" min="0">
            </div>
            <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span id="pos-total">TZS 0</span></div>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Payment Method</label>
            <select id="pos-payment" class="form-select">
                <?php foreach (PaymentMethod::options() as $key => $label): ?>
                    <option value="<?= Html::encode($key) ?>"><?= Html::encode($label) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <button type="button" id="pos-checkout" class="btn btn-admin w-100 btn-lg mb-2">Complete Sale</button>
        <button type="button" id="pos-clear" class="btn btn-outline-secondary w-100">Clear Cart</button>
        <div id="pos-message" class="mt-3"></div>
    </div>
</div>
<?php
$this->registerJsVar('posTaxRate', (float) Yii::$app->params['restaurant.taxRate']);
$this->registerJsVar('posCheckoutUrl', Url::to(['pos/checkout']));
$this->registerJsVar('posSearchUrl', Url::to(['pos/search']));
$this->registerJsFile('@web/js/pos.js', ['depends' => [\backend\assets\AdminAsset::class]]);

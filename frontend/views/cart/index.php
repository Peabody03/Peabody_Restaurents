<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\CartItem[] $items */
/** @var float $subtotal */
/** @var float $taxRate */

use yii\helpers\Html;

$this->title = 'Your Cart';
$tax = round($subtotal * $taxRate, 2);
$total = $subtotal + $tax;
$fmt = static fn (float $n): string => 'TZS ' . number_format($n, 0, '.', ',');
?>
<div class="cart-page py-4 animate-fade-in">
    <h1 class="h2 fw-bold mb-4"><?= Html::encode($this->title) ?></h1>

    <?php if ($items === []): ?>
        <div class="alert alert-info">Your cart is empty. <?= Html::a('Browse menu', ['menu/index']) ?></div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="list-group list-group-flush">
                        <?php foreach ($items as $item): ?>
                            <div class="list-group-item d-flex gap-3 align-items-center py-3">
                                <div class="food-card-img-sm" style="background-image:url('<?= Html::encode($item->food->getImageUrl()) ?>')"></div>
                                <div class="flex-grow-1">
                                    <strong><?= Html::encode($item->food->food_name) ?></strong>
                                    <div class="text-muted small"><?= $item->food->getFormattedPrice() ?> each</div>
                                </div>
                                <?= Html::beginForm(['update'], 'post', ['class' => 'd-flex align-items-center gap-1'])
                                    . Html::hiddenInput('food_id', $item->food_id)
                                    . Html::input('number', 'quantity', $item->quantity, ['class' => 'form-control form-control-sm', 'style' => 'width:70px', 'min' => 1])
                                    . Html::submitButton('Update', ['class' => 'btn btn-sm btn-outline-secondary'])
                                    . Html::endForm() ?>
                                <strong><?= $fmt($item->getLineTotal()) ?></strong>
                                <?= Html::beginForm(['remove'], 'post')
                                    . Html::hiddenInput('food_id', $item->food_id)
                                    . Html::submitButton('&times;', ['class' => 'btn btn-sm btn-outline-danger'])
                                    . Html::endForm() ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4">
                    <h2 class="h5 fw-bold mb-3">Order Summary</h2>
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span><?= $fmt($subtotal) ?></span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Tax</span><span><?= $fmt($tax) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-3"><span>Total</span><span><?= $fmt($total) ?></span></div>
                    <?= Html::a('Proceed to Checkout', ['order/checkout'], ['class' => 'btn btn-menu w-100 btn-lg']) ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

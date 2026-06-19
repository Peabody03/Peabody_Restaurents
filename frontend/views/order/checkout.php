<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \frontend\models\CheckoutForm $model */
/** @var \common\models\CartItem[] $items */
/** @var float $subtotal */
/** @var float $tax */
/** @var float $total */

use common\helpers\PaymentMethod;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Checkout';
$fmt = static fn (float $n): string => 'TZS ' . number_format($n, 0, '.', ',');
$selectedPayment = $model->payment_method;
?>
<div class="checkout-page py-4 animate-fade-in">
    <h1 class="h2 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted mb-4">Choose how you would like to pay and complete your order.</p>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 checkout-card">
                <?php $form = ActiveForm::begin(['id' => 'checkout-form']); ?>

                <?= $form->field($model, 'delivery_type')->dropDownList([
                    'pickup' => 'Pickup at restaurant',
                    'delivery' => 'Home delivery',
                ]) ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Payment Method</label>
                    <div class="payment-methods" role="radiogroup" aria-label="Payment method">
                        <?php foreach (PaymentMethod::definitions() as $key => $definition): ?>
                            <label class="payment-method-card<?= $selectedPayment === $key ? ' is-selected' : '' ?>">
                                <input type="radio"
                                       name="<?= Html::encode($model->formName()) ?>[payment_method]"
                                       value="<?= Html::encode($key) ?>"
                                       class="payment-method-card__input"
                                       data-payment-type="<?= Html::encode($definition['type']) ?>"
                                       <?= $selectedPayment === $key ? 'checked' : '' ?>>
                                <span class="payment-method-card__body">
                                    <span class="payment-brand payment-brand--<?= Html::encode($definition['brand']) ?>" aria-hidden="true">
                                        <?= $this->render('_payment-brand', ['brand' => $definition['brand']]) ?>
                                    </span>
                                    <span class="payment-method-card__text">
                                        <strong><?= Html::encode($definition['label']) ?></strong>
                                        <small><?= Html::encode($definition['description']) ?></small>
                                    </span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?= Html::error($model, 'payment_method', ['class' => 'invalid-feedback d-block']) ?>
                </div>

                <div id="checkout-card-panel" class="checkout-card-panel<?= PaymentMethod::isCard($selectedPayment) ? '' : ' d-none' ?>">
                    <p class="checkout-panel-title mb-3">Card details</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="card-number">Card number</label>
                            <input type="text" id="card-number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric" autocomplete="cc-number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="card-expiry">Expiry</label>
                            <input type="text" id="card-expiry" class="form-control" placeholder="MM / YY" maxlength="7" autocomplete="cc-exp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="card-cvc">CVC</label>
                            <input type="text" id="card-cvc" class="form-control" placeholder="123" maxlength="4" inputmode="numeric" autocomplete="cc-csc">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="card-name">Name on card</label>
                            <input type="text" id="card-name" class="form-control" placeholder="As shown on card" autocomplete="cc-name">
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Demo checkout — card details are not stored. Your order is recorded with the selected payment method.</p>
                </div>

                <div id="checkout-mobile-panel" class="checkout-mobile-panel<?= PaymentMethod::type($selectedPayment) === 'mobile' ? '' : ' d-none' ?>">
                    <p class="checkout-panel-title mb-2">Mobile Money</p>
                    <p class="small text-muted mb-0">You will receive payment instructions for M-Pesa, Tigo Pesa, or Airtel Money after placing your order.</p>
                </div>

                <?= $form->field($model, 'notes')->textarea(['rows' => 3])->label('Order notes (optional)') ?>

                <?= Html::submitButton('Place Order & Pay', ['class' => 'btn btn-menu btn-lg w-100 mt-2']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 checkout-summary">
                <h2 class="h5 fw-bold mb-3">Your Order</h2>
                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between small mb-2">
                        <span><?= Html::encode($item->food->food_name) ?> &times; <?= $item->quantity ?></span>
                        <span><?= $fmt($item->getLineTotal()) ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= $fmt($subtotal) ?></span></div>
                <div class="d-flex justify-content-between"><span>Tax</span><span><?= $fmt($tax) ?></span></div>
                <div class="d-flex justify-content-between fw-bold fs-5 mt-2 checkout-total">
                    <span>Total</span>
                    <span><?= $fmt($total) ?></span>
                </div>
                <div class="checkout-secure-note mt-3">
                    <span class="checkout-secure-icon" aria-hidden="true">&#128274;</span>
                    <span>Secure checkout with trusted payment options</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<'JS'
(function () {
    const cardPanel = document.getElementById('checkout-card-panel');
    const mobilePanel = document.getElementById('checkout-mobile-panel');
    const radios = document.querySelectorAll('.payment-method-card__input');

    function syncPanels(type) {
        cardPanel?.classList.toggle('d-none', type !== 'card');
        mobilePanel?.classList.toggle('d-none', type !== 'mobile');
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.payment-method-card').forEach(function (card) {
                card.classList.remove('is-selected');
            });
            radio.closest('.payment-method-card')?.classList.add('is-selected');
            syncPanels(radio.dataset.paymentType || '');
        });
    });

    const cardNumber = document.getElementById('card-number');
    if (cardNumber) {
        cardNumber.addEventListener('input', function () {
            let value = cardNumber.value.replace(/\D/g, '').slice(0, 16);
            cardNumber.value = value.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
        });
    }

    const cardExpiry = document.getElementById('card-expiry');
    if (cardExpiry) {
        cardExpiry.addEventListener('input', function () {
            let value = cardExpiry.value.replace(/\D/g, '').slice(0, 4);
            if (value.length > 2) {
                value = value.slice(0, 2) + ' / ' + value.slice(2);
            }
            cardExpiry.value = value;
        });
    }
})();
JS);
?>

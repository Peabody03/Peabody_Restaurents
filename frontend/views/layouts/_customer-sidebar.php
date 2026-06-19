<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use common\services\CartService;
use yii\helpers\Html;
use yii\helpers\Url;

$active = $this->params['customerNav'] ?? 'dashboard';

$cartCount = 0;
if (!Yii::$app->user->isGuest) {
    $cartCount = (new CartService())->getCount((int) Yii::$app->user->id);
}

$icons = [
    'dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'menu' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h10"/></svg>',
    'orders' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2h12v4l-2 14H8L6 6V2z"/><path d="M6 6h12"/></svg>',
    'cart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/><path d="M2 2h3l2.4 12.4a1 1 0 0 0 1 .8h9.2a1 1 0 0 0 1-.8L21 6H6"/></svg>',
    'inventory' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16v13H4z"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M9 12h6M9 16h6"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>',
    'contact' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.529 5.86L.057 23.43a.75.75 0 0 0 .92.92l5.57-1.472A11.95 11.95 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.72 9.72 0 0 1-4.97-1.36l-.355-.21-3.304.872.882-3.22-.231-.37A9.72 9.72 0 0 1 2.25 12C2.25 6.9 6.9 2.25 12 2.25S21.75 6.9 21.75 12 17.1 21.75 12 21.75z"/></svg>',
];

$whatsappDisplay = (string) (Yii::$app->params['restaurant.whatsapp'] ?? '');
$whatsappIntl = (string) (Yii::$app->params['restaurant.whatsappIntl'] ?? '');
$whatsappUrl = $whatsappIntl !== ''
    ? 'https://wa.me/' . rawurlencode($whatsappIntl) . '?text=' . rawurlencode('Hello ' . (Yii::$app->params['restaurant.name'] ?? 'Peabody Restaurant') . ', I would like to get in touch.')
    : '#';

$links = [
    'dashboard' => ['label' => 'Dashboard', 'url' => ['/dashboard/index']],
    'menu' => ['label' => 'Menu', 'url' => ['/menu/index']],
    'orders' => ['label' => 'My Order', 'url' => ['/order/my-orders']],
    'cart' => ['label' => 'Cart', 'url' => ['/cart/index'], 'badge' => $cartCount],
    'inventory' => ['label' => 'Inventory', 'url' => ['/dashboard/inventory']],
    'settings' => ['label' => 'Settings', 'url' => ['/account/settings']],
];
?>
<div class="customer-sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

<aside class="customer-sidebar" id="customerSidebar" aria-label="Main navigation">
    <a href="<?= Html::encode(Url::to(['/dashboard/index'])) ?>" class="customer-sidebar-brand">
        <span class="customer-logo-mark" aria-hidden="true">P</span>
        <span class="customer-brand-text">PEABODY</span>
    </a>

    <nav class="customer-nav">
        <p class="customer-nav-label">Main Menu</p>
        <?php foreach ($links as $key => $link): ?>
            <a href="<?= Html::encode(Url::to($link['url'])) ?>"
               class="customer-nav-link<?= $active === $key ? ' active' : '' ?>"
               <?= $active === $key ? 'aria-current="page"' : '' ?>>
                <span class="customer-nav-icon" aria-hidden="true"><?= $icons[$key] ?></span>
                <span class="customer-nav-text"><?= Html::encode($link['label']) ?></span>
                <?php if (!empty($link['badge'])): ?>
                    <span class="customer-nav-badge"><?= (int) $link['badge'] ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="customer-nav-footer">
        <?php if ($whatsappDisplay !== ''): ?>
            <a href="<?= Html::encode($whatsappUrl) ?>"
               class="customer-nav-link customer-nav-link--whatsapp"
               target="_blank"
               rel="noopener noreferrer">
                <span class="customer-nav-icon" aria-hidden="true"><?= $icons['contact'] ?></span>
                <span class="customer-nav-text">
                    <span class="d-block">Contact Us</span>
                    <small class="customer-nav-sub"><?= Html::encode($whatsappDisplay) ?></small>
                </span>
            </a>
        <?php endif ?>

        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'customer-logout-form']) ?>
            <button type="submit" class="customer-nav-link logout">
                <span class="customer-nav-icon" aria-hidden="true"><?= $icons['logout'] ?></span>
                <span class="customer-nav-text">Logout</span>
            </button>
        <?= Html::endForm() ?>
    </div>
</aside>

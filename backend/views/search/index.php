<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $q */
/** @var \common\models\Order[] $orders */
/** @var \common\models\User[] $customers */
/** @var \common\models\Food[] $foods */

use yii\helpers\Html;

$this->title = 'Search Results';
?>
<h1 class="page-title mb-1">Search: "<?= Html::encode($q) ?>"</h1>
<p class="text-muted mb-4"><?= count($orders) + count($customers) + count($foods) ?> results found</p>

<?php if ($orders !== []): ?>
<div class="admin-panel mb-4">
    <div class="panel-header"><h2 class="panel-title">Orders</h2></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= Html::encode($o->order_number) ?></td>
                    <td><?= Html::encode($o->user->username ?? '—') ?></td>
                    <td><?= $o->getFormattedTotal() ?></td>
                    <td><?= Html::a('View', ['order/view', 'id' => $o->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif ?>

<?php if ($customers !== []): ?>
<div class="admin-panel mb-4">
    <div class="panel-header"><h2 class="panel-title">Customers</h2></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Username</th><th>Email</th><th>Phone</th></tr></thead>
            <tbody>
            <?php foreach ($customers as $c): ?>
                <tr><td><?= Html::encode($c->username) ?></td><td><?= Html::encode($c->email) ?></td><td><?= Html::encode($c->phone) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif ?>

<?php if ($foods !== []): ?>
<div class="admin-panel mb-4">
    <div class="panel-header"><h2 class="panel-title">Inventory / Menu</h2></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Item</th><th>Category</th><th>Price</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($foods as $f): ?>
                <tr>
                    <td><?= Html::encode($f->food_name) ?></td>
                    <td><?= Html::encode($f->menu_type) ?></td>
                    <td><?= $f->getFormattedPrice() ?></td>
                    <td><?= Html::a('Edit', ['food/update', 'id' => $f->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif ?>

<?php if ($orders === [] && $customers === [] && $foods === []): ?>
    <div class="alert alert-info">No results found for your search.</div>
<?php endif ?>

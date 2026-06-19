<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var \common\models\Reservation[] $reservations */

use yii\helpers\Html;

$this->title = 'My Reservations';
$statusBadge = static fn (string $s): string => match ($s) {
    'confirmed' => 'success',
    'cancelled' => 'danger',
    'completed' => 'secondary',
    default => 'warning',
};
?>
<div class="py-4 animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h2 fw-bold mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('Book a Table', ['create'], ['class' => 'btn btn-menu']) ?>
    </div>

    <?php if ($reservations === []): ?>
        <div class="alert alert-info">No reservations yet. <?= Html::a('Book a table', ['create']) ?></div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Date</th><th>Time</th><th>Guests</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($r->reservation_date) ?></td>
                            <td><?= Html::encode(substr($r->reservation_time, 0, 5)) ?></td>
                            <td><?= $r->guests ?></td>
                            <td><span class="badge bg-<?= $statusBadge($r->status) ?>"><?= ucfirst($r->status) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif ?>
</div>

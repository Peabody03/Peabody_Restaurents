<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $brand */
?>
<?php if ($brand === 'mastercard'): ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="Mastercard">
        <rect width="48" height="32" rx="6" fill="#1a1d21"/>
        <circle cx="19" cy="16" r="9" fill="#EB001B"/>
        <circle cx="29" cy="16" r="9" fill="#F79E1B"/>
        <path d="M24 9.2a9 9 0 0 0-3.4 6.8A9 9 0 0 0 24 22.8a9 9 0 0 0 3.4-6.8A9 9 0 0 0 24 9.2z" fill="#FF5F00"/>
    </svg>
<?php elseif ($brand === 'visa'): ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="Visa">
        <rect width="48" height="32" rx="6" fill="#1A1F71"/>
        <text x="24" y="21" text-anchor="middle" fill="#fff" font-size="12" font-family="Arial, sans-serif" font-weight="700" font-style="italic">VISA</text>
    </svg>
<?php elseif ($brand === 'amex'): ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="American Express">
        <rect width="48" height="32" rx="6" fill="#2E77BC"/>
        <text x="24" y="14" text-anchor="middle" fill="#fff" font-size="6.5" font-family="Arial, sans-serif" font-weight="700">AMERICAN</text>
        <text x="24" y="22" text-anchor="middle" fill="#fff" font-size="6.5" font-family="Arial, sans-serif" font-weight="700">EXPRESS</text>
    </svg>
<?php elseif ($brand === 'card'): ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="Card">
        <rect width="48" height="32" rx="6" fill="#334155"/>
        <rect x="8" y="10" width="32" height="12" rx="2" fill="#fff" opacity="0.9"/>
        <rect x="8" y="24" width="12" height="2" rx="1" fill="#fff" opacity="0.75"/>
    </svg>
<?php elseif ($brand === 'mobile'): ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="Mobile Money">
        <rect width="48" height="32" rx="6" fill="#FF6B00"/>
        <rect x="16" y="6" width="16" height="20" rx="3" fill="#fff"/>
        <circle cx="24" cy="23" r="1.5" fill="#FF6B00"/>
    </svg>
<?php else: ?>
    <svg viewBox="0 0 48 32" width="48" height="32" role="img" aria-label="Cash">
        <rect width="48" height="32" rx="6" fill="#10B981"/>
        <rect x="10" y="10" width="28" height="12" rx="2" fill="#fff" opacity="0.9"/>
        <circle cx="24" cy="16" r="3" fill="#10B981"/>
    </svg>
<?php endif ?>

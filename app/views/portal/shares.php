<?php
$shareQuantity = $shareQuantity ?? 0;
$shareCapital = $shareCapital ?? 0;
$shareValue = $shareValue ?? 20000;
$shareTransactions = $shareTransactions ?? [];
$member = $member ?? [];
?>

<!-- Summary Cards -->
<div class="portal-content-padded" style="border-bottom: 8px solid #F9FAFB;">
    <div class="portal-stats-grid">
        <div class="portal-card portal-card--stat">
            <div class="portal-value"><?= number_format($shareQuantity, 2) ?></div>
            <div class="portal-label">Total Shares</div>
        </div>
        <div class="portal-card portal-card--stat">
            <div class="portal-value">Shs <?= number_format($shareCapital, 0) ?></div>
            <div class="portal-label">Share Value</div>
        </div>
        <div class="portal-card portal-card--stat">
            <div class="portal-value"><?= count($shareTransactions) ?></div>
            <div class="portal-label">Transactions</div>
        </div>
    </div>
</div>

<!-- Current Share Value -->
<div style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); padding: 16px var(--portal-space-lg); border-bottom: 8px solid #F9FAFB; display: flex; align-items: center; justify-content: space-between;">
    <div style="font-size: 13px; color: #4C51BF; font-weight: 600;">Current Share Price</div>
    <div style="font-size: 16px; font-weight: 800; color: #1B2B6B;">Shs <?= number_format($shareValue, 0) ?></div>
</div>

<!-- Transaction History -->
<div class="portal-content-padded">
    <div class="portal-section-header">
        <div>
            <h2 class="portal-section-title">Transaction History</h2>
        </div>
        <span class="portal-section-subtitle"><?= count($shareTransactions) ?> transactions</span>
    </div>

    <?php if (!empty($shareTransactions)): ?>
    <div class="portal-list">
        <?php foreach ($shareTransactions as $txn): 
            $isCredit = !in_array($txn['transaction_type'] ?? '', ['transfer_out', 'redemption']);
            $typeLabel = htmlspecialchars(str_replace('_', ' ', ucwords($txn['transaction_type'] ?? 'Unknown', '_')));
            $date = isset($txn['date']) ? date('d M Y', strtotime($txn['date'])) : 'N/A';
            $quantity = $txn['quantity'] ?? 0;
            $amount = $txn['amount'] ?? 0;
            $shareVal = $txn['share_value'] ?? 0;
            $reference = htmlspecialchars($txn['reference_number'] ?? 'N/A');
            $badgeClass = $isCredit ? 'portal-badge--success' : 'portal-badge--danger';
        ?>
        <div class="portal-list__item">
            <div class="portal-list__content">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <span class="portal-badge <?= $badgeClass ?>"><?= $typeLabel ?></span>
                    <div class="portal-list__amount">Shs <?= number_format($amount, 0) ?></div>
                </div>
                <div class="portal-list__meta">
                    <span><i class="bi bi-calendar3"></i> <?= $date ?></span>
                    <span><i class="bi bi-award"></i> <?= number_format($quantity, 4) ?> shares</span>
                    <span><i class="bi bi-tag"></i> @ Shs <?= number_format($shareVal, 0) ?></span>
                    <?php if ($reference !== 'N/A'): ?>
                    <span><i class="bi bi-hash"></i> <?= $reference ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="portal-empty">
        <div class="portal-empty__icon"><i class="bi bi-award"></i></div>
        <div class="portal-empty__text">No share transactions yet</div>
    </div>
    <?php endif; ?>
</div>


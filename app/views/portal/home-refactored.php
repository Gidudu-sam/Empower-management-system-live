<?php $base = APP_URL . '/index.php'; ?>

<!-- Dashboard Header -->
<div class="portal-header-home">
    <div>
        <div class="portal-greeting">Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?></div>
        <h1 class="portal-name"><?= htmlspecialchars($member['first_name'] ?? 'Member') ?></h1>
    </div>
    <div class="portal-avatar portal-avatar--md portal-avatar--primary portal-avatar--interactive" 
         onclick="window.location='<?= APP_URL ?>/index.php?page=portal-profile'">
        <?= strtoupper(substr($member['first_name'] ?? 'M', 0, 1)) ?>
    </div>
</div>

<!-- Balance Card -->
<div class="portal-card--featured">
    <div class="portal-label portal-label--light">Savings Balance</div>
    <div class="portal-value portal-value--large portal-value--light">
        Shs <?= number_format($savingsBalance, 0) ?>
    </div>
    <div style="font-size: 12px; opacity: 0.9; display: flex; align-items: center; gap: 4px; margin-top: 8px;">
        <?php 
        $lastDeposit = !empty($recentTxns) ? array_values(array_filter($recentTxns, fn($t) => (float)$t['amount'] > 0))[0] ?? null : null;
        ?>
        <span style="color: #34D399; font-weight: 700;">
            +Shs <?= $lastDeposit ? number_format((float)$lastDeposit['amount'], 0) : '0' ?>
        </span>
        last deposit, <?= $lastDeposit ? date('d M', strtotime($lastDeposit['transaction_date'])) : 'N/A' ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="portal-stats-grid">
    <div class="portal-card--stat">
        <div class="portal-label">Active Loan</div>
        <div class="portal-value" style="font-size: 22px; text-align: left; margin-bottom: 4px;">
            <?php if ($activeLoan): ?>
                Shs <?= number_format($activeLoan['outstanding'], 0) ?>
            <?php else: ?>
                None
            <?php endif; ?>
        </div>
        <div class="portal-description" style="text-align: left;">
            <?= $activeLoan ? 'Outstanding balance' : 'No active loan' ?>
        </div>
    </div>
    
    <div class="portal-card--stat">
        <div class="portal-label">My Shares</div>
        <div class="portal-value" style="font-size: 22px; text-align: left; margin-bottom: 4px;">
            <?= number_format($sharePosition['count']) ?>
        </div>
        <div class="portal-description" style="text-align: left;">
            Shs <?= number_format($sharePosition['value'] ?? 0, 0) ?> value
        </div>
    </div>
    
    <div class="portal-card--stat">
        <div class="portal-label">Pending Fees</div>
        <div class="portal-value" style="font-size: 22px; text-align: left; margin-bottom: 4px;">
            <?= count($pendingFees) ?>
        </div>
        <div class="portal-description" style="text-align: left;">
            <?= count($pendingFees) > 0 ? 'Require payment' : 'All paid' ?>
        </div>
    </div>
</div>

<!-- Desktop Grid: Chart + Transactions -->
<div class="portal-desktop-split">
    <!-- Savings Trend -->
    <div class="portal-card--content">
        <div class="portal-section-header">
            <div>
                <div class="portal-section-title">Savings trend</div>
                <div class="portal-section-subtitle">Track your savings growth over time</div>
            </div>
            <div style="font-size: 11px; color: var(--portal-text-tertiary); font-weight: 500;">
                Last 6 months
            </div>
        </div>
        
        <div class="portal-chart">
            <canvas id="savingsTrendChart" 
                    data-labels='<?= json_encode(array_column($savingsTrend, 'label')) ?>'
                    data-balance='<?= json_encode(array_map(fn($r) => round($r['balance'], 2), $savingsTrend)) ?>'></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="portal-card--content">
        <div class="portal-section-header">
            <div>
                <div class="portal-section-title">Recent activity</div>
                <div class="portal-section-subtitle">Latest account transactions</div>
            </div>
            <a href="<?= $base ?>?page=portal-savings" class="portal-section-link">View all</a>
        </div>
        
        <?php if (empty($recentTxns)): ?>
        <div class="portal-empty">
            <i class="bi bi-inbox portal-empty__icon"></i>
            <div class="portal-empty__text">No transactions yet</div>
        </div>
        <?php else: ?>
            <ul class="portal-list">
                <?php foreach (array_slice($recentTxns, 0, 5) as $t): ?>
                <li class="portal-list__item">
                    <div class="portal-list__icon">
                        <i class="bi bi-arrow-down-circle-fill"></i>
                    </div>
                    <div class="portal-list__content">
                        <div class="portal-list__title">
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $t['transaction_type']))) ?>
                        </div>
                        <div class="portal-list__meta">
                            <?= date('d M Y', strtotime($t['transaction_date'])) ?> • 
                            <?= htmlspecialchars(ucfirst($t['payment_method'] ?? 'Cash')) ?>
                        </div>
                    </div>
                    <div class="portal-list__aside">
                        <div class="portal-list__amount">
                            +<?= number_format(abs((float)$t['amount']), 0) ?>
                        </div>
                        <div class="portal-list__balance">
                            Bal <?= number_format((float)($t['running_balance'] ?? 0), 0) ?>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/public/js/portal-chart.js"></script>

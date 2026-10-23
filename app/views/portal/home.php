<?php $base = APP_URL . '/index.php'; ?>

<?php if (!empty($success)): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="portal-header-home">
    <div style="flex: 1;">
        <h1 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 4px 0; line-height: 1.2;">
            Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars($member['first_name'] ?? 'Member') ?>
        </h1>
        <p style="font-size: 14px; color: #6B7280; margin: 0; font-weight: 400;">Welcome to your member account</p>
    </div>
</div>

<!-- Balance Card -->
<div class="portal-card--featured">
    <div class="portal-label portal-label--light">Savings Balance</div>
    <div class="portal-value portal-value--large portal-value--light">Shs <?= number_format($savingsBalance, 0) ?></div>
    <div style="font-size: 12px; opacity: 0.9; display: flex; align-items: center; gap: 4px; margin-top: 8px;">
        <?php 
        // Find the last actual deposit (credit > 0), not just positive amount
        $lastDeposit = !empty($recentTxns) ? array_values(array_filter($recentTxns, fn($t) => (float)($t['credit'] ?? 0) > 0))[0] ?? null : null;
        ?>
        <span class="amount-green">+Shs <?= $lastDeposit ? number_format((float)$lastDeposit['credit'], 0) : '0' ?></span> last deposit<?= $lastDeposit ? ', ' . date('d M', strtotime($lastDeposit['transaction_date'])) : '' ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="portal-stats-grid">
    <a href="<?= $base ?>?page=portal-loans" class="portal-card--stat" style="text-decoration: none; color: inherit; cursor: pointer;">
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
    </a>
    <a href="<?= $base ?>?page=portal-share-certificate" class="portal-card--stat" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="portal-label">My Shares</div>
        <div class="portal-value" style="font-size: 22px; text-align: left; margin-bottom: 4px;"><?= number_format($sharePosition['count']) ?></div>
        <div class="portal-description" style="text-align: left;">
            Shs <?= number_format($sharePosition['value'] ?? 0, 0) ?> value
        </div>
    </a>
    <a href="<?= $base ?>?page=portal-fees" class="portal-card--stat" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="portal-label">Pending Fees</div>
        <div class="portal-value" style="font-size: 22px; text-align: left; margin-bottom: 4px;"><?= count($pendingFees) ?></div>
        <div class="portal-description" style="text-align: left;">
            <?= count($pendingFees) > 0 ? 'Require payment' : 'All paid' ?>
        </div>
    </a>
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
            <div style="font-size: 11px; color: var(--portal-text-tertiary); font-weight: 500;">Last 6 months</div>
        </div>
        
        <div class="portal-chart">
            <canvas id="savingsTrendChart" 
                    style="display: block; width: 100%; height: 100%;"
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
                <?php foreach (array_slice($recentTxns, 0, 5) as $t): 
                    // Determine if credit (deposit) or debit (withdrawal)
                    $isCredit = (float)($t['credit'] ?? 0) > 0;
                    $isDebit = (float)($t['debit'] ?? 0) > 0;
                    $amount = abs((float)$t['amount']);
                    $icon = $isCredit ? 'bi-arrow-down-circle-fill' : 'bi-arrow-up-circle-fill';
                    $amountClass = $isCredit ? 'amount-green' : 'amount-red';
                    $sign = $isCredit ? '+' : '-';
                ?>
                <li class="portal-list__item">
                    <div class="portal-list__icon">
                        <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div class="portal-list__content">
                        <div class="portal-list__title"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $t['transaction_type']))) ?></div>
                        <div class="portal-list__meta">
                            <?= date('d M Y', strtotime($t['transaction_date'])) ?> • 
                            <?= htmlspecialchars(ucfirst($t['payment_method'] ?? 'Cash')) ?>
                        </div>
                    </div>
                    <div class="portal-list__aside">
                        <div class="portal-list__amount">
                            <span class="<?= $amountClass ?>"><?= $sign ?>Shs <?= number_format($amount, 0) ?></span>
                        </div>
                        <div class="portal-list__balance">Bal Shs <?= number_format((float)($t['running_balance'] ?? 0), 0) ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

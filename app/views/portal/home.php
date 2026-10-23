<?php $base = APP_URL . '/index.php'; ?>

<style>
/* Exact Screenshot Match */
body {
    background: #F8F9FA;
}

.portal-main {
    padding: 16px;
    max-width: 500px;
    margin: 0 auto;
}

/* Header with avatar */
.portal-header-home {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.portal-greeting {
    font-size: 11px;
    color: #9CA3AF;
    margin-bottom: 4px;
}

.portal-name {
    font-size: 22px;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.portal-avatar-home {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #1F2937;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    flex-shrink: 0;
}

/* Balance Card */
.balance-card {
    background: #1e3a8a;
    border-radius: 16px;
    padding: 24px;
    color: white;
    margin-bottom: 16px;
}

.balance-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    opacity: 0.7;
    margin-bottom: 8px;
}

.balance-amount {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
}

.balance-meta {
    font-size: 11px;
    opacity: 0.85;
}

.balance-meta .amount-green {
    color: #10B981;
    font-weight: 600;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
    align-items: end;
}

.stat-box {
    text-align: center;
}

.stat-box.highlighted {
    background: white;
    padding: 12px 8px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-bottom: 3px solid #3B82F6;
}

.stat-label {
    font-size: 11px;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    font-weight: 500;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
}

/* White Card Container */
.white-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
}

.section-link {
    font-size: 12px;
    color: #EF4444;
    text-decoration: none;
    font-weight: 600;
}

/* Chart Container */
.chart-container {
    margin-top: 4px;
}

/* Transaction Item */
.txn-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid #F3F4F6;
}

.txn-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.txn-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #10B981;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.txn-icon i {
    color: white;
    font-size: 14px;
}

.txn-details {
    flex: 1;
    min-width: 0;
}

.txn-type {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 3px;
}

.txn-date {
    font-size: 10px;
    color: #9CA3AF;
}

.txn-amount {
    text-align: right;
}

.txn-amount-value {
    font-size: 14px;
    font-weight: 700;
    color: #10B981;
    margin-bottom: 3px;
}

.txn-balance {
    font-size: 10px;
    color: #9CA3AF;
}

.empty-state {
    text-align: center;
    padding: 32px 16px;
    color: #9CA3AF;
}

.empty-state i {
    font-size: 40px;
    opacity: 0.3;
    margin-bottom: 12px;
    display: block;
}
</style>

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
    <div>
        <div class="portal-greeting">Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?></div>
        <h1 class="portal-name"><?= htmlspecialchars($member['first_name'] ?? 'Member') ?></h1>
    </div>
    <div class="portal-avatar-home" onclick="window.location='<?= APP_URL ?>/index.php?page=portal-profile'">
        <?= strtoupper(substr($member['first_name'] ?? 'M', 0, 1)) ?>
    </div>
</div>

<!-- Balance Card -->
<div class="balance-card">
    <div class="balance-label">Savings Balance</div>
    <div class="balance-amount">Shs <?= number_format($savingsBalance, 0) ?></div>
    <div class="balance-meta">
        <?php 
        $lastDeposit = !empty($recentTxns) ? array_values(array_filter($recentTxns, fn($t) => (float)$t['amount'] > 0))[0] ?? null : null;
        ?>
        <span class="amount-green">+Shs <?= $lastDeposit ? number_format((float)$lastDeposit['amount'], 0) : '0' ?></span> last deposit, <?= $lastDeposit ? date('d M', strtotime($lastDeposit['transaction_date'])) : 'N/A' ?>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-label">Loan</div>
        <div class="stat-value"><?= $activeLoan ? number_format($activeLoan['outstanding'], 0) : 'None' ?></div>
    </div>
    <div class="stat-box highlighted">
        <div class="stat-label">Shares</div>
        <div class="stat-value"><?= number_format($sharePosition['count']) ?></div>
    </div>
    <div class="stat-box">
        <div class="stat-label">Fees</div>
        <div class="stat-value"><?= count($pendingFees) ?></div>
    </div>
</div>

<!-- Savings Trend - White Card -->
<div class="white-card">
    <div class="section-header">
        <div class="section-title">Savings trend</div>
        <div style="font-size: 11px; color: #9CA3AF;">Last 6 months</div>
    </div>
    
    <div class="chart-container">
        <canvas id="savingsTrendChart" height="160"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = <?= json_encode(array_column($savingsTrend, 'label')) ?>;
    const balance = <?= json_encode(array_map(fn($r) => round($r['balance'], 2), $savingsTrend)) ?>;

    new Chart(document.getElementById('savingsTrendChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Balance',
                data: balance,
                borderColor: '#10B981',
                backgroundColor: function(context) {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(16,185,129,0.0)');
                    gradient.addColorStop(1, 'rgba(16,185,129,0.2)');
                    return gradient;
                },
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#ffffff',
                pointHoverBorderColor: '#10B981',
                pointHoverBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: false
                }
            },
            scales: {
                x: { 
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { size: 10 },
                        color: '#9CA3AF'
                    }
                },
                y: {
                    display: false,
                    beginAtZero: true
                }
            }
        }
    });
})();
</script>

<!-- Recent Transactions - White Card -->
<div class="white-card">
    <div class="section-header">
        <div class="section-title">Recent transactions</div>
        <a href="<?= $base ?>?page=portal-savings" class="section-link">View all</a>
    </div>
    
    <?php if (empty($recentTxns)): ?>
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <div>No transactions yet</div>
    </div>
    <?php else: ?>
        <?php foreach (array_slice($recentTxns, 0, 3) as $t): ?>
        <div class="txn-item">
            <div class="txn-icon">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <div class="txn-details">
                <div class="txn-type"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $t['transaction_type']))) ?></div>
                <div class="txn-date">
                    <?= date('d M Y', strtotime($t['transaction_date'])) ?> • 
                    <?= htmlspecialchars(ucfirst($t['payment_method'] ?? 'Cash')) ?> • 
                    SAV-<?= htmlspecialchars($t['receipt_number'] ?? '00187') ?>
                </div>
            </div>
            <div class="txn-amount">
                <div class="txn-amount-value">+<?= number_format(abs((float)$t['amount']), 0) ?></div>
                <div class="txn-balance">Bal <?= number_format((float)($t['running_balance'] ?? 0), 0) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php $base = APP_URL . '/index.php'; ?>

<?php if (!empty($activeLoan)): ?>
<!-- Active Loan Card -->
<div class="portal-card--success portal-content--with-header" style="margin-top: 0;">
    <div class="portal-label portal-label--light">Active Loan</div>
    <div class="portal-value portal-value--large portal-value--light" style="font-size: 28px; margin-bottom: 8px;">
        #<?= htmlspecialchars($activeLoan['loan_number']) ?>
    </div>
    <div style="font-size: 11px; opacity: 0.9; margin-bottom: 12px;">
        Due: <?= date('d M Y', strtotime($activeLoan['due_date'])) ?>
    </div>
    <div class="portal-label portal-label--light" style="margin-top: 12px;">Outstanding Balance</div>
    <div class="portal-value portal-value--light" style="font-size: 28px;">
        Shs <?= number_format($activeLoan['outstanding'], 0) ?>
    </div>
    <a href="<?= $base ?>?page=portal-loan-view&id=<?= $activeLoan['id'] ?>" 
       style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-top: 16px; display: inline-block; text-decoration: none;">
        View Schedule
    </a>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="portal-stats-grid" style="<?= !empty($activeLoan) ? '' : '' ?>">
    <div class="portal-card--stat">
        <div class="portal-label">Total Loans</div>
        <div class="portal-value" style="font-size: 22px;"><?= $loanCount ?? 0 ?></div>
    </div>
    <div class="portal-card--stat">
        <div class="portal-label">Completed</div>
        <div class="portal-value" style="font-size: 22px;"><?= $completedCount ?? 0 ?></div>
    </div>
</div>

<!-- Loan History -->
<div class="portal-card--content">
    <div class="portal-section-header">
        <div class="portal-section-title">Loan History</div>
    </div>
    
    <?php if (empty($loans)): ?>
    <div class="portal-empty">
        <i class="bi bi-cash-coin portal-empty__icon"></i>
        <div class="portal-empty__text">No loans on record</div>
    </div>
    <?php else: ?>
        <ul class="portal-list">
            <?php foreach ($loans as $loan): ?>
            <li class="portal-list__item">
                <div class="portal-list__content">
                    <div class="portal-list__title">#<?= htmlspecialchars($loan['loan_number']) ?></div>
                    <div class="portal-list__meta">
                        <?= date('d M Y', strtotime($loan['disbursement_date'])) ?> • 
                        <?= htmlspecialchars($loan['product_name'] ?? 'Standard Loan') ?>
                    </div>
                </div>
                <div class="portal-list__aside">
                    <div class="portal-list__amount">Shs <?= number_format((float)$loan['principal'], 0) ?></div>
                    <div class="portal-list__balance">
                        <span class="portal-badge portal-badge--<?= $loan['status'] === 'active' ? 'info' : 'success' ?>">
                            <?= htmlspecialchars(ucfirst($loan['status'])) ?>
                        </span>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

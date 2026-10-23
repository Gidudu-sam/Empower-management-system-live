<?php $accountModel = new MemberSavingsAccountModel(); ?>

<!-- Summary Cards -->
<div class="portal-summary-grid">
    <div class="portal-card--stat">
        <div class="portal-label">Total Balance</div>
        <div class="portal-value" style="font-size: 22px;">Shs <?= number_format($balance, 0) ?></div>
    </div>
    <div class="portal-card--stat">
        <div class="portal-label">Total Deposits</div>
        <div class="portal-value" style="font-size: 22px;"><?= (int)$depositCount ?></div>
    </div>
</div>

<!-- My Accounts -->
<div class="portal-card--content">
    <div class="portal-section-header">
        <div class="portal-section-title">My Accounts</div>
    </div>
    
    <?php if (empty($accounts)): ?>
    <div class="portal-empty">
        <i class="bi bi-wallet2 portal-empty__icon"></i>
        <div class="portal-empty__text">No savings accounts</div>
    </div>
    <?php else: ?>
        <ul class="portal-list">
            <?php foreach ($accounts as $a): ?>
            <li class="portal-list__item">
                <div class="portal-list__content">
                    <div class="portal-list__title"><?= htmlspecialchars($a['account_number']) ?></div>
                    <div class="portal-list__meta"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $a['account_type']))) ?></div>
                </div>
                <div class="portal-list__aside">
                    <div class="portal-list__amount">Shs <?= number_format($accountModel->getAccountBalance((int)$a['id']), 0) ?></div>
                    <div class="portal-list__balance">
                        <span class="portal-badge portal-badge--<?= $a['status'] === 'active' ? 'success' : 'warning' ?>">
                            <?= htmlspecialchars(ucfirst($a['status'])) ?>
                        </span>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<!-- Transaction History -->
<div class="portal-card--content">
    <div class="portal-section-header">
        <div class="portal-section-title">Transaction History</div>
    </div>
    
    <?php if (empty($history)): ?>
    <div class="portal-empty">
        <i class="bi bi-inbox portal-empty__icon"></i>
        <div class="portal-empty__text">No transactions yet</div>
    </div>
    <?php else: ?>
        <ul class="portal-list">
            <?php foreach ($history as $t): ?>
            <?php $isDebit = (float)$t['amount'] < 0; ?>
            <li class="portal-list__item">
                <div class="portal-list__icon" style="background: <?= $isDebit ? 'linear-gradient(135deg, #EF4444, #DC2626)' : 'linear-gradient(135deg, var(--portal-success), var(--portal-success-dark))' ?>">
                    <i class="bi bi-arrow-<?= $isDebit ? 'up' : 'down' ?>-circle-fill"></i>
                </div>
                <div class="portal-list__content">
                    <div class="portal-list__title"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $t['transaction_type']))) ?></div>
                    <div class="portal-list__meta">
                        <?= date('d M Y', strtotime($t['transaction_date'])) ?> • 
                        <?= htmlspecialchars(ucfirst($t['payment_method'] ?? 'Cash')) ?> • 
                        <?= htmlspecialchars($t['receipt_number'] ?? '—') ?>
                    </div>
                </div>
                <div class="portal-list__aside">
                    <div class="portal-list__amount" style="color: <?= $isDebit ? 'var(--portal-danger)' : 'var(--portal-success)' ?>">
                        <?= $isDebit ? '-' : '+' ?>Shs <?= number_format(abs((float)$t['amount']), 0) ?>
                    </div>
                    <div class="portal-list__balance">Bal <?= number_format((float)($t['running_balance'] ?? 0), 0) ?></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

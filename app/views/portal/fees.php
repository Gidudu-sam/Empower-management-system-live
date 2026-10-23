<?php
$fees = $fees ?? [];
$totalPending = 0;
$totalPaid = 0;
foreach ($fees as $f) {
    if ($f['status'] === 'pending') {
        $totalPending += (float)$f['amount'];
    } else {
        $totalPaid += (float)$f['amount'];
    }
}
?>

<!-- Summary Cards -->
<div class="portal-content-padded" style="border-bottom: 8px solid #F9FAFB;">
    <div class="portal-summary-grid portal-summary-grid--2col">
        <div class="portal-card portal-card--stat" style="background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);">
            <div class="portal-label" style="color: #C2410C; letter-spacing: 0.5px;">PENDING FEES</div>
            <div class="portal-value" style="color: #EA580C; font-size: 22px;">Shs <?= number_format($totalPending, 0) ?></div>
        </div>
        <div class="portal-card portal-card--stat" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%);">
            <div class="portal-label" style="color: #059669; letter-spacing: 0.5px;">TOTAL PAID</div>
            <div class="portal-value" style="color: #059669; font-size: 22px;">Shs <?= number_format($totalPaid, 0) ?></div>
        </div>
    </div>
</div>

<!-- Fee List -->
<div class="portal-content-padded">
    <div class="portal-section-header">
        <div>
            <h2 class="portal-section-title">All Fees</h2>
        </div>
        <span class="portal-section-subtitle"><?= count($fees) ?> fees</span>
    </div>

    <?php if (!empty($fees)): ?>
    <div class="portal-list">
        <?php foreach ($fees as $f): 
            $feeName = htmlspecialchars($f['fee_name'] ?? 'Fee');
            $amount = number_format((float)($f['amount'] ?? 0), 0);
            $status = $f['status'] ?? 'pending';
            $chargedDate = isset($f['charged_date']) ? date('d M Y', strtotime($f['charged_date'])) : 'N/A';
            $paidDate = isset($f['paid_date']) && $f['paid_date'] ? date('d M Y', strtotime($f['paid_date'])) : null;
            $badgeClass = $status === 'paid' ? 'portal-badge--success' : 'portal-badge--warning';
        ?>
        <div class="portal-list__item">
            <div class="portal-list__content">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <div class="portal-list__title"><?= $feeName ?></div>
                    <div class="portal-list__amount">Shs <?= $amount ?></div>
                </div>
                <div class="portal-list__meta">
                    <span><i class="bi bi-calendar3"></i> Charged: <?= $chargedDate ?></span>
                    <?php if ($paidDate): ?>
                    <span><i class="bi bi-check-circle"></i> Paid: <?= $paidDate ?></span>
                    <?php endif; ?>
                </div>
                <div style="margin-top: 8px;">
                    <span class="portal-badge <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="portal-empty">
        <div class="portal-empty__icon"><i class="bi bi-tag"></i></div>
        <div class="portal-empty__text">No fee charges on record</div>
    </div>
    <?php endif; ?>
</div>


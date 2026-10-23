<?php
$repayments = $repayments ?? [];
$totalPaid = $totalPaid ?? 0;
?>

<!-- Summary -->
<div class="portal-content-padded" style="border-bottom: 8px solid #F9FAFB;">
    <div class="portal-card portal-card--featured" style="background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); padding: 24px; text-align: center;">
        <div class="portal-label" style="color: #059669; letter-spacing: 0.5px;">TOTAL PAID</div>
        <div class="portal-value" style="color: #059669; font-size: 32px;">Shs <?= number_format($totalPaid, 0) ?></div>
    </div>
</div>

<!-- Repayment History -->
<div class="portal-content-padded">
    <div class="portal-section-header">
        <div>
            <h2 class="portal-section-title">Payment History</h2>
        </div>
        <span class="portal-section-subtitle"><?= count($repayments) ?> payments</span>
    </div>

    <?php if (!empty($repayments)): ?>
    <div class="portal-list">
        <?php foreach ($repayments as $r): 
            $date = date('d M Y', strtotime($r['payment_date']));
            $receiptNo = htmlspecialchars($r['repayment_number'] ?? '—');
            $loanNo = htmlspecialchars($r['loan_number']);
            $amount = number_format((float)$r['amount_paid'], 0);
        ?>
        <div class="portal-list__item">
            <div class="portal-list__content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div class="portal-list__title">Loan <?= $loanNo ?></div>
                    <div class="portal-list__amount" style="color: #059669;">Shs <?= $amount ?></div>
                </div>
                <div class="portal-list__meta">
                    <span><i class="bi bi-calendar3"></i> <?= $date ?></span>
                    <span><i class="bi bi-receipt"></i> <?= $receiptNo ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="portal-empty">
        <div class="portal-empty__icon"><i class="bi bi-receipt"></i></div>
        <div class="portal-empty__text">No repayments on record</div>
    </div>
    <?php endif; ?>
</div>



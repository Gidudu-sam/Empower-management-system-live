<?php if (!empty($success)): ?>
<div class="alert alert-success" style="background: #ECFDF5; color: #10B981; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px;">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger" style="background: #FEE2E2; color: #EF4444; padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 16px;">
    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<!-- Download Statement -->
<div class="portal-card portal-card--content" style="margin-bottom: 16px;">
    <div class="portal-section-title" style="margin-bottom: 12px;">Download Statement</div>
    
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <div class="form-group">
            <label class="form-label" style="font-size: 11px; color: #6B7280; font-weight: 500; display: block; margin-bottom: 6px;">Period</label>
            <select id="stmtPeriod" class="form-select" style="padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; width: 100%;">
                <option value="fy">Financial Year</option>
                <option value="7d">Last 7 Days</option>
                <option value="1m">Last 1 Month</option>
                <option value="3m">Last 3 Months</option>
                <option value="6m">Last 6 Months</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        
        <div id="stmtYearWrap" class="form-group">
            <label class="form-label" style="font-size: 11px; color: #6B7280; font-weight: 500; display: block; margin-bottom: 6px;">Financial Year</label>
            <select id="stmtYear" class="form-select" style="padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; width: 100%;">
                <?php foreach ($years as $y): ?>
                <option value="<?= (int)$y ?>" <?= $y == $currentFY ? 'selected' : '' ?>>
                    <?= htmlspecialchars(StatementModel::fyLabel($y)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="stmtCustomWrap" style="display: none; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label" style="font-size: 11px; color: #6B7280; font-weight: 500; display: block; margin-bottom: 6px;">From</label>
                <input type="date" id="stmtFrom" style="padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; width: 100%;">
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="form-label" style="font-size: 11px; color: #6B7280; font-weight: 500; display: block; margin-bottom: 6px;">To</label>
                <input type="date" id="stmtTo" style="padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; width: 100%;">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" style="font-size: 11px; color: #6B7280; font-weight: 500; display: block; margin-bottom: 6px;">Statement Type</label>
            <select id="stmtType" class="form-select" style="padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; width: 100%;">
                <option value="savings">Savings</option>
                <option value="shares">Shares</option>
            </select>
        </div>
        
        <button type="button" style="background: #1B2B6B; color: white; border: none; padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 4px;" id="downloadStmtBtn">
            <i class="bi bi-download"></i> Download / Print
        </button>
    </div>
</div>

<!-- Savings Transactions -->
<div class="portal-card portal-card--content" style="margin-bottom: 16px;">
    <div class="portal-section-title" style="margin-bottom: 12px;">Savings Transactions</div>
    
    <?php if (empty($savings)): ?>
    <div class="portal-empty">
        <div class="portal-empty__text">No transactions yet</div>
    </div>
    <?php else: ?>
    <div class="portal-list">
        <?php foreach ($savings as $t): ?>
        <div class="portal-list__item">
            <div class="portal-list__content">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <div style="font-size: 12px; color: #9CA3AF; margin-bottom: 2px;"><?= date('d M Y', strtotime($t['transaction_date'])) ?></div>
                        <div class="portal-list__title"><?= htmlspecialchars(ucwords(str_replace('_',' ',$t['transaction_type']))) ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div class="portal-list__amount" style="color: #10B981; margin-bottom: 2px;">Shs <?= number_format((float)$t['amount'], 0) ?></div>
                        <div style="font-size: 11px; color: #9CA3AF;">Bal Shs <?= number_format((float)($t['running_balance'] ?? 0), 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
(function () {
    var periodSelect = document.getElementById('stmtPeriod');
    var yearWrap      = document.getElementById('stmtYearWrap');
    var customWrap    = document.getElementById('stmtCustomWrap');

    function toggleWrap(el, show) {
        el.style.display = show ? 'flex' : 'none';
    }

    periodSelect.addEventListener('change', function () {
        toggleWrap(yearWrap, this.value === 'fy');
        toggleWrap(customWrap, this.value === 'custom');
    });

    function fmtDate(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function resolveParams() {
        var type   = document.getElementById('stmtType').value;
        var period = periodSelect.value;
        var params = { statement_type: type };

        if (period === 'fy') {
            params.year = document.getElementById('stmtYear').value;
        } else if (period === 'custom') {
            var from = document.getElementById('stmtFrom').value;
            var to   = document.getElementById('stmtTo').value;
            if (!from || !to) { alert('Please choose both a start and end date.'); return null; }
            if (from > to) { alert('The start date must be before the end date.'); return null; }
            params.range_mode = 'custom';
            params.date_from  = from;
            params.date_to    = to;
        } else {
            var to = new Date();
            var from = new Date();
            if (period === '7d') { from.setDate(from.getDate() - 7); }
            else { from.setMonth(from.getMonth() - { '1m': 1, '3m': 3, '6m': 6 }[period]); }
            params.range_mode = 'custom';
            params.date_from  = fmtDate(from);
            params.date_to    = fmtDate(to);
        }
        return params;
    }

    document.getElementById('downloadStmtBtn').addEventListener('click', function () {
        var params = resolveParams();
        if (!params) { return; }
        var url = '<?= APP_URL ?>/index.php?page=portal-statement-print';
        Object.keys(params).forEach(function (k) { url += '&' + k + '=' + encodeURIComponent(params[k]); });
        window.open(url, '_blank');
    });
})();
</script>


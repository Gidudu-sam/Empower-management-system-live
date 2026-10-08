<?php
/**
 * Confirm hard-delete of a closed financial year (Admin/Treasurer only).
 * Callers: FinancialYearController::deleteConfirm() via route financial-year-delete-confirm.
 * No prior delete-confirm view existed under financial-years/.
 * Display-only fields: year.id/name/status/start_date/end_date; periods[]; summary.journal_entries/total_debit/total_credit.
 * User instruction: Only the Treasurer may delete closed financial years (admin retains full access).
 */
$pageTitle = $pageTitle ?? 'Delete Closed Financial Year';
$title = $pageTitle;
$icon  = 'bi-trash';
$base  = APP_URL . '/index.php';
$periodCount = is_array($periods ?? null) ? count($periods) : 0;
?>

    <?php require VIEW_PATH . '/layouts/page-title.php'; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete <?= htmlspecialchars($year['name']) ?>?
                </div>
                <div class="card-body">
                    <?php if (Session::has('error')): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars(Session::flash('error')) ?></div>
                    <?php endif; ?>

                    <p class="mb-3">
                        This permanently deletes the <strong>closed</strong> financial year
                        (<strong><?= date('d M Y', strtotime($year['start_date'])) ?> – <?= date('d M Y', strtotime($year['end_date'])) ?></strong>),
                        all of its accounting periods, and all related journal entries / lines /
                        tied savings rows. This cannot be undone.
                    </p>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <tr><th>Status</th><td><?= htmlspecialchars(ucfirst($year['status'])) ?></td></tr>
                            <tr><th>Accounting Periods</th><td><?= (int)$periodCount ?></td></tr>
                            <tr><th>Journal Entries</th><td><?= (int)$summary['journal_entries'] ?></td></tr>
                            <tr><th>Total Debits</th><td>Shs <?= number_format($summary['total_debit'], 2) ?></td></tr>
                            <tr><th>Total Credits</th><td>Shs <?= number_format($summary['total_credit'], 2) ?></td></tr>
                        </table>
                    </div>

                    <form method="POST" action="<?= $base ?>?page=financial-year-delete">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="year_id" value="<?= (int)$year['id'] ?>">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Permanently delete this closed financial year and all related accounting data?');">
                                <i class="bi bi-trash me-1"></i>Confirm Delete
                            </button>
                            <a href="<?= $base ?>?page=financial-year-view&id=<?= (int)$year['id'] ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

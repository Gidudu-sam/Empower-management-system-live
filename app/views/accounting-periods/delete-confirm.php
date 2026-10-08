<?php
/**
 * Confirm hard-delete of an accounting period (Admin/Treasurer only).
 * Rendered by AccountingPeriodController::deleteConfirm().
 */
$pageTitle = $pageTitle ?? 'Delete Accounting Period';
$title = $pageTitle;
$icon  = 'bi-trash';
$base  = APP_URL . '/index.php';
?>

    <?php require VIEW_PATH . '/layouts/page-title.php'; ?>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete <?= htmlspecialchars($period['name']) ?>?
                </div>
                <div class="card-body">
                    <?php if (Session::has('error')): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars(Session::flash('error')) ?></div>
                    <?php endif; ?>

                    <p class="mb-3">
                        This permanently deletes the accounting period
                        (<strong><?= date('d M Y', strtotime($period['start_date'])) ?> – <?= date('d M Y', strtotime($period['end_date'])) ?></strong>)
                        and removes <strong>all journal entries and journal lines</strong> posted to it,
                        plus related savings rows tied to those journals. This cannot be undone.
                    </p>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <tr><th>Journal Entries</th><td><?= (int)$summary['journal_entries'] ?></td></tr>
                            <tr><th>Total Debits</th><td>Shs <?= number_format($summary['total_debit'], 2) ?></td></tr>
                            <tr><th>Total Credits</th><td>Shs <?= number_format($summary['total_credit'], 2) ?></td></tr>
                            <tr><th>Status</th><td><?= htmlspecialchars(ucfirst($period['status'])) ?></td></tr>
                        </table>
                    </div>

                    <form method="POST" action="<?= $base ?>?page=accounting-period-delete">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="period_id" value="<?= (int)$period['id'] ?>">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Permanently delete this period and all related journals?');">
                                <i class="bi bi-trash me-1"></i>Confirm Delete
                            </button>
                            <a href="<?= $base ?>?page=accounting-period-view&id=<?= (int)$period['id'] ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

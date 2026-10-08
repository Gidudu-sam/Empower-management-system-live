<?php
/**
 * AccountingHardDeleteService — Treasurer/Admin hard-delete of savings
 * transactions, accounting periods, and closed financial years, with
 * cascade removal of related journal entries and lines.
 *
 * JournalEntryModel / JournalLineModel intentionally forbid ordinary
 * delete(); this service is the sole authorized hard-delete path and must
 * only be invoked after controller role gates (admin + treasurer).
 *
 * All public methods run inside a DB transaction and roll back on failure.
 */
class AccountingHardDeleteService
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    /**
     * Delete one savings row and purge its linked journal entry (if any).
     * Also removes a withdrawal row that shares the same journal_entry_id
     * (WithdrawalModel posts both against one JE).
     *
     * @return array{savings_id:int, journal_entry_id:?int, receipt_number:string}
     */
    public function deleteSavingsTransaction(int $savingsId, int $userId): array
    {
        $own = !$this->db->inTransaction();
        if ($own) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM `savings` WHERE `id` = ? LIMIT 1 FOR UPDATE');
            $stmt->execute([$savingsId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                throw new InvalidArgumentException("Savings record #{$savingsId} was not found.");
            }

            $this->assertSavingsNotReferencedByContraVoucher($savingsId);

            $jeId = !empty($row['journal_entry_id']) ? (int)$row['journal_entry_id'] : null;
            $memberId = (int)$row['member_id'];
            $accountId = !empty($row['savings_account_id']) ? (int)$row['savings_account_id'] : null;
            $receipt = (string)$row['receipt_number'];

            // Detach FK before purging the journal so SET NULL children stay consistent.
            if ($jeId !== null) {
                $this->db->prepare('UPDATE `savings` SET `journal_entry_id` = NULL WHERE `id` = ?')
                    ->execute([$savingsId]);
                $this->deleteWithdrawalSharingJournal($jeId);
                $this->purgeJournalEntry($jeId, $userId, "Savings {$receipt} deleted");
            }

            $this->db->prepare('DELETE FROM `savings` WHERE `id` = ?')->execute([$savingsId]);

            if ($accountId !== null) {
                $this->recalcAccountRunningBalances($accountId);
            } else {
                $this->recalcMemberRunningBalances($memberId);
            }

            $this->logActivity(
                $userId,
                'savings_hard_deleted',
                "Hard-deleted savings {$receipt} (id {$savingsId})" .
                    ($jeId !== null ? " and journal entry #{$jeId}" : '')
            );

            if ($own) {
                $this->db->commit();
            }

            return [
                'savings_id'       => $savingsId,
                'journal_entry_id' => $jeId,
                'receipt_number'   => $receipt,
            ];
        } catch (Throwable $e) {
            if ($own && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Delete an accounting period and every journal entry posted to it,
     * plus related source savings rows / opening-balance batches /
     * provisioning runs that would otherwise orphan or block the delete.
     *
     * @return array{period_id:int, period_name:string, journals_purged:int, savings_deleted:int}
     */
    public function deleteAccountingPeriod(int $periodId, int $userId): array
    {
        $own = !$this->db->inTransaction();
        if ($own) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM `accounting_periods` WHERE `id` = ? LIMIT 1 FOR UPDATE');
            $stmt->execute([$periodId]);
            $period = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$period) {
                throw new InvalidArgumentException("Accounting period #{$periodId} was not found.");
            }

            $stats = $this->purgePeriodContents($periodId, $userId);

            $this->db->prepare('DELETE FROM `accounting_periods` WHERE `id` = ?')->execute([$periodId]);

            $this->logActivity(
                $userId,
                'accounting_period_deleted',
                "Deleted accounting period \"{$period['name']}\" (id {$periodId}); " .
                "purged {$stats['journals_purged']} journal entries and {$stats['savings_deleted']} savings rows"
            );

            if ($own) {
                $this->db->commit();
            }

            return [
                'period_id'       => $periodId,
                'period_name'     => (string)$period['name'],
                'journals_purged' => $stats['journals_purged'],
                'savings_deleted' => $stats['savings_deleted'],
            ];
        } catch (Throwable $e) {
            if ($own && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Delete a CLOSED financial year, its accounting periods, and all
     * related journals / source savings rows / opening-balance batches.
     *
     * @return array{year_id:int, year_name:string, periods_deleted:int, journals_purged:int, savings_deleted:int}
     */
    public function deleteClosedFinancialYear(int $yearId, int $userId): array
    {
        $own = !$this->db->inTransaction();
        if ($own) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM `financial_years` WHERE `id` = ? LIMIT 1 FOR UPDATE');
            $stmt->execute([$yearId]);
            $year = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$year) {
                throw new InvalidArgumentException("Financial year #{$yearId} was not found.");
            }
            if (($year['status'] ?? '') !== 'closed') {
                throw new InvalidArgumentException(
                    'Only closed financial years can be deleted. Close the year first.'
                );
            }

            $periodsStmt = $this->db->prepare(
                'SELECT `id` FROM `accounting_periods` WHERE `financial_year_id` = ? ORDER BY `id` ASC'
            );
            $periodsStmt->execute([$yearId]);
            $periodIds = array_map('intval', $periodsStmt->fetchAll(PDO::FETCH_COLUMN));

            $journalsPurged = 0;
            $savingsDeleted = 0;
            foreach ($periodIds as $pid) {
                $stats = $this->purgePeriodContents($pid, $userId);
                $journalsPurged += $stats['journals_purged'];
                $savingsDeleted += $stats['savings_deleted'];
                $this->db->prepare('DELETE FROM `accounting_periods` WHERE `id` = ?')->execute([$pid]);
            }

            // Orphan-period journals that only carry financial_year_id.
            $orphanJe = $this->db->prepare(
                'SELECT `id` FROM `journal_entries`
                 WHERE `financial_year_id` = ? AND (`accounting_period_id` IS NULL OR `accounting_period_id` = 0)
                 ORDER BY `id` ASC'
            );
            $orphanJe->execute([$yearId]);
            foreach ($orphanJe->fetchAll(PDO::FETCH_COLUMN) as $jeId) {
                $journalsPurged += $this->purgeJournalAndRelatedSources((int)$jeId, $userId, $savingsDeleted);
            }

            $this->deleteOpeningBalanceBatchesForYear($yearId, $userId, $journalsPurged, $savingsDeleted);

            $this->db->prepare('DELETE FROM `financial_years` WHERE `id` = ?')->execute([$yearId]);

            $this->logActivity(
                $userId,
                'financial_year_deleted',
                "Deleted closed financial year \"{$year['name']}\" (id {$yearId}); " .
                'removed ' . count($periodIds) . " period(s), purged {$journalsPurged} journal entries, " .
                "deleted {$savingsDeleted} savings rows"
            );

            if ($own) {
                $this->db->commit();
            }

            return [
                'year_id'         => $yearId,
                'year_name'       => (string)$year['name'],
                'periods_deleted' => count($periodIds),
                'journals_purged' => $journalsPurged,
                'savings_deleted' => $savingsDeleted,
            ];
        } catch (Throwable $e) {
            if ($own && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // -------------------------------------------------------------------------
    // Internals
    // -------------------------------------------------------------------------

    /**
     * @return array{journals_purged:int, savings_deleted:int}
     */
    private function purgePeriodContents(int $periodId, int $userId): array
    {
        $journalsPurged = 0;
        $savingsDeleted = 0;

        $this->deleteProvisioningRunsForPeriod($periodId);
        $this->deleteOpeningBalanceBatchesForPeriod($periodId, $userId, $journalsPurged, $savingsDeleted);

        // Purge reversal entries first so fk_je_reversal_of never blocks parents.
        $jeStmt = $this->db->prepare(
            'SELECT `id` FROM `journal_entries`
             WHERE `accounting_period_id` = ?
             ORDER BY (`reversal_of_id` IS NULL) ASC, `id` DESC'
        );
        $jeStmt->execute([$periodId]);
        $jeIds = array_map('intval', $jeStmt->fetchAll(PDO::FETCH_COLUMN));

        foreach ($jeIds as $jeId) {
            $journalsPurged += $this->purgeJournalAndRelatedSources($jeId, $userId, $savingsDeleted);
        }

        return [
            'journals_purged' => $journalsPurged,
            'savings_deleted' => $savingsDeleted,
        ];
    }

    /**
     * Remove source savings (and shared withdrawals) tied to a JE, then purge the JE.
     * Returns 1 if a journal was purged, 0 if it was already gone.
     */
    private function purgeJournalAndRelatedSources(int $jeId, int $userId, int &$savingsDeleted): int
    {
        $check = $this->db->prepare('SELECT `id` FROM `journal_entries` WHERE `id` = ? LIMIT 1');
        $check->execute([$jeId]);
        if (!$check->fetchColumn()) {
            return 0;
        }

        $savStmt = $this->db->prepare(
            'SELECT `id`, `member_id`, `savings_account_id`, `receipt_number`
             FROM `savings` WHERE `journal_entry_id` = ?'
        );
        $savStmt->execute([$jeId]);
        $savingsRows = $savStmt->fetchAll(PDO::FETCH_ASSOC);

        $accountsToRecalc = [];
        $membersToRecalc  = [];

        foreach ($savingsRows as $s) {
            $sid = (int)$s['id'];
            $this->assertSavingsNotReferencedByContraVoucher($sid);
            $this->db->prepare('UPDATE `savings` SET `journal_entry_id` = NULL WHERE `id` = ?')
                ->execute([$sid]);
            $this->db->prepare('DELETE FROM `savings` WHERE `id` = ?')->execute([$sid]);
            $savingsDeleted++;
            if (!empty($s['savings_account_id'])) {
                $accountsToRecalc[(int)$s['savings_account_id']] = true;
            } else {
                $membersToRecalc[(int)$s['member_id']] = true;
            }
        }

        $this->deleteWithdrawalSharingJournal($jeId);
        $this->purgeJournalEntry($jeId, $userId, 'Accounting period/year hard-delete');

        foreach (array_keys($accountsToRecalc) as $aid) {
            $this->recalcAccountRunningBalances($aid);
        }
        foreach (array_keys($membersToRecalc) as $mid) {
            $this->recalcMemberRunningBalances($mid);
        }

        return 1;
    }

    /**
     * Hard-delete one journal entry and its lines after clearing RESTRICT FKs.
     * SET NULL FKs on other modules are left to the engine on DELETE.
     */
    private function purgeJournalEntry(int $jeId, int $userId, string $reason): void
    {
        $stmt = $this->db->prepare('SELECT * FROM `journal_entries` WHERE `id` = ? LIMIT 1');
        $stmt->execute([$jeId]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$entry) {
            return;
        }

        $this->db->prepare(
            'UPDATE `corrections` SET `resulting_journal_entry_id` = NULL, `resulting_journal_entry_number` = NULL
             WHERE `resulting_journal_entry_id` = ?'
        )->execute([$jeId]);

        $this->db->prepare(
            'UPDATE `share_transactions` SET `journal_entry_id` = NULL WHERE `journal_entry_id` = ?'
        )->execute([$jeId]);

        $this->db->prepare(
            'UPDATE `journal_entries` SET `reversal_of_id` = NULL WHERE `reversal_of_id` = ?'
        )->execute([$jeId]);

        $this->db->prepare('DELETE FROM `journal_lines` WHERE `journal_entry_id` = ?')->execute([$jeId]);
        $this->db->prepare('DELETE FROM `journal_entries` WHERE `id` = ?')->execute([$jeId]);

        $this->writeJournalDeleteAudit($userId, $entry, $reason);
    }

    private function deleteWithdrawalSharingJournal(int $jeId): void
    {
        $this->db->prepare('DELETE FROM `withdrawals` WHERE `journal_entry_id` = ?')->execute([$jeId]);
    }

    private function assertSavingsNotReferencedByContraVoucher(int $savingsId): void
    {
        $stmt = $this->db->prepare(
            'SELECT `id`, `voucher_number` FROM `internal_vouchers` WHERE `contra_savings_id` = ? LIMIT 1'
        );
        $stmt->execute([$savingsId]);
        $v = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($v) {
            throw new InvalidArgumentException(
                "Cannot delete savings #{$savingsId}: internal voucher {$v['voucher_number']} " .
                "references it as contra_savings_id. Reverse or clear that voucher first."
            );
        }
    }

    private function deleteProvisioningRunsForPeriod(int $periodId): void
    {
        $idsStmt = $this->db->prepare(
            'SELECT `id` FROM `loan_provisioning_runs` WHERE `accounting_period_id` = ?'
        );
        $idsStmt->execute([$periodId]);
        $runIds = array_map('intval', $idsStmt->fetchAll(PDO::FETCH_COLUMN));
        if ($runIds === []) {
            return;
        }

        $this->db->prepare(
            'UPDATE `loan_provisioning_runs` SET `corrects_run_id` = NULL, `journal_entry_id` = NULL
             WHERE `accounting_period_id` = ?'
        )->execute([$periodId]);

        $in = implode(',', array_fill(0, count($runIds), '?'));
        $this->db->prepare("DELETE FROM `loan_provisioning_run_details` WHERE `run_id` IN ({$in})")
            ->execute($runIds);
        $this->db->prepare("DELETE FROM `loan_provisioning_runs` WHERE `id` IN ({$in})")
            ->execute($runIds);
    }

    private function deleteOpeningBalanceBatchesForPeriod(
        int $periodId,
        int $userId,
        int &$journalsPurged,
        int &$savingsDeleted
    ): void {
        $stmt = $this->db->prepare(
            'SELECT `id`, `journal_entry_id` FROM `opening_balance_batches` WHERE `accounting_period_id` = ?'
        );
        $stmt->execute([$periodId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $batch) {
            $jeId = !empty($batch['journal_entry_id']) ? (int)$batch['journal_entry_id'] : null;
            $this->db->prepare('UPDATE `opening_balance_batches` SET `journal_entry_id` = NULL WHERE `id` = ?')
                ->execute([(int)$batch['id']]);
            $this->db->prepare('DELETE FROM `opening_balance_batches` WHERE `id` = ?')
                ->execute([(int)$batch['id']]);
            if ($jeId !== null) {
                $journalsPurged += $this->purgeJournalAndRelatedSources($jeId, $userId, $savingsDeleted);
            }
        }
    }

    private function deleteOpeningBalanceBatchesForYear(
        int $yearId,
        int $userId,
        int &$journalsPurged,
        int &$savingsDeleted
    ): void {
        $stmt = $this->db->prepare(
            'SELECT `id`, `journal_entry_id` FROM `opening_balance_batches` WHERE `financial_year_id` = ?'
        );
        $stmt->execute([$yearId]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $batch) {
            $jeId = !empty($batch['journal_entry_id']) ? (int)$batch['journal_entry_id'] : null;
            $this->db->prepare('UPDATE `opening_balance_batches` SET `journal_entry_id` = NULL WHERE `id` = ?')
                ->execute([(int)$batch['id']]);
            $this->db->prepare('DELETE FROM `opening_balance_batches` WHERE `id` = ?')
                ->execute([(int)$batch['id']]);
            if ($jeId !== null) {
                $journalsPurged += $this->purgeJournalAndRelatedSources($jeId, $userId, $savingsDeleted);
            }
        }
    }

    private function recalcAccountRunningBalances(int $accountId): void
    {
        $stmt = $this->db->prepare(
            'SELECT `id`, `debit`, `credit` FROM `savings`
             WHERE `savings_account_id` = ?
             ORDER BY `transaction_date` ASC, `id` ASC'
        );
        $stmt->execute([$accountId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $balance = 0.0;
        $upd = $this->db->prepare('UPDATE `savings` SET `running_balance` = ? WHERE `id` = ?');
        foreach ($rows as $row) {
            $balance += (float)($row['credit'] ?? 0) - (float)($row['debit'] ?? 0);
            $upd->execute([$balance, $row['id']]);
        }
    }

    private function recalcMemberRunningBalances(int $memberId): void
    {
        $stmt = $this->db->prepare(
            'SELECT `id`, `debit`, `credit` FROM `savings`
             WHERE `member_id` = ? AND `savings_account_id` IS NULL
             ORDER BY `transaction_date` ASC, `id` ASC'
        );
        $stmt->execute([$memberId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $balance = 0.0;
        $upd = $this->db->prepare('UPDATE `savings` SET `running_balance` = ? WHERE `id` = ?');
        foreach ($rows as $row) {
            $balance += (float)($row['credit'] ?? 0) - (float)($row['debit'] ?? 0);
            $upd->execute([$balance, $row['id']]);
        }
    }

    private function writeJournalDeleteAudit(int $userId, array $entry, string $reason): void
    {
        $after = json_encode([
            'action'       => 'hard_delete',
            'entry_number' => $entry['entry_number'] ?? null,
            'entry_date'   => $entry['entry_date'] ?? null,
            'reason'       => $reason,
        ], JSON_UNESCAPED_UNICODE);
        $stmt = $this->db->prepare(
            'INSERT INTO `journal_entry_audit`
                (`user_id`, `action`, `entity_type`, `entity_id`, `before_json`, `after_json`, `reason`, `ip_address`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $userId,
            'hard_delete',
            'journal_entry',
            (int)$entry['id'],
            json_encode($entry, JSON_UNESCAPED_UNICODE),
            $after !== false ? $after : '{"action":"hard_delete"}',
            $reason,
            $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }

    private function logActivity(int $userId, string $action, string $desc): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO `activity_logs`(`user_id`,`action`,`description`,`ip_address`) VALUES (?,?,?,?)'
        );
        $stmt->execute([$userId, $action, $desc, $_SERVER['REMOTE_ADDR'] ?? null]);
    }
}

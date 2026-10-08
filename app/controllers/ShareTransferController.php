<?php
require_once CORE_PATH . '/Controller.php';
require_once APP_PATH  . '/services/ShareTransferService.php';
require_once APP_PATH  . '/models/MemberShareAccountModel.php';
require_once APP_PATH  . '/models/MemberSavingsAccountModel.php';
require_once APP_PATH  . '/models/MemberModel.php';

/**
 * ShareTransferController — Savings ↔ Shares internal transfers.
 *
 * Callers: index.php routes share-transfers, share-transfer-*.
 * User instruction: fix everything
 */
class ShareTransferController extends Controller
{
    private ShareTransferService $transferService;
    private MemberShareAccountModel $shareAccountModel;
    private MemberSavingsAccountModel $savingsAccountModel;
    private MemberModel $memberModel;

    public function __construct()
    {
        Session::requireAuth();
        if (!Session::hasRole(['admin', 'treasurer'])) {
            http_response_code(403);
            die('Access denied. Admin or Treasurer privileges required for share transfers.');
        }

        $this->transferService     = new ShareTransferService();
        $this->shareAccountModel   = new MemberShareAccountModel();
        $this->savingsAccountModel = new MemberSavingsAccountModel();
        $this->memberModel         = new MemberModel();
    }

    private function getCsrf(): string
    {
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return (string)Session::get('csrf_token');
    }

    private function verifyCsrf(string $token): bool
    {
        $stored = (string)Session::get('csrf_token', '');
        return $token !== '' && $stored !== '' && hash_equals($stored, $token);
    }

    private function requireTransferAccess(): void
    {
        if (!Session::hasRole(['admin', 'treasurer'])) {
            $this->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
    }

    public function index(): void
    {
        $this->render('shares/transfers', [
            'pageTitle' => 'Share Transfers — ' . APP_NAME,
            'csrfToken' => $this->getCsrf(),
            'members'   => $this->memberModel->getAllActive(),
        ], 'main');
    }

    public function getMemberAccounts(): void
    {
        $this->requireTransferAccess();

        try {
            $memberId = (int)($_GET['member_id'] ?? 0);
            if ($memberId <= 0) {
                throw new RuntimeException('Invalid member ID');
            }

            $member = $this->memberModel->find($memberId);
            if (!$member) {
                throw new RuntimeException('Member not found');
            }

            $savingsAccounts = $this->savingsAccountModel->getMemberAccounts($memberId);
            $shareAccount    = $this->shareAccountModel->findByMemberId($memberId);

            $savingsAccountsWithBalance = [];
            foreach ($savingsAccounts as $acc) {
                $savingsAccountsWithBalance[] = [
                    'id'             => (int)$acc['id'],
                    'account_number' => $acc['account_number'],
                    'account_type'   => $acc['account_type'],
                    'status'         => $acc['status'],
                    'balance'        => $this->savingsAccountModel->getAccountBalance((int)$acc['id']),
                ];
            }

            $shareAccountData = null;
            if ($shareAccount) {
                $shareAccountData = [
                    'id'             => (int)$shareAccount['id'],
                    'account_number' => $shareAccount['account_number'],
                    'status'         => $shareAccount['status'],
                    'balance'        => $this->shareAccountModel->getBalance((int)$shareAccount['id']),
                ];
            }

            $this->json([
                'success' => true,
                'member'  => [
                    'id'            => (int)$member['id'],
                    'member_number' => $member['member_number'],
                    'name'          => trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')),
                ],
                'savings_accounts' => $savingsAccountsWithBalance,
                'share_account'    => $shareAccountData,
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function savingsToShares(): void
    {
        $this->requireTransferAccess();

        try {
            if (!$this->verifyCsrf($_POST['csrf_token'] ?? '')) {
                throw new RuntimeException('Invalid security token');
            }

            $memberId         = (int)($_POST['member_id'] ?? 0);
            $savingsAccountId = (int)($_POST['savings_account_id'] ?? 0);
            $shareAccountId   = (int)($_POST['share_account_id'] ?? 0);
            $amount           = (float)($_POST['amount'] ?? 0);
            $narration        = trim((string)($_POST['narration'] ?? ''));

            if ($memberId <= 0) {
                throw new RuntimeException('Invalid member');
            }
            if ($savingsAccountId <= 0) {
                throw new RuntimeException('Invalid savings account');
            }
            if ($shareAccountId <= 0) {
                throw new RuntimeException('Invalid share account');
            }
            if ($amount <= 0) {
                throw new RuntimeException('Amount must be greater than zero');
            }
            if ($narration === '') {
                throw new RuntimeException('Narration is required');
            }

            $result = $this->transferService->transferSavingsToShares(
                $memberId,
                $savingsAccountId,
                $shareAccountId,
                $amount,
                $narration,
                (int)Session::get('user_id')
            );

            $this->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'data'    => $result,
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function sharesToSavings(): void
    {
        $this->requireTransferAccess();

        try {
            if (!$this->verifyCsrf($_POST['csrf_token'] ?? '')) {
                throw new RuntimeException('Invalid security token');
            }

            $memberId         = (int)($_POST['member_id'] ?? 0);
            $shareAccountId   = (int)($_POST['share_account_id'] ?? 0);
            $savingsAccountId = (int)($_POST['savings_account_id'] ?? 0);
            $amount           = (float)($_POST['amount'] ?? 0);
            $narration        = trim((string)($_POST['narration'] ?? ''));

            if ($memberId <= 0) {
                throw new RuntimeException('Invalid member');
            }
            if ($shareAccountId <= 0) {
                throw new RuntimeException('Invalid share account');
            }
            if ($savingsAccountId <= 0) {
                throw new RuntimeException('Invalid savings account');
            }
            if ($amount <= 0) {
                throw new RuntimeException('Amount must be greater than zero');
            }
            if ($narration === '') {
                throw new RuntimeException('Narration is required');
            }

            $result = $this->transferService->transferSharesToSavings(
                $memberId,
                $shareAccountId,
                $savingsAccountId,
                $amount,
                $narration,
                (int)Session::get('user_id')
            );

            $this->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'data'    => $result,
            ]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function history(): void
    {
        $this->requireTransferAccess();

        try {
            $memberId = isset($_GET['member_id']) ? (int)$_GET['member_id'] : null;
            $limit    = min(100, max(1, (int)($_GET['limit'] ?? 50)));
            $offset   = max(0, (int)($_GET['offset'] ?? 0));

            $db = Database::getInstance();

            $sql = "
                SELECT
                    st.id,
                    st.member_id,
                    st.transaction_type,
                    st.transaction_date,
                    st.amount,
                    st.reference_number,
                    st.created_at,
                    m.member_number,
                    CONCAT(m.first_name, ' ', m.last_name) AS member_name,
                    msa.account_number AS share_account_number,
                    je.entry_number AS journal_entry_number,
                    u.full_name AS processed_by_name
                FROM share_transactions st
                INNER JOIN members m ON m.id = st.member_id
                INNER JOIN member_share_accounts msa ON msa.id = st.share_account_id
                LEFT JOIN journal_entries je ON je.id = st.journal_entry_id
                LEFT JOIN users u ON u.id = st.processed_by
                WHERE st.transaction_type IN ('transfer_in', 'transfer_out')
            ";

            $params = [];
            if ($memberId !== null && $memberId > 0) {
                $sql .= ' AND st.member_id = ?';
                $params[] = $memberId;
            }

            $sql .= ' ORDER BY st.transaction_date DESC, st.id DESC LIMIT ? OFFSET ?';
            $params[] = $limit;
            $params[] = $offset;

            $transfers = $db->query($sql, $params)->fetchAll();

            $this->json(['success' => true, 'transfers' => $transfers]);
        } catch (Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}

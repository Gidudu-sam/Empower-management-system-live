<?php
/**
 * Savings Account Mapping Service
 * 
 * Centralized mapping between member_savings_accounts.account_type 
 * and General Ledger accounts.
 * 
 * This ensures each savings type posts to its correct GL account:
 * - compulsory     → 2020 Compulsory Savings (ID: 17)
 * - voluntary      → 2021 Voluntary Savings (ID: 80)
 * - fixed_deposit  → 2022 Fixed Savings (ID: 91)
 * - joint          → 2023 Joint Savings (ID: 92)
 * - corporate      → 2024 Corporate Savings (ID: 93)
 */
class SavingsAccountMapping
{
    /**
     * Map savings account type to GL account ID
     * 
     * @var array<string, int>
     */
    private static $ACCOUNT_TYPE_TO_GL = [
        'compulsory'     => 17,  // 2020 - Compulsory Savings
        'voluntary'      => 80,  // 2021 - Voluntary Savings
        'fixed_deposit'  => 91,  // 2022 - Fixed Savings
        'joint'          => 92,  // 2023 - Joint Savings
        'corporate'      => 93,  // 2024 - Corporate Savings
    ];
    
    /**
     * Get GL account ID for a given account type
     * 
     * @param string $accountType The savings account type (compulsory, voluntary, etc.)
     * @return int|null GL account ID, or null if not found
     */
    public static function getGLAccountId(string $accountType): ?int
    {
        return self::$ACCOUNT_TYPE_TO_GL[$accountType] ?? null;
    }
    
    /**
     * Get GL account ID by looking up the member_savings_account
     * 
     * @param int $savingsAccountId The member_savings_accounts.id
     * @return int|null GL account ID, or null if not found
     * @throws RuntimeException if savings account not found
     */
    public static function getGLAccountIdBySavingsAccount(int $savingsAccountId): ?int
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT account_type 
            FROM member_savings_accounts 
            WHERE id = ?
        ");
        $stmt->execute([$savingsAccountId]);
        $accountType = $stmt->fetchColumn();
        
        if (!$accountType) {
            throw new RuntimeException("Savings account ID {$savingsAccountId} not found in member_savings_accounts");
        }
        
        $glAccountId = self::getGLAccountId($accountType);
        
        if (!$glAccountId) {
            throw new RuntimeException("No GL account mapping found for account type: {$accountType}");
        }
        
        return $glAccountId;
    }
    
    /**
     * Get GL account details (code and name) for a savings account
     * 
     * @param int $savingsAccountId The member_savings_accounts.id
     * @return array{code: string, name: string, id: int}|null
     */
    public static function getGLAccountDetails(int $savingsAccountId): ?array
    {
        $glAccountId = self::getGLAccountIdBySavingsAccount($savingsAccountId);
        
        if (!$glAccountId) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT id, code, name 
            FROM accounts 
            WHERE id = ?
        ");
        $stmt->execute([$glAccountId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Validate that all required GL accounts exist
     * 
     * @return array{valid: bool, missing: array<string>}
     */
    public static function validateMapping(): array
    {
        $db = Database::getInstance()->getConnection();
        $missing = [];
        
        foreach (self::$ACCOUNT_TYPE_TO_GL as $accountType => $glAccountId) {
            $stmt = $db->prepare("SELECT id FROM accounts WHERE id = ?");
            $stmt->execute([$glAccountId]);
            
            if (!$stmt->fetch()) {
                $missing[] = "{$accountType} (expected GL account ID: {$glAccountId})";
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing' => $missing
        ];
    }
    
    /**
     * Get all savings GL account IDs
     * 
     * @return array<int> Array of GL account IDs
     */
    public static function getAllGLAccountIds(): array
    {
        return array_values(self::$ACCOUNT_TYPE_TO_GL);
    }
    
    /**
     * Get account type for a given GL account ID (reverse lookup)
     * 
     * @param int $glAccountId
     * @return string|null Account type, or null if not found
     */
    public static function getAccountTypeByGLId(int $glAccountId): ?string
    {
        $flip = array_flip(self::$ACCOUNT_TYPE_TO_GL);
        return $flip[$glAccountId] ?? null;
    }
}

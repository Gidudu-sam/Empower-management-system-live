<?php
/**
 * Database Connection Diagnostic
 */
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/config/database.php';

echo "<h2>Database Configuration Check</h2>";
echo "<pre>";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";
echo "DB_PORT: " . DB_PORT . "\n";
echo "DB_PASS: " . (DB_PASS ? '***SET***' : '(empty)') . "\n";
echo "\n";

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "✓ Connection successful!\n\n";
    
    // Check members count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM members WHERE status = 'active'");
    $members = $stmt->fetch();
    echo "Active Members: " . $members['count'] . "\n";
    
    // Check total savings
    $stmt = $pdo->query("SELECT SUM(balance) as total FROM member_savings_accounts");
    $savings = $stmt->fetch();
    echo "Total Savings Balance: Shs " . number_format($savings['total'] ?? 0) . "\n";
    
    // Check total shares
    $stmt = $pdo->query("SELECT SUM(shares * value_per_share) as total FROM member_share_accounts");
    $shares = $stmt->fetch();
    echo "Total Share Capital: Shs " . number_format($shares['total'] ?? 0) . "\n";
    
    // List all databases available
    echo "\n--- Available Databases ---\n";
    $stmt = $pdo->query("SHOW DATABASES");
    while ($row = $stmt->fetch()) {
        echo "- " . $row['Database'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed: " . $e->getMessage() . "\n";
}

echo "</pre>";

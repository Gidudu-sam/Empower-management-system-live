<?php
/**
 * Configuration Debug Script
 * IMPORTANT: This must be in /public/ to be accessible
 */
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== CONFIGURATION DEBUG ===\n\n";

// BEFORE any includes
echo "--- BEFORE bootstrap ---\n";
echo "getenv('DB_NAME'): " . (getenv('DB_NAME') ?: '(not set)') . "\n";
echo "\$_ENV['DB_NAME']: " . ($_ENV['DB_NAME'] ?? '(not set)') . "\n";
echo "\$_SERVER['DB_NAME']: " . ($_SERVER['DB_NAME'] ?? '(not set)') . "\n\n";

// Load bootstrap
require_once __DIR__ . '/../app/config/bootstrap_env.php';

echo "--- AFTER bootstrap_env.php ---\n";
echo "getenv('DB_NAME'): " . (getenv('DB_NAME') ?: '(not set)') . "\n";
echo "\$_ENV['DB_NAME']: " . ($_ENV['DB_NAME'] ?? '(not set)') . "\n";
echo "\$_SERVER['DB_NAME']: " . ($_SERVER['DB_NAME'] ?? '(not set)') . "\n";

// Check which secret file
$secretFile = empower_find_secret_file('db_credentials.php');
echo "\nSecret file found: " . ($secretFile ?: 'NONE') . "\n";
if ($secretFile) {
    echo "Secret file exists: " . (file_exists($secretFile) ? 'YES' : 'NO') . "\n";
    echo "Secret file readable: " . (is_readable($secretFile) ? 'YES' : 'NO') . "\n";
    echo "Secret file size: " . filesize($secretFile) . " bytes\n";
    echo "Secret file modified: " . date('Y-m-d H:i:s', filemtime($secretFile)) . "\n";
}

// Load database.php
require_once __DIR__ . '/../app/config/database.php';

echo "\n--- AFTER database.php ---\n";
echo "DB_NAME constant: " . (defined('DB_NAME') ? DB_NAME : '(not defined)') . "\n";
echo "DB_HOST constant: " . (defined('DB_HOST') ? DB_HOST : '(not defined)') . "\n";
echo "DB_USER constant: " . (defined('DB_USER') ? DB_USER : '(not defined)') . "\n";

// Show all EMPOWER_ constants
$allConstants = get_defined_constants(true);
$userConstants = $allConstants['user'] ?? [];
echo "\n--- All EMPOWER_ constants ---\n";
foreach ($userConstants as $name => $value) {
    if (strpos($name, 'EMPOWER_') === 0) {
        echo "$name = " . (is_string($value) ? $value : var_export($value, true)) . "\n";
    }
}

echo "\n=== END DEBUG ===\n";

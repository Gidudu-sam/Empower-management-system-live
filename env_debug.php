<?php
/**
 * Debug environment variables BEFORE any bootstrap
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== Environment Variables DEBUG ===\n\n";

echo "--- getenv() checks ---\n";
echo "DB_HOST from getenv: " . (getenv('DB_HOST') ?: '(not set)') . "\n";
echo "DB_NAME from getenv: " . (getenv('DB_NAME') ?: '(not set)') . "\n";
echo "DB_USER from getenv: " . (getenv('DB_USER') ?: '(not set)') . "\n";
echo "DB_PASS from getenv: " . (getenv('DB_PASS') !== false ? '***SET***' : '(not set)') . "\n";

echo "\n--- \$_ENV checks ---\n";
echo "DB_HOST from \$_ENV: " . ($_ENV['DB_HOST'] ?? '(not set)') . "\n";
echo "DB_NAME from \$_ENV: " . ($_ENV['DB_NAME'] ?? '(not set)') . "\n";
echo "DB_USER from \$_ENV: " . ($_ENV['DB_USER'] ?? '(not set)') . "\n";

echo "\n--- \$_SERVER checks ---\n";
echo "DB_HOST from \$_SERVER: " . ($_SERVER['DB_HOST'] ?? '(not set)') . "\n";
echo "DB_NAME from \$_SERVER: " . ($_SERVER['DB_NAME'] ?? '(not set)') . "\n";
echo "DB_USER from \$_SERVER: " . ($_SERVER['DB_USER'] ?? '(not set)') . "\n";

echo "\n--- Now loading bootstrap ---\n";
require_once __DIR__ . '/app/config/bootstrap_env.php';

echo "\n--- After bootstrap_env.php ---\n";
echo "DB_HOST from getenv: " . (getenv('DB_HOST') ?: '(not set)') . "\n";
echo "DB_NAME from getenv: " . (getenv('DB_NAME') ?: '(not set)') . "\n";
echo "DB_USER from getenv: " . (getenv('DB_USER') ?: '(not set)') . "\n";

echo "\n--- Loading database.php ---\n";
require_once __DIR__ . '/app/config/database.php';

echo "\n--- Final DB Constants ---\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : '(not defined)') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : '(not defined)') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : '(not defined)') . "\n";
echo "DB_PORT: " . (defined('DB_PORT') ? DB_PORT : '(not defined)') . "\n";

echo "\n--- Secret file search ---\n";
$secretFile = empower_find_secret_file('db_credentials.php');
if ($secretFile) {
    echo "Found db_credentials.php at: $secretFile\n";
    echo "File exists: " . (file_exists($secretFile) ? 'Yes' : 'No') . "\n";
    echo "File readable: " . (is_readable($secretFile) ? 'Yes' : 'No') . "\n";
    echo "File modified: " . date('Y-m-d H:i:s', filemtime($secretFile)) . "\n";
    echo "\nFile contents:\n";
    echo file_get_contents($secretFile);
} else {
    echo "No db_credentials.php found\n";
}

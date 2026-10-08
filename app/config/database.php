<?php
/**
 * Database Configuration
 *
 * DirectAdmin: put credentials in /home/USERNAME/empower_secrets/db_credentials.php
 * or /home/USERNAME/empower_secrets/.env (see empower_secrets/ examples).
 *
 * Also supports process env vars: DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT
 */

require_once __DIR__ . '/bootstrap_env.php';

// TEMPORARY DEBUG: Force load from secret file, ignore environment variables
$secretFile = empower_find_secret_file('db_credentials.php');
if ($secretFile) {
    require $secretFile;
    $dbHost = defined('EMPOWER_DB_HOST') ? EMPOWER_DB_HOST : 'localhost';
    $dbName = defined('EMPOWER_DB_NAME') ? EMPOWER_DB_NAME : '';
    $dbUser = defined('EMPOWER_DB_USER') ? EMPOWER_DB_USER : '';
    $dbPass = defined('EMPOWER_DB_PASS') ? EMPOWER_DB_PASS : '';
} else {
    // Fallback to environment variables if secret file not found
    $dbHost = getenv('DB_HOST') ?: 'localhost';
    $dbName = getenv('DB_NAME') ?: '';
    $dbUser = getenv('DB_USER') ?: '';
    $dbPass = getenv('DB_PASS') ?: '';
}

define('DB_HOST',    $dbHost ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT') ?: '3306');
define('DB_NAME',    $dbName ?: '');
define('DB_USER',    $dbUser ?: '');
define('DB_PASS',    $dbPass !== false ? (string)$dbPass : '');
define('DB_CHARSET', 'utf8mb4');

unset($dbHost, $dbName, $dbUser, $dbPass, $secretFile);

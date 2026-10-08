<?php
/**
 * Email / SMTP Configuration
 *
 * DirectAdmin: /home/USERNAME/empower_secrets/smtp_credentials.php or .env
 * Callers: index.php require; SMTP used by mail-sending services/controllers.
 * Schema: EMPOWER_SMTP_HOST/PORT/USERNAME/PASSWORD/FROM/FROM_NAME (strings/int).
 * User instruction: fix the system to be uploadable to directadmin
 */

require_once __DIR__ . '/bootstrap_env.php';

$smtpHost = getenv('SMTP_HOST');
$smtpPort = getenv('SMTP_PORT');
$smtpUsername = getenv('SMTP_USERNAME');
$smtpPassword = getenv('SMTP_PASSWORD');
$smtpFrom = getenv('SMTP_FROM');
$smtpFromName = getenv('SMTP_FROM_NAME');

if (!$smtpHost || !$smtpUsername) {
    $secretFile = empower_find_secret_file('smtp_credentials.php');
    if ($secretFile) {
        require $secretFile;
        $smtpHost = defined('EMPOWER_SMTP_HOST') ? EMPOWER_SMTP_HOST : 'localhost';
        $smtpPort = defined('EMPOWER_SMTP_PORT') ? EMPOWER_SMTP_PORT : 587;
        $smtpUsername = defined('EMPOWER_SMTP_USERNAME') ? EMPOWER_SMTP_USERNAME : '';
        $smtpPassword = defined('EMPOWER_SMTP_PASSWORD') ? EMPOWER_SMTP_PASSWORD : '';
        $smtpFrom = defined('EMPOWER_SMTP_FROM') ? EMPOWER_SMTP_FROM : 'noreply@localhost';
        $smtpFromName = defined('EMPOWER_SMTP_FROM_NAME') ? EMPOWER_SMTP_FROM_NAME : 'Empower Investment Club';
    } else {
        $smtpHost = 'localhost';
        $smtpPort = 587;
        $smtpUsername = '';
        $smtpPassword = '';
        $smtpFrom = 'noreply@localhost';
        $smtpFromName = 'Empower Investment Club';
    }
}

define('SMTP_HOST',      $smtpHost ?: 'localhost');
define('SMTP_PORT',      (int)($smtpPort ?: 587));
define('SMTP_USERNAME',  $smtpUsername ?: '');
define('SMTP_PASSWORD',  $smtpPassword !== false ? (string)$smtpPassword : '');
define('SMTP_FROM',      $smtpFrom ?: 'noreply@localhost');
define('SMTP_FROM_NAME', $smtpFromName ?: 'Empower Investment Club');

// Aliases for backward compatibility with MailerService
define('SMTP_USER', SMTP_USERNAME);
define('SMTP_PASS', SMTP_PASSWORD);

unset($smtpHost, $smtpPort, $smtpUsername, $smtpPassword, $smtpFrom, $smtpFromName, $secretFile);

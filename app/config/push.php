<?php
/**
 * Push notification (Web Push / VAPID) configuration — Stage 12-D.
 *
 * DirectAdmin: /home/USERNAME/empower_secrets/push_credentials.php or .env
 * Callers: index.php require; PushDeliveryService / web-push.
 * Schema: EMPOWER_VAPID_PUBLIC_KEY, EMPOWER_VAPID_PRIVATE_KEY (strings).
 * User instruction: fix the system to be uploadable to directadmin
 */

require_once __DIR__ . '/bootstrap_env.php';

$vapidPublicKey = getenv('VAPID_PUBLIC_KEY');
$vapidPrivateKey = getenv('VAPID_PRIVATE_KEY');

if (!$vapidPublicKey || !$vapidPrivateKey) {
    $secretFile = empower_find_secret_file('push_credentials.php');
    if ($secretFile) {
        require $secretFile;
        $vapidPublicKey = defined('EMPOWER_VAPID_PUBLIC_KEY') ? EMPOWER_VAPID_PUBLIC_KEY : '';
        $vapidPrivateKey = defined('EMPOWER_VAPID_PRIVATE_KEY') ? EMPOWER_VAPID_PRIVATE_KEY : '';
    }
}

define('VAPID_PUBLIC_KEY',  $vapidPublicKey ?: '');
define('VAPID_PRIVATE_KEY', $vapidPrivateKey ?: '');

unset($vapidPublicKey, $vapidPrivateKey, $secretFile);

define('VAPID_SUBJECT', APP_URL);

// Windows/XAMPP only — do not force a Windows openssl.cnf path on Linux/DirectAdmin
if (PHP_OS_FAMILY === 'Windows' && !getenv('OPENSSL_CONF')) {
    putenv('OPENSSL_CONF=C:\\xampp\\php\\extras\\openssl\\openssl.cnf');
}

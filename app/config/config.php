<?php
/**
 * Application Configuration
 *
 * Callers: index.php require_once. Constants used app-wide (APP_URL, APP_ENV, paths).
 * DirectAdmin: APP_URL/APP_ENV via /home/USERNAME/empower_secrets/app_config.php or .env.
 * User instruction: fix the system to be uploadable to directadmin
 */

require_once __DIR__ . '/bootstrap_env.php';

// Environment (override via environment variable / secrets)
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_NAME', 'Empower Investment Club');
define('APP_VERSION', '1.0.0');

// Application URL (CRITICAL: set on the server for your DirectAdmin domain)
define('APP_URL', rtrim(getenv('APP_URL') ?: 'http://localhost/empower%20management%20sys.live', '/'));

// Paths
define('ROOT_PATH',   dirname(__DIR__, 2));
define('APP_PATH',    ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEW_PATH',   APP_PATH  . '/views');
define('CORE_PATH',   ROOT_PATH . '/core');

// Session
define('SESSION_NAME',     'empower_session');
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Session security (production only)
if (APP_ENV === 'production') {
    ini_set('session.cookie_httponly', '1');  // Blocks JavaScript access (XSS protection)
    ini_set('session.cookie_secure', '1');    // HTTPS only
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
}

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Error reporting
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT); // log everything, display nothing
}

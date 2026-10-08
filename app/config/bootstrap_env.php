<?php
/**
 * DirectAdmin / shared-hosting environment bootstrap.
 *
 * Loads APP_, DB_, SMTP_, and VAPID_ settings from (first match wins per key):
 *   1. Already-set environment variables (SetEnv, PHP-FPM, shell)
 *   2. PHP secrets files under empower_secrets/ (project root or home)
 *   3. Optional KEY=VALUE .env next to those secrets
 *
 * Prefer secrets outside the document root on DirectAdmin:
 *   /home/USERNAME/empower_secrets/
 * Local / uploadable layout also supports:
 *   <project>/empower_secrets/
 */

if (defined('EMPOWER_ENV_BOOTSTRAPPED')) {
    return;
}
define('EMPOWER_ENV_BOOTSTRAPPED', true);

/**
 * @return list<string>
 */
function empower_secret_dirs(): array
{
    $dirs = [];
    $home = getenv('HOME') ?: (isset($_SERVER['HOME']) ? (string)$_SERVER['HOME'] : '');
    if ($home !== '') {
        $dirs[] = rtrim($home, '/\\') . '/empower_secrets';
    }
    // Common DirectAdmin account home when HOME is unset under Apache
    if (!empty($_SERVER['USER'])) {
        $dirs[] = '/home/' . preg_replace('/[^a-zA-Z0-9._-]/', '', (string)$_SERVER['USER']) . '/empower_secrets';
    }
    // Callers: empower_secret_dirs() used by database.php / bootstrap_env.php.
    // Added production DirectAdmin home for empowermanagementsystem.com.
    $dirs[] = '/home/idkwqcahoa/empower_secrets';
    $dirs[] = '/home/empowermanage/empower_secrets';
    // Project-root folder (local + DirectAdmin upload layout)
    $dirs[] = dirname(__DIR__, 2) . '/empower_secrets';
    $dirs[] = 'C:\\xampp\\empower_secrets';

    $unique = [];
    foreach ($dirs as $dir) {
        if ($dir !== '' && !in_array($dir, $unique, true)) {
            $unique[] = $dir;
        }
    }
    return $unique;
}

/**
 * @return string|null
 */
function empower_find_secret_file(string $filename): ?string
{
    foreach (empower_secret_dirs() as $dir) {
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        if (is_file($path)) {
            return $path;
        }
    }
    return null;
}

/**
 * Load KEY=VALUE pairs into the process environment when unset.
 */
function empower_load_dotenv_file(string $path): void
{
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === '' || getenv($key) !== false) {
            continue;
        }
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

foreach (empower_secret_dirs() as $dir) {
    $envFile = $dir . DIRECTORY_SEPARATOR . '.env';
    if (is_file($envFile)) {
        empower_load_dotenv_file($envFile);
        break;
    }
}

// Optional PHP app overrides (APP_URL / APP_ENV) for hosts without SetEnv
$appConfig = empower_find_secret_file('app_config.php');
if ($appConfig !== null) {
    require $appConfig;
    if (defined('EMPOWER_APP_ENV') && getenv('APP_ENV') === false) {
        putenv('APP_ENV=' . EMPOWER_APP_ENV);
        $_ENV['APP_ENV'] = EMPOWER_APP_ENV;
        $_SERVER['APP_ENV'] = EMPOWER_APP_ENV;
    }
    if (defined('EMPOWER_APP_URL') && getenv('APP_URL') === false) {
        putenv('APP_URL=' . EMPOWER_APP_URL);
        $_ENV['APP_URL'] = EMPOWER_APP_URL;
        $_SERVER['APP_URL'] = EMPOWER_APP_URL;
    }
}

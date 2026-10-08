<?php
/**
 * Temporary production diagnostics for cPanel deploy issues.
 * Callers: index.php?page=health (Auth not required).
 * Checks DB connectivity and users/roles tables used by login.
 * User: HTTP ERROR 500 on https://empowermanagementsystem.com login POST.
 */
class HealthController extends Controller
{
    public function index(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $lines = [];
        $lines[] = 'Empower health check';
        $lines[] = 'Time: ' . date('c');
        $lines[] = 'PHP: ' . PHP_VERSION;
        $lines[] = 'APP_ENV: ' . (defined('APP_ENV') ? APP_ENV : '(unset)');
        $lines[] = 'APP_URL: ' . (defined('APP_URL') ? APP_URL : '(unset)');
        $lines[] = 'DB_HOST: ' . (defined('DB_HOST') ? DB_HOST : '(unset)');
        $lines[] = 'DB_NAME: ' . (defined('DB_NAME') ? DB_NAME : '(unset)');
        $lines[] = 'DB_USER: ' . (defined('DB_USER') ? DB_USER : '(unset)');
        
        // Debug: Show which secret file was loaded
        $secretFile = function_exists('empower_find_secret_file') ? empower_find_secret_file('db_credentials.php') : null;
        if ($secretFile) {
            $lines[] = 'Secret file: ' . $secretFile;
            $lines[] = 'Secret file modified: ' . date('Y-m-d H:i:s', @filemtime($secretFile));
            $lines[] = 'Secret file size: ' . @filesize($secretFile) . ' bytes';
        } else {
            $lines[] = 'Secret file: NOT FOUND';
        }
        
        // Debug: Show ALL defined constants with EMPOWER prefix
        $allConstants = get_defined_constants(true);
        $userConstants = $allConstants['user'] ?? [];
        $empowerConstants = array_filter($userConstants, function($key) {
            return strpos($key, 'EMPOWER_') === 0;
        }, ARRAY_FILTER_USE_KEY);
        $lines[] = 'EMPOWER constants: ' . (empty($empowerConstants) ? 'NONE' : implode(', ', array_keys($empowerConstants)));
        
        // Debug: Show defined constants from the secret file
        if (defined('EMPOWER_DB_NAME')) {
            $lines[] = 'EMPOWER_DB_NAME constant: ' . EMPOWER_DB_NAME;
        } else {
            $lines[] = 'EMPOWER_DB_NAME constant: NOT DEFINED';
        }
        if (defined('EMPOWER_DB_HOST')) {
            $lines[] = 'EMPOWER_DB_HOST constant: ' . EMPOWER_DB_HOST;
        }

        try {
            $pdo = Database::getInstance()->getConnection();
            $lines[] = 'DB connect: OK';
            $dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
            $lines[] = 'Selected database: ' . ($dbName !== '' ? $dbName : '(none)');

            foreach (['users', 'roles', 'remember_tokens', 'activity_logs', 'savings', 'share_transactions'] as $table) {
                try {
                    $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
                    $lines[] = "Table {$table}: OK ({$count} rows)";
                } catch (Throwable $e) {
                    $lines[] = "Table {$table}: MISSING/ERROR — " . $e->getMessage();
                }
            }

            try {
                $stmt = $pdo->query(
                    "SELECT u.id, u.email, r.name AS role_name
                     FROM users u
                     JOIN roles r ON r.id = u.role_id
                     LIMIT 3"
                );
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $lines[] = 'Login join users+roles: OK (' . count($rows) . ' sample row(s))';
            } catch (Throwable $e) {
                $lines[] = 'Login join users+roles: FAIL — ' . $e->getMessage();
            }
        } catch (Throwable $e) {
            $lines[] = 'DB connect: FAIL — ' . $e->getMessage();
        }

        echo implode("\n", $lines) . "\n";
        exit;
    }
}

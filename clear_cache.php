<?php
/**
 * Clear PHP OPcache
 */
header('Content-Type: text/plain; charset=utf-8');

echo "=== PHP Cache Clear ===\n\n";

// Clear OPcache
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✓ OPcache cleared successfully\n";
    } else {
        echo "✗ Failed to clear OPcache\n";
    }
} else {
    echo "ℹ OPcache not enabled\n";
}

// Clear realpath cache
clearstatcache(true);
echo "✓ Realpath cache cleared\n";

// Show current opcache status
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    if ($status !== false) {
        echo "\nOPcache Status:\n";
        echo "- Enabled: " . ($status['opcache_enabled'] ? 'Yes' : 'No') . "\n";
        echo "- Cache full: " . ($status['cache_full'] ? 'Yes' : 'No') . "\n";
    }
}

echo "\n=== Now test: index.php?page=health ===\n";

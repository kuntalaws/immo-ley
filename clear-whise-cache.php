<?php
// Clear Whise API caches
require_once('wp-config.php');

echo "<h1>Clearing Whise API Caches</h1>";

// Get client ID
$client_id = get_field('whise_client_id', 'option');

// Clear all Whise caches
$caches_cleared = 0;

// Clear auth token cache
if (delete_transient('whise_auth_token')) {
    echo "<p>✅ Cleared auth token cache</p>";
    $caches_cleared++;
}

// Clear client token cache
if (delete_transient('whise_client_token_' . $client_id)) {
    echo "<p>✅ Cleared client token cache</p>";
    $caches_cleared++;
}

// Clear cities cache
if (delete_transient('whise_cities')) {
    echo "<p>✅ Cleared cities cache</p>";
    $caches_cleared++;
}

// Clear estates caches
global $wpdb;
$estates_caches = $wpdb->get_results("
    SELECT option_name 
    FROM {$wpdb->options} 
    WHERE option_name LIKE '_transient_whise_estates_%'
");

if ($estates_caches) {
    foreach ($estates_caches as $cache) {
        $cache_name = str_replace('_transient_', '', $cache->option_name);
        delete_transient($cache_name);
    }
    echo "<p>✅ Cleared " . count($estates_caches) . " estates caches</p>";
    $caches_cleared += count($estates_caches);
}

if ($caches_cleared == 0) {
    echo "<p>No caches found to clear</p>";
} else {
    echo "<p><strong>Total caches cleared:</strong> " . $caches_cleared . "</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<p>1. <a href='?debug=1'>Add ?debug=1 to any page to see filter debug info</a></p>";
echo "<p>2. Check the WordPress error logs for detailed API communication</p>";
echo "<p>3. Test the filters again</p>";

echo "<h2>Test URL:</h2>";
echo "<p><a href='/?purpose=1&category=2&price_min=1000000&price_max=1500000&debug=1'>Test with filters and debug</a></p>";
?> 
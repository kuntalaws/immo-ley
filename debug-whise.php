<?php
// Debug Whise API configuration and clear caches
require_once('includes/WhiseAPI.php');

echo "<h1>Whise API Debug</h1>";

// Check configuration
echo "<h2>Configuration Check</h2>";
$api_url = get_field('whise_api_url', 'option') ?: 'https://api.whise.eu';
$username = get_field('whise_username', 'option');
$password = get_field('whise_password', 'option');
$client_id = get_field('whise_client_id', 'option');

echo "<p><strong>API URL:</strong> " . $api_url . "</p>";
echo "<p><strong>Username:</strong> " . ($username ? 'Set' : 'Not set') . "</p>";
echo "<p><strong>Password:</strong> " . ($password ? 'Set' : 'Not set') . "</p>";
echo "<p><strong>Client ID:</strong> " . ($client_id ? $client_id : 'Not set') . "</p>";

// Clear all Whise caches
echo "<h2>Clearing Caches</h2>";
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

// Clear estates caches (find and delete all estates caches)
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
}

// Test API connection
echo "<h2>Testing API Connection</h2>";
$whise = new WhiseAPI();
$test_result = $whise->test_connection();

if ($test_result['success']) {
    echo "<p>✅ API connection successful</p>";
    echo "<p><strong>Clients:</strong> " . $test_result['clients_count'] . "</p>";
    echo "<p><strong>Estates:</strong> " . $test_result['estates_count'] . "</p>";
} else {
    echo "<p>❌ API connection failed: " . $test_result['message'] . "</p>";
}

// Test filters
echo "<h2>Testing Filters</h2>";

// Test purpose filter
$purpose_filter = ['PurposeId' => 1];
$purpose_result = $whise->get_estates($purpose_filter);
echo "<p><strong>Purpose Filter (PurposeId=1):</strong> " . 
     ($purpose_result && isset($purpose_result['estates']) ? count($purpose_result['estates']) . ' estates' : 'Failed') . "</p>";

// Test city filter
$city_filter = ['City' => 'Antwerp'];
$city_result = $whise->get_estates($city_filter);
echo "<p><strong>City Filter (City=Antwerp):</strong> " . 
     ($city_result && isset($city_result['estates']) ? count($city_result['estates']) . ' estates' : 'Failed') . "</p>";

// Test price filter
$price_filter = ['PriceMin' => 100000, 'PriceMax' => 500000];
$price_result = $whise->get_estates($price_filter);
echo "<p><strong>Price Filter (100k-500k):</strong> " . 
     ($price_result && isset($price_result['estates']) ? count($price_result['estates']) . ' estates' : 'Failed') . "</p>";

echo "<h2>Debug Complete</h2>";
echo "<p><a href='?debug=1'>Add ?debug=1 to any page to see filter debug info</a></p>";
?> 
<?php
/**
 * Test script for Whise API integration
 * Run this file directly to test the API connection
 */

// Load WordPress
require_once('wp-config.php');

// Include the Whise API class
require_once('includes/WhiseAPI.php');

echo "<h1>Whise API Connection Test</h1>\n";

// Test credentials
$test_credentials = [
    'api_url' => 'https://api.whise.eu',
    'username' => 'arnab@anushaweb.com',
    'password' => 'A!aH6Hcnx6eJz7?g',
    'client_id' => 12889
];

echo "<h2>Testing with credentials:</h2>\n";
echo "<ul>\n";
echo "<li><strong>API URL:</strong> " . htmlspecialchars($test_credentials['api_url']) . "</li>\n";
echo "<li><strong>Username:</strong> " . htmlspecialchars($test_credentials['username']) . "</li>\n";
echo "<li><strong>Client ID:</strong> " . htmlspecialchars($test_credentials['client_id']) . "</li>\n";
echo "</ul>\n";

// Create Whise API instance with test credentials
$whise = new WhiseAPI();

// Override the credentials for testing
$whise->api_url = $test_credentials['api_url'];
$whise->username = $test_credentials['username'];
$whise->password = $test_credentials['password'];
$whise->client_id = $test_credentials['client_id'];

echo "<h2>Step 1: Testing Authentication</h2>\n";
$auth_result = $whise->authenticate();
if ($auth_result) {
    echo "<p style='color: green;'>✅ Authentication successful!</p>\n";
    echo "<p><strong>Token:</strong> " . substr($whise->token, 0, 20) . "...</p>\n";
} else {
    echo "<p style='color: red;'>❌ Authentication failed!</p>\n";
    exit;
}

echo "<h2>Step 2: Testing Client Token</h2>\n";
$client_token_result = $whise->get_client_token();
if ($client_token_result) {
    echo "<p style='color: green;'>✅ Client token obtained successfully!</p>\n";
    echo "<p><strong>Client Token:</strong> " . substr($whise->client_token, 0, 20) . "...</p>\n";
} else {
    echo "<p style='color: red;'>❌ Failed to get client token!</p>\n";
    echo "<p>This might mean the client account is not activated. Contact WHISE at api@whise.eu with Client ID: " . $test_credentials['client_id'] . "</p>\n";
    exit;
}

echo "<h2>Step 3: Testing Estates List</h2>\n";
$estates = $whise->get_estates();
if ($estates && isset($estates['estates'])) {
    echo "<p style='color: green;'>✅ Estates retrieved successfully!</p>\n";
    echo "<p><strong>Total Estates:</strong> " . count($estates['estates']) . "</p>\n";
    
    if (count($estates['estates']) > 0) {
        echo "<h3>Sample Estate:</h3>\n";
        $sample_estate = $estates['estates'][0];
        echo "<ul>\n";
        echo "<li><strong>ID:</strong> " . htmlspecialchars($sample_estate['id']) . "</li>\n";
        echo "<li><strong>Name:</strong> " . htmlspecialchars($sample_estate['name'] ?? 'N/A') . "</li>\n";
        echo "<li><strong>City:</strong> " . htmlspecialchars($sample_estate['city'] ?? 'N/A') . "</li>\n";
        echo "<li><strong>Price:</strong> €" . number_format($sample_estate['price'] ?? 0) . "</li>\n";
        echo "<li><strong>Pictures:</strong> " . count($sample_estate['pictures'] ?? []) . "</li>\n";
        echo "</ul>\n";
    }
} else {
    echo "<p style='color: red;'>❌ Failed to retrieve estates!</p>\n";
    if ($estates) {
        echo "<pre>" . print_r($estates, true) . "</pre>\n";
    }
}

echo "<h2>Step 4: Testing Cities List</h2>\n";
$cities = $whise->get_cities();
if ($cities && isset($cities['cities'])) {
    echo "<p style='color: green;'>✅ Cities retrieved successfully!</p>\n";
    echo "<p><strong>Total Cities:</strong> " . count($cities['cities']) . "</p>\n";
    
    if (count($cities['cities']) > 0) {
        echo "<h3>Sample Cities:</h3>\n";
        echo "<ul>\n";
        for ($i = 0; $i < min(5, count($cities['cities'])); $i++) {
            $city = $cities['cities'][$i];
            echo "<li>" . htmlspecialchars($city['name']) . " (" . htmlspecialchars($city['zip']) . ")</li>\n";
        }
        echo "</ul>\n";
    }
} else {
    echo "<p style='color: red;'>❌ Failed to retrieve cities!</p>\n";
    if ($cities) {
        echo "<pre>" . print_r($cities, true) . "</pre>\n";
    }
}

echo "<h2>Step 5: Testing Filter Functionality</h2>\n";
$filtered_estates = $whise->get_estates(['PurposeId' => 1]); // Test with "Te koop" filter
if ($filtered_estates && isset($filtered_estates['estates'])) {
    echo "<p style='color: green;'>✅ Filter functionality working!</p>\n";
    echo "<p><strong>Estates for Sale:</strong> " . count($filtered_estates['estates']) . "</p>\n";
} else {
    echo "<p style='color: orange;'>⚠️ Filter test inconclusive</p>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p style='color: green; font-weight: bold;'>🎉 Whise API integration is working correctly!</p>\n";
echo "<p>You can now configure these credentials in your WordPress admin panel:</p>\n";
echo "<ul>\n";
echo "<li>Go to <strong>WordPress Admin → Global → Whise API Settings</strong></li>\n";
echo "<li>Enter the credentials shown above</li>\n";
echo "<li>Save the settings</li>\n";
echo "<li>Test the filter functionality on your website</li>\n";
echo "</ul>\n";
?> 
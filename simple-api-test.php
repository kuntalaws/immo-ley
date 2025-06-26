<?php
/**
 * Simple API Test - Direct API calls
 */

// Load WordPress
require_once('wp-config.php');

// Include the Whise API class
require_once('includes/WhiseAPI.php');

echo "<h1>🔍 Simple API Test</h1>\n";

// Test 1: Basic API call
echo "<h2>1. Testing Direct API Call</h2>\n";

$whise = new WhiseAPI();

// Test authentication
echo "<h3>Authentication Test:</h3>\n";
$auth_result = $whise->authenticate();
echo "Auth result: " . ($auth_result ? "SUCCESS" : "FAILED") . "<br>\n";

if ($auth_result) {
    // Test client token
    echo "<h3>Client Token Test:</h3>\n";
    $client_token_result = $whise->get_client_token();
    echo "Client token result: " . ($client_token_result ? "SUCCESS" : "FAILED") . "<br>\n";
    
    if ($client_token_result) {
        // Test estates with minimal request
        echo "<h3>Estates Test (Minimal Request):</h3>\n";
        
        // Make a direct API call to see what happens
        $api_url = get_field('whise_api_url', 'option') ?: 'https://api.whise.eu';
        $client_token = get_transient('whise_client_token_' . get_field('whise_client_id', 'option'));
        
        echo "API URL: $api_url<br>\n";
        echo "Client Token: " . (empty($client_token) ? "NOT FOUND" : "FOUND") . "<br>\n";
        
        if ($client_token) {
            $response = wp_remote_post($api_url . '/v1/estates/list', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $client_token
                ],
                'body' => json_encode([
                    'Field' => [
                        'excluded' => ['longDescription']
                    ]
                ]),
                'timeout' => 30
            ]);
            
            if (is_wp_error($response)) {
                echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
                echo "<strong>❌ Error:</strong> " . $response->get_error_message();
                echo "</div>";
            } else {
                $status_code = wp_remote_retrieve_response_code($response);
                $body = wp_remote_retrieve_body($response);
                
                echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
                echo "<strong>Status Code:</strong> $status_code<br>\n";
                echo "<strong>Response Body:</strong><br>\n";
                echo "<pre style='font-size: 12px; overflow-x: auto;'>" . htmlspecialchars($body) . "</pre>";
                echo "</div>";
                
                $data = json_decode($body, true);
                if ($data) {
                    if (isset($data['estates'])) {
                        echo "<div style='background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px;'>";
                        echo "<strong>✅ Success!</strong> Found " . count($data['estates']) . " estates";
                        echo "</div>";
                    } else {
                        echo "<div style='background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px;'>";
                        echo "<strong>⚠️ Warning:</strong> No 'estates' key in response";
                        echo "</div>";
                    }
                } else {
                    echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
                    echo "<strong>❌ Error:</strong> Failed to decode JSON response";
                    echo "</div>";
                }
            }
        }
    }
} else {
    echo "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px;'>";
    echo "<strong>❌ Authentication failed</strong>";
    echo "</div>";
}

// Test 2: Check if there are any properties at all
echo "<h2>2. Testing Different API Endpoints</h2>\n";

if ($auth_result && $client_token_result) {
    $client_token = get_transient('whise_client_token_' . get_field('whise_client_id', 'option'));
    
    // Test cities endpoint
    echo "<h3>Cities Test:</h3>\n";
    $cities_response = wp_remote_post($api_url . '/v1/estates/usedcities/list', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $client_token
        ],
        'body' => json_encode([]),
        'timeout' => 30
    ]);
    
    if (!is_wp_error($cities_response)) {
        $cities_status = wp_remote_retrieve_response_code($cities_response);
        $cities_body = wp_remote_retrieve_body($cities_response);
        echo "Cities Status: $cities_status<br>\n";
        echo "Cities Response: <pre style='font-size: 12px;'>" . htmlspecialchars($cities_body) . "</pre>\n";
    }
    
    // Test owned estates endpoint (might have different permissions)
    echo "<h3>Owned Estates Test:</h3>\n";
    $owned_response = wp_remote_post($api_url . '/v1/estates/owned/list', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $client_token
        ],
        'body' => json_encode([
            'Field' => [
                'excluded' => ['longDescription']
            ]
        ]),
        'timeout' => 30
    ]);
    
    if (!is_wp_error($owned_response)) {
        $owned_status = wp_remote_retrieve_response_code($owned_response);
        $owned_body = wp_remote_retrieve_body($owned_response);
        echo "Owned Estates Status: $owned_status<br>\n";
        echo "Owned Estates Response: <pre style='font-size: 12px;'>" . htmlspecialchars($owned_body) . "</pre>\n";
    }
}

echo "<h2>📋 Analysis</h2>\n";
echo "<ul style='line-height: 1.6;'>";
echo "<li><strong>If status code is 200 but no estates:</strong> Your account has no properties</li>";
echo "<li><strong>If status code is 401/403:</strong> Permission issue - contact WHISE</li>";
echo "<li><strong>If status code is 404:</strong> API endpoint issue</li>";
echo "<li><strong>If status code is 500:</strong> Server error - try again later</li>";
echo "</ul>";
?> 
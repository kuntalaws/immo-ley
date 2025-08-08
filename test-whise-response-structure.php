<?php
/**
 * Test script to examine Whise API response structure
 * This will help us understand what fields are actually returned
 */

// Load WordPress
require_once('wp-load.php');

// Include Whise API
require_once(get_template_directory() . '/includes/WhiseAPI.php');

echo "<h1>🔍 Whise API Response Structure Analysis</h1>";

// Initialize Whise API
$whise = new WhiseAPI();

if (!$whise) {
    echo "<p style='color: red;'>❌ Could not initialize Whise API</p>";
    exit;
}

// Test 1: Get a single estate with minimal filters to see the structure
echo "<h2>Test 1: Single Estate Response Structure</h2>";

// Try to get just one estate
$test_request = [
    'Field' => [
        'excluded' => ['longDescription']
    ],
    'Limit' => 1,
    'Offset' => 0
];

echo "<h3>Request Body:</h3>";
echo "<pre>" . json_encode($test_request, JSON_PRETTY_PRINT) . "</pre>";

$result = $whise->debug_api_call($test_request);

if ($result && isset($result['data']['estates']) && count($result['data']['estates']) > 0) {
    $estate = $result['data']['estates'][0];
    
    echo "<h3>First Estate Structure:</h3>";
    echo "<pre>" . json_encode($estate, JSON_PRETTY_PRINT) . "</pre>";
    
    echo "<h3>Available Fields:</h3>";
    echo "<ul>";
    foreach ($estate as $key => $value) {
        $type = is_array($value) ? 'array(' . count($value) . ')' : gettype($value);
        echo "<li><strong>" . esc_html($key) . "</strong>: " . esc_html($type) . " = " . esc_html(json_encode($value)) . "</li>";
    }
    echo "</ul>";
    
    // Check for purpose and category fields
    echo "<h3>Purpose/Category Field Check:</h3>";
    $purpose_fields = ['purposeId', 'purpose', 'PurposeId', 'Purpose'];
    $category_fields = ['categoryId', 'category', 'CategoryId', 'Category'];
    
    echo "<h4>Purpose Fields:</h4>";
    foreach ($purpose_fields as $field) {
        $value = $estate[$field] ?? 'NOT FOUND';
        echo "<p><strong>" . esc_html($field) . ":</strong> " . esc_html($value) . "</p>";
    }
    
    echo "<h4>Category Fields:</h4>";
    foreach ($category_fields as $field) {
        $value = $estate[$field] ?? 'NOT FOUND';
        echo "<p><strong>" . esc_html($field) . ":</strong> " . esc_html($value) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ No estate data received</p>";
    if ($result) {
        echo "<h3>API Response:</h3>";
        echo "<pre>" . htmlspecialchars($result['body']) . "</pre>";
    }
}

// Test 2: Check if we need to request specific fields
echo "<h2>Test 2: Requesting Specific Fields</h2>";

$test_request_with_fields = [
    'Field' => [
        'included' => ['id', 'name', 'price', 'city', 'purposeId', 'categoryId', 'purpose', 'category'],
        'excluded' => ['longDescription']
    ],
    'Limit' => 1,
    'Offset' => 0
];

echo "<h3>Request Body (with specific fields):</h3>";
echo "<pre>" . json_encode($test_request_with_fields, JSON_PRETTY_PRINT) . "</pre>";

$result2 = $whise->debug_api_call($test_request_with_fields);

if ($result2 && isset($result2['data']['estates']) && count($result2['data']['estates']) > 0) {
    $estate2 = $result2['data']['estates'][0];
    
    echo "<h3>Response with Specific Fields:</h3>";
    echo "<pre>" . json_encode($estate2, JSON_PRETTY_PRINT) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ No estate data received with specific fields</p>";
}

// Test 3: Check API documentation structure
echo "<h2>Test 3: API Documentation Check</h2>";
echo "<p>Based on the <a href='https://api.whise.eu/WebsiteDesigner.html#tag/Contacts/operation/' target='_blank'>Whise API documentation</a>, the correct structure might be different.</p>";

echo "<h3>Possible Issues:</h3>";
echo "<ul>";
echo "<li><strong>Field Names:</strong> The API might use different field names (e.g., 'purpose' instead of 'purposeId')</li>";
echo "<li><strong>Request Structure:</strong> Filters might need to be in a different part of the request</li>";
echo "<li><strong>Permissions:</strong> The client token might not have filtering permissions</li>";
echo "<li><strong>API Version:</strong> The endpoint might be different or require different parameters</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>Check the actual API response to see what fields are available</li>";
echo "<li>Verify the correct field names for filtering</li>";
echo "<li>Test with different request structures</li>";
echo "<li>Contact Whise support for correct API usage</li>";
echo "</ul>";
?> 
<?php
/**
 * Simple test script to debug Whise API filters
 * Run this directly to see what's happening with the API calls
 */

// Load WordPress
require_once('wp-load.php');

// Include Whise API
require_once(get_template_directory() . '/includes/WhiseAPI.php');

echo "<h1>🔍 Whise API Filter Debug Test</h1>";

// Initialize Whise API
$whise = new WhiseAPI();

if (!$whise) {
    echo "<p style='color: red;'>❌ Could not initialize Whise API</p>";
    exit;
}

// Test 1: No filters (should return all estates)
echo "<h2>Test 1: No Filters</h2>";
$result1 = $whise->get_estates([]);
if ($result1 && isset($result1['estates'])) {
    echo "<p>✅ Found " . count($result1['estates']) . " estates (no filters)</p>";
} else {
    echo "<p>❌ No estates found or error</p>";
}

// Test 2: Purpose filter only
echo "<h2>Test 2: Purpose Filter (ID: 1)</h2>";
$result2 = $whise->get_estates(['PurposeId' => 1]);
if ($result2 && isset($result2['estates'])) {
    echo "<p>✅ Found " . count($result2['estates']) . " estates (PurposeId: 1)</p>";
} else {
    echo "<p>❌ No estates found or error</p>";
}

// Test 3: Category filter only
echo "<h2>Test 3: Category Filter (ID: 2)</h2>";
$result3 = $whise->get_estates(['CategoryId' => 2]);
if ($result3 && isset($result3['estates'])) {
    echo "<p>✅ Found " . count($result3['estates']) . " estates (CategoryId: 2)</p>";
} else {
    echo "<p>❌ No estates found or error</p>";
}

// Test 4: Price filter only
echo "<h2>Test 4: Price Filter (€100k - €500k)</h2>";
$result4 = $whise->get_estates(['PriceMin' => 100000, 'PriceMax' => 500000]);
if ($result4 && isset($result4['estates'])) {
    echo "<p>✅ Found " . count($result4['estates']) . " estates (Price: €100k-€500k)</p>";
} else {
    echo "<p>❌ No estates found or error</p>";
}

// Test 5: Multiple filters
echo "<h2>Test 5: Multiple Filters (Purpose: 1, Category: 2, Price: €100k-€500k)</h2>";
$result5 = $whise->get_estates([
    'PurposeId' => 1,
    'CategoryId' => 2,
    'PriceMin' => 100000,
    'PriceMax' => 500000
]);
if ($result5 && isset($result5['estates'])) {
    echo "<p>✅ Found " . count($result5['estates']) . " estates (multiple filters)</p>";
} else {
    echo "<p>❌ No estates found or error</p>";
}

// Show detailed comparison
echo "<h2>📊 Results Comparison</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Test</th><th>Filters Applied</th><th>Estates Found</th><th>Difference</th></tr>";

$tests = [
    'No Filters' => ['filters' => [], 'result' => $result1],
    'Purpose Only' => ['filters' => ['PurposeId' => 1], 'result' => $result2],
    'Category Only' => ['filters' => ['CategoryId' => 2], 'result' => $result3],
    'Price Only' => ['filters' => ['PriceMin' => 100000, 'PriceMax' => 500000], 'result' => $result4],
    'Multiple' => ['filters' => ['PurposeId' => 1, 'CategoryId' => 2, 'PriceMin' => 100000, 'PriceMax' => 500000], 'result' => $result5]
];

$base_count = 0;
if ($result1 && isset($result1['estates'])) {
    $base_count = count($result1['estates']);
}

foreach ($tests as $test_name => $test_data) {
    $count = 0;
    if ($test_data['result'] && isset($test_data['result']['estates'])) {
        $count = count($test_data['result']['estates']);
    }
    
    $difference = $base_count - $count;
    $difference_text = $difference > 0 ? "-$difference" : ($difference < 0 ? "+" . abs($difference) : "0");
    
    echo "<tr>";
    echo "<td>" . esc_html($test_name) . "</td>";
    echo "<td>" . esc_html(json_encode($test_data['filters'])) . "</td>";
    echo "<td>" . $count . "</td>";
    echo "<td>" . $difference_text . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check if all results are the same
$counts = [];
foreach ($tests as $test_name => $test_data) {
    $count = 0;
    if ($test_data['result'] && isset($test_data['result']['estates'])) {
        $count = count($test_data['result']['estates']);
    }
    $counts[] = $count;
}

$unique_counts = array_unique($counts);
if (count($unique_counts) == 1) {
    echo "<p style='color: red; font-weight: bold;'>⚠️ WARNING: All tests returned the same number of estates (" . $unique_counts[0] . "). This suggests filters are not working properly.</p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>✅ Filters appear to be working - different counts returned.</p>";
}

// Show sample estate data to verify filter values
echo "<h2>🔍 Sample Estate Data Analysis</h2>";
if ($result1 && isset($result1['estates']) && count($result1['estates']) > 0) {
    echo "<h3>First 3 Estates (No Filters):</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>PurposeId</th><th>CategoryId</th><th>Price</th><th>City</th></tr>";
    
    for ($i = 0; $i < min(3, count($result1['estates'])); $i++) {
        $estate = $result1['estates'][$i];
        echo "<tr>";
        echo "<td>" . esc_html($estate['id'] ?? 'N/A') . "</td>";
        echo "<td>" . esc_html($estate['purposeId'] ?? 'N/A') . "</td>";
        echo "<td>" . esc_html($estate['categoryId'] ?? 'N/A') . "</td>";
        echo "<td>" . esc_html($estate['price'] ?? 'N/A') . "</td>";
        echo "<td>" . esc_html($estate['city'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h2>🔍 Debug Information</h2>";
echo "<p>Check the WordPress error logs for detailed API request/response information.</p>";
echo "<p>Look for lines starting with 'Whise API:' to see the actual requests being sent.</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ul>";
echo "<li>Check if the API is actually receiving the filter parameters</li>";
echo "<li>Verify the filter parameter names match the API documentation</li>";
echo "<li>Check if there are any API limitations or pagination issues</li>";
echo "<li>Verify the client token has proper permissions</li>";
echo "</ul>";
?> 
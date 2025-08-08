<?php
/**
 * Test page for International Property Filter
 * This page demonstrates the new international property filtering functionality
 */

// Load WordPress
require_once('wp-load.php');

// Include Whise API
require_once(get_template_directory() . '/includes/WhiseAPI.php');

echo "<!DOCTYPE html>";
echo "<html><head><title>International Property Filter Test</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }";
echo ".success { background-color: #d4edda; border-color: #c3e6cb; }";
echo ".error { background-color: #f8d7da; border-color: #f5c6cb; }";
echo ".info { background-color: #d1ecf1; border-color: #bee5eb; }";
echo "</style>";
echo "</head><body>";

echo "<h1>🌍 International Property Filter Test</h1>";

// Initialize Whise API
$whise = new WhiseAPI();

if (!$whise) {
    echo "<div class='test-section error'>❌ Could not initialize Whise API</div>";
    exit;
}

echo "<div class='test-section info'>";
echo "<h2>Test Overview</h2>";
echo "<p>This page tests the new international property filtering functionality:</p>";
echo "<ul>";
echo "<li><strong>Belgian Properties Filter:</strong> Shows only properties in Belgium (Country ID: 1)</li>";
echo "<li><strong>International Properties Filter:</strong> Shows properties in all countries except Belgium</li>";
echo "<li><strong>Country Filter:</strong> Allows filtering by specific countries</li>";
echo "</ul>";
echo "</div>";

// Test 1: Belgian Properties Only
echo "<div class='test-section'>";
echo "<h2>Test 1: Belgian Properties Only</h2>";
$belgian_filters = ['CountryIds' => [1]];
$belgian_result = $whise->get_estates($belgian_filters);

if ($belgian_result && isset($belgian_result['estates'])) {
    echo "<p class='success'>✅ Found " . count($belgian_result['estates']) . " Belgian properties</p>";
    
    if (count($belgian_result['estates']) > 0) {
        echo "<p><strong>Sample Belgian Properties:</strong></p>";
        echo "<ul>";
        foreach (array_slice($belgian_result['estates'], 0, 3) as $estate) {
            $country = isset($estate['country']) ? $estate['country'] : 'Unknown';
            $city = isset($estate['city']) ? $estate['city'] : 'Unknown';
            echo "<li>" . esc_html($estate['name'] ?? 'Unnamed Property') . " - " . esc_html($city) . ", " . esc_html($country) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ No Belgian properties found or API error</p>";
}
echo "</div>";

// Test 2: International Properties (Excluding Belgium)
echo "<div class='test-section'>";
echo "<h2>Test 2: International Properties (Excluding Belgium)</h2>";
$international_countries = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31, 32, 35, 36, 38, 43, 46, 61, 72, 73, 76, 83, 93, 103, 120, 121, 122, 123, 126, 128, 132, 140, 141, 145, 146, 155, 161, 163, 164, 166, 168, 193, 196, 210, 213, 216, 234, 239, 240, 242, 246, 247];
$international_filters = ['CountryIds' => $international_countries];
$international_result = $whise->get_estates($international_filters);

if ($international_result && isset($international_result['estates'])) {
    echo "<p class='success'>✅ Found " . count($international_result['estates']) . " international properties</p>";
    
    if (count($international_result['estates']) > 0) {
        echo "<p><strong>Sample International Properties:</strong></p>";
        echo "<ul>";
        foreach (array_slice($international_result['estates'], 0, 3) as $estate) {
            $country = isset($estate['country']) ? $estate['country'] : 'Unknown';
            $city = isset($estate['city']) ? $estate['city'] : 'Unknown';
            echo "<li>" . esc_html($estate['name'] ?? 'Unnamed Property') . " - " . esc_html($city) . ", " . esc_html($country) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ No international properties found or API error</p>";
}
echo "</div>";

// Test 3: Specific Country Filter (Spain)
echo "<div class='test-section'>";
echo "<h2>Test 3: Specific Country Filter (Spain - ID: 7)</h2>";
$spain_filters = ['CountryIds' => [7]];
$spain_result = $whise->get_estates($spain_filters);

if ($spain_result && isset($spain_result['estates'])) {
    echo "<p class='success'>✅ Found " . count($spain_result['estates']) . " Spanish properties</p>";
    
    if (count($spain_result['estates']) > 0) {
        echo "<p><strong>Sample Spanish Properties:</strong></p>";
        echo "<ul>";
        foreach (array_slice($spain_result['estates'], 0, 3) as $estate) {
            $country = isset($estate['country']) ? $estate['country'] : 'Unknown';
            $city = isset($estate['city']) ? $estate['city'] : 'Unknown';
            echo "<li>" . esc_html($estate['name'] ?? 'Unnamed Property') . " - " . esc_html($city) . ", " . esc_html($country) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ No Spanish properties found or API error</p>";
}
echo "</div>";

// Test 4: Multiple Countries Filter
echo "<div class='test-section'>";
echo "<h2>Test 4: Multiple Countries Filter (France, Germany, Italy)</h2>";
$multiple_countries_filters = ['CountryIds' => [3, 4, 8]]; // France, Germany, Italy
$multiple_countries_result = $whise->get_estates($multiple_countries_filters);

if ($multiple_countries_result && isset($multiple_countries_result['estates'])) {
    echo "<p class='success'>✅ Found " . count($multiple_countries_result['estates']) . " properties in France, Germany, and Italy</p>";
    
    if (count($multiple_countries_result['estates']) > 0) {
        echo "<p><strong>Sample Properties:</strong></p>";
        echo "<ul>";
        foreach (array_slice($multiple_countries_result['estates'], 0, 3) as $estate) {
            $country = isset($estate['country']) ? $estate['country'] : 'Unknown';
            $city = isset($estate['city']) ? $estate['city'] : 'Unknown';
            echo "<li>" . esc_html($estate['name'] ?? 'Unnamed Property') . " - " . esc_html($city) . ", " . esc_html($country) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ No properties found in France, Germany, and Italy or API error</p>";
}
echo "</div>";

// Test 5: Combined Filters
echo "<div class='test-section'>";
echo "<h2>Test 5: Combined Filters (Purpose + Country)</h2>";
$combined_filters = [
    'PurposeIds' => [1], // Sale
    'CountryIds' => [7, 8] // Spain and Italy
];
$combined_result = $whise->get_estates($combined_filters);

if ($combined_result && isset($combined_result['estates'])) {
    echo "<p class='success'>✅ Found " . count($combined_result['estates']) . " properties for sale in Spain and Italy</p>";
    
    if (count($combined_result['estates']) > 0) {
        echo "<p><strong>Sample Properties:</strong></p>";
        echo "<ul>";
        foreach (array_slice($combined_result['estates'], 0, 3) as $estate) {
            $country = isset($estate['country']) ? $estate['country'] : 'Unknown';
            $city = isset($estate['city']) ? $estate['city'] : 'Unknown';
            $purpose = isset($estate['purpose']['name']) ? $estate['purpose']['name'] : 'Unknown';
            echo "<li>" . esc_html($estate['name'] ?? 'Unnamed Property') . " - " . esc_html($city) . ", " . esc_html($country) . " (" . esc_html($purpose) . ")</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>❌ No properties found with combined filters or API error</p>";
}
echo "</div>";

echo "<div class='test-section info'>";
echo "<h2>Next Steps</h2>";
echo "<p>To use these filters in your WordPress site:</p>";
echo "<ol>";
echo "<li>Use the <strong>Belgian Properties Filter</strong> block for pages showing only Belgian properties</li>";
echo "<li>Use the <strong>International Properties Filter</strong> block for pages showing international properties</li>";
echo "<li>The international filter includes a country dropdown to filter by specific countries</li>";
echo "<li>Both filters support all existing functionality (purpose, category, price, city)</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
?>

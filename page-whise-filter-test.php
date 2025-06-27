<?php
/**
 * Template Name: Whise Filter Test
 * 
 * A comprehensive test page for debugging Whise API filters
 * This template runs various filter tests and shows detailed results
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <h1 style="color: #333; border-bottom: 2px solid #5c182e; padding-bottom: 10px;">🔍 Whise API Filter Test</h1>
    
    <?php
    // Include Whise API
    require_once(get_template_directory() . '/includes/WhiseAPI.php');
    
    // Initialize Whise API
    $whise = new WhiseAPI();
    
    if (!$whise) {
        echo "<p style='color: red;'>❌ Could not initialize Whise API</p>";
        get_footer();
        return;
    }
    ?>

    <!-- Test Results Section -->
    <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007cba;">
        <h2 style="color: #007cba; margin-top: 0;">🧪 Filter Test Results</h2>
        
        <?php
        // Test 1: No filters (should return all estates)
        echo "<h3>Test 1: No Filters</h3>";
        $result1 = $whise->get_estates([]);
        if ($result1 && isset($result1['estates'])) {
            echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($result1['estates']) . " estates (no filters)</p>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or error</p>";
        }

        // Test 2: Purpose filter only
        echo "<h3>Test 2: Purpose Filter (ID: 1)</h3>";
        $result2 = $whise->get_estates(['PurposeId' => 1]);
        if ($result2 && isset($result2['estates'])) {
            echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($result2['estates']) . " estates (PurposeId: 1)</p>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or error</p>";
        }

        // Test 3: Category filter only
        echo "<h3>Test 3: Category Filter (ID: 2)</h3>";
        $result3 = $whise->get_estates(['CategoryId' => 2]);
        if ($result3 && isset($result3['estates'])) {
            echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($result3['estates']) . " estates (CategoryId: 2)</p>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or error</p>";
        }

        // Test 4: Price filter only
        echo "<h3>Test 4: Price Filter (€100k - €500k)</h3>";
        $result4 = $whise->get_estates(['PriceMin' => 100000, 'PriceMax' => 500000]);
        if ($result4 && isset($result4['estates'])) {
            echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($result4['estates']) . " estates (Price: €100k-€500k)</p>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or error</p>";
        }

        // Test 5: Multiple filters
        echo "<h3>Test 5: Multiple Filters (Purpose: 1, Category: 2, Price: €100k-€500k)</h3>";
        $result5 = $whise->get_estates([
            'PurposeId' => 1,
            'CategoryId' => 2,
            'PriceMin' => 100000,
            'PriceMax' => 500000
        ]);
        if ($result5 && isset($result5['estates'])) {
            echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($result5['estates']) . " estates (multiple filters)</p>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or error</p>";
        }
        ?>
    </div>

    <!-- Results Comparison Table -->
    <div style="background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6c757d;">
        <h2 style="color: #495057; margin-top: 0;">📊 Results Comparison</h2>
        
        <?php
        echo "<table border='1' style='border-collapse: collapse; width: 100%; background: white;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th style='padding: 12px; text-align: left;'>Test</th>";
        echo "<th style='padding: 12px; text-align: left;'>Filters Applied</th>";
        echo "<th style='padding: 12px; text-align: center;'>Estates Found</th>";
        echo "<th style='padding: 12px; text-align: center;'>Difference</th>";
        echo "</tr>";

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
            $difference_color = $difference == 0 ? '#721c24' : ($difference > 0 ? '#155724' : '#856404');
            
            echo "<tr>";
            echo "<td style='padding: 12px;'>" . esc_html($test_name) . "</td>";
            echo "<td style='padding: 12px; font-family: monospace;'>" . esc_html(json_encode($test_data['filters'])) . "</td>";
            echo "<td style='padding: 12px; text-align: center; font-weight: bold;'>" . $count . "</td>";
            echo "<td style='padding: 12px; text-align: center; color: " . $difference_color . "; font-weight: bold;'>" . $difference_text . "</td>";
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
            echo "<p style='color: #721c24; background: #f8d7da; padding: 15px; border-radius: 4px; margin-top: 20px; font-weight: bold;'>⚠️ WARNING: All tests returned the same number of estates (" . $unique_counts[0] . "). This suggests filters are not working properly.</p>";
        } else {
            echo "<p style='color: #155724; background: #d4edda; padding: 15px; border-radius: 4px; margin-top: 20px; font-weight: bold;'>✅ Filters appear to be working - different counts returned.</p>";
        }
        ?>
    </div>

    <!-- Sample Estate Data Analysis -->
    <div style="background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #17a2b8;">
        <h2 style="color: #0c5460; margin-top: 0;">🔍 Sample Estate Data Analysis</h2>
        
        <?php
        if ($result1 && isset($result1['estates']) && count($result1['estates']) > 0) {
            echo "<h3>First 3 Estates (No Filters):</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%; background: white;'>";
            echo "<tr style='background: #f8f9fa;'>";
            echo "<th style='padding: 12px; text-align: left;'>ID</th>";
            echo "<th style='padding: 12px; text-align: left;'>PurposeId</th>";
            echo "<th style='padding: 12px; text-align: left;'>CategoryId</th>";
            echo "<th style='padding: 12px; text-align: left;'>Price</th>";
            echo "<th style='padding: 12px; text-align: left;'>City</th>";
            echo "</tr>";
            
            for ($i = 0; $i < min(3, count($result1['estates'])); $i++) {
                $estate = $result1['estates'][$i];
                echo "<tr>";
                echo "<td style='padding: 12px;'>" . esc_html($estate['id'] ?? 'N/A') . "</td>";
                echo "<td style='padding: 12px;'>" . esc_html($estate['purposeId'] ?? 'N/A') . "</td>";
                echo "<td style='padding: 12px;'>" . esc_html($estate['categoryId'] ?? 'N/A') . "</td>";
                echo "<td style='padding: 12px;'>" . esc_html($estate['price'] ?? 'N/A') . "</td>";
                echo "<td style='padding: 12px;'>" . esc_html($estate['city'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estate data available for analysis</p>";
        }
        ?>
    </div>

    <!-- Debug Information -->
    <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ffc107;">
        <h2 style="color: #856404; margin-top: 0;">🔍 Debug Information</h2>
        <p>Check the WordPress error logs for detailed API request/response information.</p>
        <p>Look for lines starting with 'Whise API:' to see the actual requests being sent.</p>
        
        <h3>Next Steps:</h3>
        <ul>
            <li><strong>Check API Logs:</strong> Review WordPress error logs for detailed API communication</li>
            <li><strong>Verify Parameters:</strong> Ensure filter parameter names match the API documentation</li>
            <li><strong>Test Permissions:</strong> Verify the client token has proper filtering permissions</li>
            <li><strong>Check Limitations:</strong> Look for API pagination or result limit issues</li>
            <li><strong>Use Debug Page:</strong> Visit the Whise Debug page for more detailed testing</li>
        </ul>
        
        <h3>Common Issues:</h3>
        <ul>
            <li><strong>Same count across all tests:</strong> Filters not being processed by the API</li>
            <li><strong>Authentication errors:</strong> Check client token and permissions</li>
            <li><strong>Parameter format:</strong> API might expect different parameter structure</li>
            <li><strong>Data availability:</strong> No estates match the filter criteria</li>
        </ul>
    </div>

    <!-- Quick Actions -->
    <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6c757d;">
        <h2 style="color: #495057; margin-top: 0;">⚡ Quick Actions</h2>
        <p>Use these links to perform additional debugging:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
            <a href="<?php echo admin_url('admin.php?page=whise-debug'); ?>" style="background: #007cba; color: white; padding: 15px; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;">🔧 Whise Debug Page</a>
            <a href="<?php echo admin_url('admin.php?page=whise-settings'); ?>" style="background: #28a745; color: white; padding: 15px; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;">⚙️ API Settings</a>
            <a href="<?php echo admin_url('admin.php?page=site-health'); ?>" style="background: #6c757d; color: white; padding: 15px; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;">📊 Site Health</a>
        </div>
    </div>
</div>

<?php get_footer(); ?> 
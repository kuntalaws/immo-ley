<?php
/**
 * Template Name: Whise API Debug
 * 
 * A comprehensive debugging page for testing Whise API filters
 * Access this page to test individual filters and see detailed API responses
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <h1 style="color: #333; border-bottom: 2px solid #5c182e; padding-bottom: 10px;">🔍 Whise API Debug Dashboard</h1>
    
    <?php
    // Include Whise API
    require_once(get_template_directory() . '/includes/WhiseAPI.php');
    
    // Initialize Whise API
    $whise = new WhiseAPI();
    
    // Get configuration
    $api_url = get_field('whise_api_url', 'option') ?: 'https://api.whise.eu';
    $username = get_field('whise_username', 'option');
    $password = get_field('whise_password', 'option');
    $client_id = get_field('whise_client_id', 'option');
    ?>
    
    <!-- Configuration Section -->
    <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007cba;">
        <h2 style="color: #007cba; margin-top: 0;">⚙️ Configuration Check</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>API URL:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo esc_html($api_url); ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Username:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo $username ? '✅ Set' : '❌ Not set'; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Password:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?php echo $password ? '✅ Set' : '❌ Not set'; ?></td>
            </tr>
            <tr>
                <td style="padding: 8px;"><strong>Client ID:</strong></td>
                <td style="padding: 8px;"><?php echo $client_id ? esc_html($client_id) : '❌ Not set'; ?></td>
            </tr>
        </table>
    </div>

    <!-- API Connection Test -->
    <div style="background: #d1ecf1; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #17a2b8;">
        <h2 style="color: #0c5460; margin-top: 0;">🔗 API Connection Test</h2>
        <?php
        if ($whise) {
            $test_result = $whise->test_connection();
            
            if ($test_result['success']) {
                echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ API connection successful</p>";
                echo "<p><strong>Clients found:</strong> " . $test_result['clients_count'] . "</p>";
                echo "<p><strong>Estates found:</strong> " . $test_result['estates_count'] . "</p>";
            } else {
                echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ API connection failed: " . esc_html($test_result['message']) . "</p>";
            }
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ Could not initialize Whise API</p>";
        }
        ?>
    </div>

    <!-- Debug API Calls Section -->
    <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ffc107;">
        <h2 style="color: #856404; margin-top: 0;">🔧 Debug API Calls</h2>
        
        <form method="GET" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
            <input type="hidden" name="debug_api" value="1">
            <h3>Test API Call with Detailed Logging</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label><strong>Purpose ID:</strong></label><br>
                    <input type="number" name="debug_purpose" value="<?php echo isset($_GET['debug_purpose']) ? esc_attr($_GET['debug_purpose']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Category ID:</strong></label><br>
                    <input type="number" name="debug_category" value="<?php echo isset($_GET['debug_category']) ? esc_attr($_GET['debug_category']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Price Min:</strong></label><br>
                    <input type="number" name="debug_price_min" value="<?php echo isset($_GET['debug_price_min']) ? esc_attr($_GET['debug_price_min']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Price Max:</strong></label><br>
                    <input type="number" name="debug_price_max" value="<?php echo isset($_GET['debug_price_max']) ? esc_attr($_GET['debug_price_max']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>
            
            <button type="submit" style="background: #ffc107; color: #856404; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Debug API Call</button>
        </form>

        <?php
        // Debug API call if requested
        if (isset($_GET['debug_api']) && $_GET['debug_api'] == '1') {
            $debug_filters = [];
            
            if (!empty($_GET['debug_purpose'])) {
                $debug_filters['PurposeId'] = intval($_GET['debug_purpose']);
            }
            if (!empty($_GET['debug_category'])) {
                $debug_filters['CategoryId'] = intval($_GET['debug_category']);
            }
            if (!empty($_GET['debug_price_min'])) {
                $debug_filters['PriceMin'] = intval($_GET['debug_price_min']);
            }
            if (!empty($_GET['debug_price_max'])) {
                $debug_filters['PriceMax'] = intval($_GET['debug_price_max']);
            }
            
            echo "<div style='background: white; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
            echo "<h3>Debug API Call Results</h3>";
            echo "<p><strong>Applied Filters:</strong> " . print_r($debug_filters, true) . "</p>";
            
            if ($whise && method_exists($whise, 'debug_api_call')) {
                $debug_result = $whise->debug_api_call($debug_filters);
                
                if ($debug_result) {
                    echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ API call completed</p>";
                    echo "<p><strong>Status Code:</strong> " . $debug_result['status_code'] . "</p>";
                    
                    if (isset($debug_result['data']['estates'])) {
                        echo "<p><strong>Estates Found:</strong> " . count($debug_result['data']['estates']) . "</p>";
                    }
                    
                    echo "<details style='margin-top: 15px;'>";
                    echo "<summary style='cursor: pointer; font-weight: bold;'>Show Full API Response</summary>";
                    echo "<h4>Response Headers:</h4>";
                    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;'>" . print_r($debug_result['headers'], true) . "</pre>";
                    echo "<h4>Response Body:</h4>";
                    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;'>" . htmlspecialchars($debug_result['body']) . "</pre>";
                    echo "</details>";
                } else {
                    echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ API call failed</p>";
                }
            } else {
                echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ Debug method not available</p>";
            }
            echo "</div>";
        }
        ?>
    </div>

    <!-- Filter Testing Section -->
    <div style="background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6c757d;">
        <h2 style="color: #495057; margin-top: 0;">🧪 Filter Testing</h2>
        
        <!-- Test Form -->
        <form method="GET" style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
            <h3>Test Individual Filters</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label><strong>Purpose ID:</strong></label><br>
                    <input type="number" name="test_purpose" value="<?php echo isset($_GET['test_purpose']) ? esc_attr($_GET['test_purpose']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Category ID:</strong></label><br>
                    <input type="number" name="test_category" value="<?php echo isset($_GET['test_category']) ? esc_attr($_GET['test_category']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>City:</strong></label><br>
                    <input type="text" name="test_city" value="<?php echo isset($_GET['test_city']) ? esc_attr($_GET['test_city']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Price Min:</strong></label><br>
                    <input type="number" name="test_price_min" value="<?php echo isset($_GET['test_price_min']) ? esc_attr($_GET['test_price_min']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Price Max:</strong></label><br>
                    <input type="number" name="test_price_max" value="<?php echo isset($_GET['test_price_max']) ? esc_attr($_GET['test_price_max']) : ''; ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div>
                    <label><strong>Show Debug Info:</strong></label><br>
                    <input type="checkbox" name="show_debug" value="1" <?php echo isset($_GET['show_debug']) ? 'checked' : ''; ?>>
                </div>
            </div>
            
            <button type="submit" style="background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Test Filters</button>
        </form>

        <?php
        // Test filters if form submitted
        if (isset($_GET['test_purpose']) || isset($_GET['test_category']) || isset($_GET['test_city']) || isset($_GET['test_price_min']) || isset($_GET['test_price_max'])) {
            $test_filters = [];
            
            if (!empty($_GET['test_purpose'])) {
                $test_filters['PurposeId'] = intval($_GET['test_purpose']);
            }
            if (!empty($_GET['test_category'])) {
                $test_filters['CategoryId'] = intval($_GET['test_category']);
            }
            if (!empty($_GET['test_city'])) {
                $test_filters['City'] = sanitize_text_field($_GET['test_city']);
            }
            if (!empty($_GET['test_price_min'])) {
                $test_filters['PriceMin'] = intval($_GET['test_price_min']);
            }
            if (!empty($_GET['test_price_max'])) {
                $test_filters['PriceMax'] = intval($_GET['test_price_max']);
            }
            
            echo "<div style='background: white; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
            echo "<h3>Test Results</h3>";
            echo "<p><strong>Applied Filters:</strong> " . print_r($test_filters, true) . "</p>";
            
            if ($whise) {
                $test_result = $whise->get_estates($test_filters);
                
                if ($test_result && isset($test_result['estates'])) {
                    echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 4px;'>✅ Found " . count($test_result['estates']) . " estates</p>";
                    
                    // Count sold properties
                    $sold_count = 0;
                    foreach ($test_result['estates'] as $estate) {
                        if (isset($estate['is_sold']) && $estate['is_sold']) {
                            $sold_count++;
                        }
                    }
                    echo "<p style='color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px;'>🏠 Sold Properties: " . $sold_count . " out of " . count($test_result['estates']) . "</p>";
                    
                    if (isset($_GET['show_debug']) && $_GET['show_debug'] == '1') {
                        echo "<details style='margin-top: 15px;'>";
                        echo "<summary style='cursor: pointer; font-weight: bold;'>Show Full API Response</summary>";
                        echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto;'>" . print_r($test_result, true) . "</pre>";
                        echo "</details>";
                    }
                    
                    // Show first few estates with sold status
                    echo "<h4>Sample Estates with Sold Status:</h4>";
                    echo "<div style='max-height: 400px; overflow-y: auto;'>";
                    foreach (array_slice($test_result['estates'], 0, 5) as $estate) {
                        $sold_status = isset($estate['is_sold']) && $estate['is_sold'] ? '🔴 SOLD' : '🟢 AVAILABLE';
                        $sold_class = isset($estate['is_sold']) && $estate['is_sold'] ? 'sold-property' : '';
                        
                        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px 0; border-radius: 4px; background: " . (isset($estate['is_sold']) && $estate['is_sold'] ? '#fff5f5' : '#f8f9fa') . ";'>";
                        echo "<strong>ID:</strong> " . esc_html($estate['id']) . " | <strong>Status:</strong> <span style='color: " . (isset($estate['is_sold']) && $estate['is_sold'] ? '#dc3545' : '#28a745') . "; font-weight: bold;'>" . $sold_status . "</span><br>";
                        echo "<strong>Name:</strong> " . esc_html($estate['name'] ?? 'N/A') . "<br>";
                        echo "<strong>City:</strong> " . esc_html($estate['city'] ?? 'N/A') . "<br>";
                        echo "<strong>Price:</strong> " . esc_html($estate['price'] ?? 'N/A') . "<br>";
                        echo "<strong>Purpose ID:</strong> " . esc_html($estate['purposeId'] ?? 'N/A') . "<br>";
                        echo "<strong>Category ID:</strong> " . esc_html($estate['categoryId'] ?? 'N/A') . "<br>";
                        
                        // Show status-related fields for debugging
                        if (isset($estate['statusId'])) {
                            echo "<strong>Status ID:</strong> " . esc_html($estate['statusId']) . "<br>";
                        }
                        if (isset($estate['purposeStatusId'])) {
                            echo "<strong>Purpose Status ID:</strong> " . esc_html($estate['purposeStatusId']) . "<br>";
                        }
                        if (isset($estate['status'])) {
                            echo "<strong>Status:</strong> " . esc_html($estate['status']) . "<br>";
                        }
                        if (isset($estate['purposeStatus'])) {
                            echo "<strong>Purpose Status:</strong> " . esc_html($estate['purposeStatus']) . "<br>";
                        }
                        
                        echo "</div>";
                    }
                    echo "</div>";
                } else {
                    echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or API error</p>";
                }
            } else {
                echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ Whise API not available</p>";
            }
            echo "</div>";
        }
        ?>
    </div>

    <!-- Quick Test Links -->
    <div style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6c757d;">
        <h2 style="color: #495057; margin-top: 0;">⚡ Quick Test Links</h2>
        <p>Click these links to quickly test different filter combinations:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; margin-top: 15px;">
            <a href="?page_id=<?php echo get_the_ID(); ?>&test_purpose=1&show_debug=1" style="background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 4px; text-align: center;">Test Purpose ID 1 (Te koop)</a>
            <a href="?page_id=<?php echo get_the_ID(); ?>&test_category=2&show_debug=1" style="background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 4px; text-align: center;">Test Category ID 2 (Huis)</a>
            <a href="?page_id=<?php echo get_the_ID(); ?>&test_city=Antwerp&show_debug=1" style="background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 4px; text-align: center;">Test City: Antwerp</a>
            <a href="?page_id=<?php echo get_the_ID(); ?>&test_price_min=100000&test_price_max=500000&show_debug=1" style="background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 4px; text-align: center;">Test Price: €100k-€500k</a>
            <a href="?page_id=<?php echo get_the_ID(); ?>&test_purpose=1&test_category=2&test_price_min=1000000&show_debug=1" style="background: #007cba; color: white; padding: 10px; text-decoration: none; border-radius: 4px; text-align: center;">Test Multiple Filters</a>
        </div>
    </div>

    <!-- Instructions -->
    <div style="background: #e2e3e5; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #6c757d;">
        <h2 style="color: #495057; margin-top: 0;">📖 How to Use This Debug Page</h2>
        <ol>
            <li><strong>Check Configuration:</strong> Verify all API credentials are set correctly</li>
            <li><strong>Test Connection:</strong> Verify the API connection is working</li>
            <li><strong>Test Individual Filters:</strong> Use the form above to test specific filters</li>
            <li><strong>Use Quick Test Links:</strong> Try the pre-configured test scenarios</li>
            <li><strong>Check Error Logs:</strong> Review WordPress error logs for API communication</li>
        </ol>
    </div>

    <!-- Sold Property Detection Test -->
    <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ffc107;">
        <h2 style="color: #856404; margin-top: 0;">🏠 Sold Property Detection Test</h2>
        
        <?php
        if ($whise) {
            echo "<p>Testing sold property detection with all estates...</p>";
            
            $all_estates = $whise->get_estates();
            
            if ($all_estates && isset($all_estates['estates'])) {
                $total_estates = count($all_estates['estates']);
                $sold_estates = 0;
                $available_estates = 0;
                
                foreach ($all_estates['estates'] as $estate) {
                    if (isset($estate['is_sold']) && $estate['is_sold']) {
                        $sold_estates++;
                    } else {
                        $available_estates++;
                    }
                }
                
                echo "<div style='background: white; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
                echo "<h3>Detection Results:</h3>";
                echo "<ul>";
                echo "<li><strong>Total Estates:</strong> " . $total_estates . "</li>";
                echo "<li><strong>Available Estates:</strong> <span style='color: #28a745; font-weight: bold;'>" . $available_estates . "</span></li>";
                echo "<li><strong>Sold Estates:</strong> <span style='color: #dc3545; font-weight: bold;'>" . $sold_estates . "</span></li>";
                echo "<li><strong>Sold Percentage:</strong> " . round(($sold_estates / $total_estates) * 100, 1) . "%</li>";
                echo "</ul>";
                echo "</div>";
                
                // Show sample of sold properties
                if ($sold_estates > 0) {
                    echo "<h3>Sample Sold Properties:</h3>";
                    echo "<div style='max-height: 300px; overflow-y: auto;'>";
                    $sold_count = 0;
                    foreach ($all_estates['estates'] as $estate) {
                        if (isset($estate['is_sold']) && $estate['is_sold'] && $sold_count < 5) {
                            echo "<div style='background: #fff5f5; border: 1px solid #feb2b2; padding: 10px; margin: 5px 0; border-radius: 4px;'>";
                            echo "<strong>🔴 SOLD - ID:</strong> " . esc_html($estate['id']) . "<br>";
                            echo "<strong>Name:</strong> " . esc_html($estate['name'] ?? 'N/A') . "<br>";
                            echo "<strong>City:</strong> " . esc_html($estate['city'] ?? 'N/A') . "<br>";
                            echo "<strong>Price:</strong> " . esc_html($estate['price'] ?? 'N/A') . "<br>";
                            
                            // Show detection indicators
                            if (isset($estate['statusId'])) {
                                echo "<strong>Status ID:</strong> " . esc_html($estate['statusId']) . "<br>";
                            }
                            if (isset($estate['purposeStatusId'])) {
                                echo "<strong>Purpose Status ID:</strong> " . esc_html($estate['purposeStatusId']) . "<br>";
                            }
                            if (isset($estate['status'])) {
                                echo "<strong>Status:</strong> " . esc_html($estate['status']) . "<br>";
                            }
                            if (isset($estate['purposeStatus'])) {
                                echo "<strong>Purpose Status:</strong> " . esc_html($estate['purposeStatus']) . "<br>";
                            }
                            
                            echo "</div>";
                            $sold_count++;
                        }
                    }
                    echo "</div>";
                }
                
                // Show sample of available properties
                if ($available_estates > 0) {
                    echo "<h3>Sample Available Properties:</h3>";
                    echo "<div style='max-height: 300px; overflow-y: auto;'>";
                    $available_count = 0;
                    foreach ($all_estates['estates'] as $estate) {
                        if ((!isset($estate['is_sold']) || !$estate['is_sold']) && $available_count < 5) {
                            echo "<div style='background: #f8fff9; border: 1px solid #9ae6b4; padding: 10px; margin: 5px 0; border-radius: 4px;'>";
                            echo "<strong>🟢 AVAILABLE - ID:</strong> " . esc_html($estate['id']) . "<br>";
                            echo "<strong>Name:</strong> " . esc_html($estate['name'] ?? 'N/A') . "<br>";
                            echo "<strong>City:</strong> " . esc_html($estate['city'] ?? 'N/A') . "<br>";
                            echo "<strong>Price:</strong> " . esc_html($estate['price'] ?? 'N/A') . "<br>";
                            
                            // Show detection indicators
                            if (isset($estate['statusId'])) {
                                echo "<strong>Status ID:</strong> " . esc_html($estate['statusId']) . "<br>";
                            }
                            if (isset($estate['purposeStatusId'])) {
                                echo "<strong>Purpose Status ID:</strong> " . esc_html($estate['purposeStatusId']) . "<br>";
                            }
                            if (isset($estate['status'])) {
                                echo "<strong>Status:</strong> " . esc_html($estate['status']) . "<br>";
                            }
                            if (isset($estate['purposeStatus'])) {
                                echo "<strong>Purpose Status:</strong> " . esc_html($estate['purposeStatus']) . "<br>";
                            }
                            
                            echo "</div>";
                            $available_count++;
                        }
                    }
                    echo "</div>";
                }
                
            } else {
                echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ No estates found or API error</p>";
            }
        } else {
            echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px;'>❌ Whise API not available</p>";
        }
        ?>
    </div>
</div>

<?php get_footer(); ?> 
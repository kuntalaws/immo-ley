<?php
/**
 * Template Name: Whise API Test
 * 
 * This template is for testing the Whise API integration
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 50px auto; padding: 20px; font-family: Arial, sans-serif;">
    
    <h1 style="color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px;">🔍 Whise API Test Page</h1>
    
    <?php
    // Include the Whise API class
    if (file_exists(get_template_directory() . '/includes/WhiseAPI.php')) {
        require_once(get_template_directory() . '/includes/WhiseAPI.php');
    } else {
        echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 20px 0;">';
        echo '<strong>❌ Error:</strong> WhiseAPI.php file not found!';
        echo '</div>';
        get_footer();
        return;
    }
    ?>
    
    <!-- Test 1: Credentials Check -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">1. 🔑 Credentials Check</h2>
        <?php
        $api_url = get_field('whise_api_url', 'option');
        $username = get_field('whise_username', 'option');
        $client_id = get_field('whise_client_id', 'option');
        
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>API URL:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($api_url ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>Username:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($username ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>Client ID:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($client_id ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '</table>';
        
        if (!$api_url || !$username || !$client_id) {
            echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin-top: 15px;">';
            echo '<strong>❌ Issue:</strong> Some credentials are missing. Please check your WordPress admin → Global → Whise API settings.';
            echo '</div>';
        } else {
            echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin-top: 15px;">';
            echo '<strong>✅ Good:</strong> All credentials are configured.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Test 2: API Connection -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">2. 🌐 API Connection Test</h2>
        <?php
        if ($api_url && $username && $client_id) {
            try {
                $whise = new WhiseAPI();
                
                // Test authentication
                echo '<h3>Testing Authentication...</h3>';
                $auth_result = $whise->authenticate();
                
                if ($auth_result) {
                    echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>✅ Authentication Successful!</strong>';
                    echo '</div>';
                } else {
                    echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>⚠️ Authentication Failed</strong> - Check your credentials or contact WHISE support.';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test API without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Test 3: Estates Data -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">3. 🏠 Estates Data Test</h2>
        <?php
        if ($api_url && $username && $client_id) {
            try {
                $whise = new WhiseAPI();
                $estates = $whise->get_estates();
                
                if ($estates === false) {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Error:</strong> API call failed completely.';
                    echo '</div>';
                } elseif (is_array($estates)) {
                    if (isset($estates['estates'])) {
                        $count = count($estates['estates']);
                        echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>✅ Success!</strong> Found ' . $count . ' estates in the system.';
                        echo '</div>';
                        
                        if ($count > 0) {
                            echo '<h3>Sample Estate Data:</h3>';
                            $first = $estates['estates'][0];
                            echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                            echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($first, true) . '</pre>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>⚠️ Warning:</strong> No "estates" key in response.';
                        echo '</div>';
                        echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                        echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($estates, true) . '</pre>';
                        echo '</div>';
                    }
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Error:</strong> Unexpected response type.';
                    echo '</div>';
                    echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                    echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($estates, true) . '</pre>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test estates without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Test 4: Cities Data -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">4. 🏙️ Cities Data Test</h2>
        <?php
        if ($api_url && $username && $client_id) {
            try {
                $whise = new WhiseAPI();
                $cities = $whise->get_cities();
                
                if ($cities && isset($cities['cities'])) {
                    $count = count($cities['cities']);
                    echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>✅ Success!</strong> Found ' . $count . ' cities.';
                    echo '</div>';
                    
                    if ($count > 0) {
                        echo '<h3>First 10 Cities:</h3>';
                        echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                        $first_10 = array_slice($cities['cities'], 0, 10);
                        foreach ($first_10 as $city) {
                            echo '<div style="padding: 5px 0; border-bottom: 1px solid #eee;">';
                            echo '<strong>' . esc_html($city['name']) . '</strong>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>⚠️ Warning:</strong> No cities found or error in response.';
                    echo '</div>';
                    echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                    echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($cities, true) . '</pre>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test cities without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Test 5: AJAX Function Test -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">5. 🔄 AJAX Function Test</h2>
        <?php
        if (function_exists('whise_get_estates_ajax')) {
            echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>✅ Success!</strong> AJAX function exists and is properly registered.';
            echo '</div>';
        } else {
            echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>❌ Error:</strong> AJAX function not found. Check if functions.php is loaded properly.';
            echo '</div>';
        }
        
        if (function_exists('whise_get_filter_options_ajax')) {
            echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>✅ Success!</strong> Filter options AJAX function exists.';
            echo '</div>';
        } else {
            echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>❌ Error:</strong> Filter options AJAX function not found.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Test 6: JavaScript Test -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">6. 📜 JavaScript Test</h2>
        <div id="js-test-results" style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
            <p>Checking JavaScript variables...</p>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            var results = $('#js-test-results');
            var html = '<h3>JavaScript Test Results:</h3>';
            
            // Test if whise_ajax object exists
            if (typeof whise_ajax !== 'undefined') {
                html += '<div style="background: #e8f5e8; color: #2e7d32; padding: 10px; border-radius: 5px; margin: 10px 0;">';
                html += '<strong>✅ Success!</strong> whise_ajax object found.';
                html += '</div>';
                html += '<p><strong>AJAX URL:</strong> ' + (whise_ajax.ajax_url || 'Not set') + '</p>';
                html += '<p><strong>Nonce:</strong> ' + (whise_ajax.nonce ? 'Set' : 'Not set') + '</p>';
            } else {
                html += '<div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin: 10px 0;">';
                html += '<strong>❌ Error:</strong> whise_ajax object not found. JavaScript may not be loading properly.';
                html += '</div>';
            }
            
            // Test if jQuery is available
            if (typeof $ !== 'undefined') {
                html += '<div style="background: #e8f5e8; color: #2e7d32; padding: 10px; border-radius: 5px; margin: 10px 0;">';
                html += '<strong>✅ Success!</strong> jQuery is available.';
                html += '</div>';
            } else {
                html += '<div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin: 10px 0;">';
                html += '<strong>❌ Error:</strong> jQuery not available.';
                html += '</div>';
            }
            
            results.html(html);
        });
        </script>
    </div>
    
    <!-- Instructions -->
    <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196f3;">
        <h2 style="color: #1976d2; margin-top: 0;">📋 Next Steps</h2>
        <ul style="line-height: 1.6;">
            <li><strong>If credentials are missing:</strong> Go to WordPress Admin → Global → Whise API settings</li>
            <li><strong>If authentication fails:</strong> Check your credentials or contact WHISE support</li>
            <li><strong>If no estates found:</strong> Your WHISE account may not have any properties yet</li>
            <li><strong>If JavaScript fails:</strong> Check if the whise-api.js file is loading properly</li>
        </ul>
    </div>
    
</div>

<?php get_footer(); ?> 
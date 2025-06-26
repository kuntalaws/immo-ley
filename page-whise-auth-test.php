<?php
/**
 * Template Name: Whise Auth Test
 * 
 * This template tests the complete WHISE API authentication flow
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 50px auto; padding: 20px; font-family: Arial, sans-serif;">
    
    <h1 style="color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px;">🔐 Whise API Authentication Flow Test</h1>
    
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
    
    <!-- Step 1: Credentials Check -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">🔑 Step 0: Credentials Check</h2>
        <?php
        $api_url = get_field('whise_api_url', 'option');
        $username = get_field('whise_username', 'option');
        $password = get_field('whise_password', 'option');
        $client_id = get_field('whise_client_id', 'option');
        
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>API URL:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($api_url ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>Username:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($username ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>Password:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($password ? '***SET***' : '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>Client ID:</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ($client_id ?: '<span style="color: red;">NOT SET</span>') . '</td></tr>';
        echo '</table>';
        
        if (!$api_url || !$username || !$password || !$client_id) {
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
    
    <!-- Step 1: Authentication -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">🔐 Step 1: Authentication (Username/Password → Token)</h2>
        <?php
        if ($api_url && $username && $password && $client_id) {
            try {
                $whise = new WhiseAPI();
                
                echo '<h3>Testing Authentication...</h3>';
                $auth_result = $whise->authenticate();
                
                if ($auth_result) {
                    echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>✅ Step 1 Successful!</strong> Authentication token obtained.';
                    echo '</div>';
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Step 1 Failed</strong> - Check your username/password or contact WHISE support.';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test authentication without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Step 2: Client List -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">📋 Step 2: Get Client List (Token → Client List)</h2>
        <?php
        if ($api_url && $username && $password && $client_id) {
            try {
                $whise = new WhiseAPI();
                
                // First authenticate
                if ($whise->authenticate()) {
                    echo '<h3>Getting Client List...</h3>';
                    $clients = $whise->get_client_list();
                    
                    if ($clients !== false) {
                        echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>✅ Step 2 Successful!</strong> Found ' . count($clients) . ' clients.';
                        echo '</div>';
                        
                        // Show client list
                        echo '<h3>Available Clients:</h3>';
                        echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                        foreach ($clients as $client) {
                            $is_selected = ($client['id'] == $client_id) ? ' <strong style="color: #1976d2;">(SELECTED)</strong>' : '';
                            echo '<div style="padding: 5px 0; border-bottom: 1px solid #eee;">';
                            echo '<strong>ID: ' . $client['id'] . '</strong> - ' . $client['name'] . $is_selected;
                            echo '</div>';
                        }
                        echo '</div>';
                    } else {
                        echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>❌ Step 2 Failed</strong> - Could not get client list.';
                        echo '</div>';
                    }
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Step 2 Failed</strong> - Authentication required first.';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test client list without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Step 3: Client Token -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">🎫 Step 3: Get Client Token (Client ID + Token → Client Token)</h2>
        <?php
        if ($api_url && $username && $password && $client_id) {
            try {
                $whise = new WhiseAPI();
                
                // First authenticate
                if ($whise->authenticate()) {
                    echo '<h3>Getting Client Token...</h3>';
                    $client_token_result = $whise->get_client_token();
                    
                    if ($client_token_result) {
                        echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>✅ Step 3 Successful!</strong> Client token obtained for Client ID: ' . $client_id;
                        echo '</div>';
                    } else {
                        echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>❌ Step 3 Failed</strong> - Could not get client token. Check if Client ID is correct.';
                        echo '</div>';
                    }
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Step 3 Failed</strong> - Authentication required first.';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot test client token without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Step 4: Estates -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">🏠 Step 4: Get Estates (Client Token → Estates)</h2>
        <?php
        if ($api_url && $username && $password && $client_id) {
            try {
                $whise = new WhiseAPI();
                
                // First authenticate and get client token
                if ($whise->authenticate() && $whise->get_client_token()) {
                    echo '<h3>Getting Estates...</h3>';
                    $estates = $whise->get_estates();
                    
                    if ($estates === false) {
                        echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>❌ Step 4 Failed</strong> - API call failed completely.';
                        echo '</div>';
                    } elseif (is_array($estates)) {
                        if (isset($estates['estates'])) {
                            $count = count($estates['estates']);
                            echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                            echo '<strong>✅ Step 4 Successful!</strong> Found ' . $count . ' estates.';
                            echo '</div>';
                            
                            if ($count > 0) {
                                echo '<h3>Sample Estate:</h3>';
                                $first = $estates['estates'][0];
                                echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                                echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($first, true) . '</pre>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                            echo '<strong>⚠️ Step 4 Warning:</strong> No "estates" key in response.';
                            echo '</div>';
                            echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                            echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($estates, true) . '</pre>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                        echo '<strong>❌ Step 4 Failed</strong> - Unexpected response type.';
                        echo '</div>';
                        echo '<div style="background: white; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">';
                        echo '<pre style="font-size: 12px; overflow-x: auto;">' . print_r($estates, true) . '</pre>';
                        echo '</div>';
                    }
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Step 4 Failed</strong> - Authentication and client token required first.';
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
    
    <!-- Complete Test -->
    <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="color: #333; margin-top: 0;">🧪 Complete Flow Test</h2>
        <?php
        if ($api_url && $username && $password && $client_id) {
            try {
                $whise = new WhiseAPI();
                $test_result = $whise->test_connection();
                
                if ($test_result['success']) {
                    echo '<div style="background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>🎉 All Steps Successful!</strong><br>';
                    echo 'Clients found: ' . $test_result['clients_count'] . '<br>';
                    echo 'Estates found: ' . $test_result['estates_count'];
                    echo '</div>';
                } else {
                    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                    echo '<strong>❌ Test Failed:</strong> ' . $test_result['message'];
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin: 10px 0;">';
                echo '<strong>❌ Error:</strong> ' . $e->getMessage();
                echo '</div>';
            }
        } else {
            echo '<div style="background: #fff3e0; color: #ef6c00; padding: 15px; border-radius: 5px; margin: 10px 0;">';
            echo '<strong>⚠️ Skipped:</strong> Cannot run complete test without credentials.';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Instructions -->
    <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2196f3;">
        <h2 style="color: #1976d2; margin-top: 0;">📋 Troubleshooting Guide</h2>
        <ul style="line-height: 1.6;">
            <li><strong>Step 1 fails:</strong> Check username/password in WordPress admin</li>
            <li><strong>Step 2 fails:</strong> Your account might not have admin access</li>
            <li><strong>Step 3 fails:</strong> Check if Client ID is correct in the client list</li>
            <li><strong>Step 4 fails:</strong> Your account might not have any properties yet</li>
            <li><strong>All steps work but no estates:</strong> Contact WHISE to add properties to your account</li>
        </ul>
    </div>
    
</div>

<?php get_footer(); ?> 
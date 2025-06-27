<?php
/**
 * Whise API Configuration
 * Add ACF fields for Whise API settings
 */

// Add Whise API settings to ACF options page
add_action('acf/init', 'whise_add_acf_fields');

function whise_add_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_whise_api_settings',
            'title' => 'Whise API Settings',
            'fields' => array(
                array(
                    'key' => 'field_whise_api_url',
                    'label' => 'API URL',
                    'name' => 'whise_api_url',
                    'type' => 'url',
                    'default_value' => 'https://api.whise.eu',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_whise_username',
                    'label' => 'Username',
                    'name' => 'whise_username',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_whise_password',
                    'label' => 'Password',
                    'name' => 'whise_password',
                    'type' => 'password',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_whise_client_id',
                    'label' => 'Client ID',
                    'name' => 'whise_client_id',
                    'type' => 'number',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_whise_office_id',
                    'label' => 'Office ID',
                    'name' => 'whise_office_id',
                    'type' => 'number',
                    'instructions' => 'Optional: Office ID for filtering estates by office',
                ),
                array(
                    'key' => 'field_whise_cache_duration',
                    'label' => 'Cache Duration (seconds)',
                    'name' => 'whise_cache_duration',
                    'type' => 'number',
                    'default_value' => 3600, // 1 hour
                    'instructions' => 'How long to cache API responses (in seconds)',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-settings',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'left',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
    }
}

// Add cache functionality to Whise API
function whise_get_cached_data($key, $callback, $duration = null) {
    if ($duration === null) {
        $duration = get_field('whise_cache_duration', 'option') ?: 3600;
    }
    
    $cached_data = get_transient('whise_' . $key);
    
    if ($cached_data === false) {
        $cached_data = $callback();
        if ($cached_data !== false) {
            set_transient('whise_' . $key, $cached_data, $duration);
        }
    }
    
    return $cached_data;
}

// Clear Whise cache
function whise_clear_cache() {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_whise_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_whise_%'");
}

// Add admin action to clear cache
add_action('wp_ajax_clear_whise_cache', 'whise_ajax_clear_cache');
function whise_ajax_clear_cache() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    whise_clear_cache();
    wp_send_json_success('Cache cleared successfully');
}

// Add cache clear button to admin
add_action('admin_footer', 'whise_admin_footer_script');
function whise_admin_footer_script() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_theme-settings') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.acf-field-group_whise_api_settings').append(
                '<div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">' +
                '<h4>Cache Management</h4>' +
                '<p>Clear the Whise API cache if you\'re experiencing issues with outdated data.</p>' +
                '<button type="button" id="clear-whise-cache" class="button button-secondary">Clear Cache</button>' +
                '<span id="cache-status" style="margin-left: 10px;"></span>' +
                '</div>'
            );
            
            $('#clear-whise-cache').on('click', function() {
                var button = $(this);
                var status = $('#cache-status');
                
                button.prop('disabled', true).text('Clearing...');
                status.text('');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'clear_whise_cache',
                        nonce: '<?php echo wp_create_nonce('whise_cache_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            status.html('<span style="color: green;">✓ ' + response.data + '</span>');
                        } else {
                            status.html('<span style="color: red;">✗ Error clearing cache</span>');
                        }
                    },
                    error: function() {
                        status.html('<span style="color: red;">✗ Error clearing cache</span>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Clear Cache');
                        setTimeout(function() {
                            status.text('');
                        }, 3000);
                    }
                });
            });
        });
        </script>
        <?php
    }
} 
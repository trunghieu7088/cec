<?php
/*
Plugin Name: Redis Cache Settings
Description: Adds a Redis connection settings page in WordPress admin.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add menu item to admin
function redis_cache_settings_menu() {
    add_menu_page(
        'Redis Cache Settings',         // Page title
        'Redis Cache Settings',         // Menu title
        'manage_options',               // Capability
        'redis-cache-settings',         // Menu slug
        'redis_cache_settings_page',    // Function to display page
        'dashicons-performance',        // Icon (you can change)
        100                             // Position
    );
}
add_action('admin_menu', 'redis_cache_settings_menu');

// Register settings
function redis_cache_register_settings() {
    register_setting('redis_cache_options_group', 'redis_cache_options', 'redis_cache_options_sanitize');
}
add_action('admin_init', 'redis_cache_register_settings');

// Sanitize callback
function redis_cache_options_sanitize($input) {
    $sanitized = array();

    $sanitized['endpoint'] = sanitize_text_field($input['endpoint'] ?? '');
    $sanitized['port']     = $input['port'];
    $sanitized['username'] = sanitize_text_field($input['username'] ?? '');
    $sanitized['password'] = $input['password'] ?? ''; // Password should not be sanitized as text (may contain special chars)

    return $sanitized;
}

// Get current options
function get_redis_options() {
    return get_option('redis_cache_options', [
        'endpoint' => '127.0.0.1',
        'port'     => 6379,
        'username' => '',
        'password' => ''
    ]);
}

// Render the settings page
function redis_cache_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $options = get_redis_options();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('redis_cache_options_group');
            do_settings_sections('redis_cache_options_group');
            ?>

            <table class="form-table" role="presentation">
                <tr valign="top">
                    <th scope="row"><label for="redis_endpoint">Redis Endpoint</label></th>
                    <td>
                        <input type="text" 
                               name="redis_cache_options[endpoint]" 
                               id="redis_endpoint" 
                               value="<?php echo esc_attr($options['endpoint']); ?>" 
                               class="regular-text" 
                               placeholder="e.g. 127.0.0.1 or redis.example.com" />
                        <p class="description">Redis server hostname or IP address.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="redis_port">Port</label></th>
                    <td>
                        <input type="number" 
                               name="redis_cache_options[port]" 
                               id="redis_port" 
                              value="<?php echo esc_attr($options['port']); ?>" 
                               class="small-text" 
                               min="1" 
                               max="65535" />
                        <p class="description">Default Redis port is 6379.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="redis_username">Username (ACL)</label></th>
                    <td>
                        <input type="text" 
                               name="redis_cache_options[username]" 
                               id="redis Za_username" 
                               value="<?php echo esc_attr($options['username']); ?>" 
                               class="regular-text" 
                               placeholder="Leave empty if not using ACL" />
                        <p class="description">Only required for Redis 6+ with ACL enabled.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="redis_password">Password</label></th>
                    <td>
                        <input type="password" 
                               name="redis_cache_options[password]" 
                               id="redis_password" 
                               value="<?php echo esc_attr($options['password']); ?>" 
                               class="regular-text" 
                               autocomplete="new-password" />
                        <p class="description">Leave empty if no password is set.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <hr>
    </div>
    <?php
}
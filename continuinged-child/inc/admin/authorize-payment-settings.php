<?php
/**
 * Plugin Name: Authorize.net Payment Gateway Settings
 * Description: Admin settings for Authorize.net integration with LifterLMS
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class AuthorizeNet_Admin_Settings {
    
    private $option_name = 'authorizenet_settings';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }
    
    /**
     * Add menu item to WordPress admin
     */
    public function add_admin_menu() {
        add_menu_page(
            'Authorize.net Settings',           // Page title
            'Authorize.net',                    // Menu title
            'manage_options',                   // Capability
            'authorizenet-settings',            // Menu slug
            array($this, 'render_settings_page'), // Callback function
            'dashicons-lock',                   // Icon
            58                                  // Position
        );
    }
    
    /**
     * Register settings and fields
     */
    public function register_settings() {
        register_setting(
            'authorizenet_settings_group',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
    }
    
    /**
     * Sanitize user input
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Sanitize mode
        $sanitized['mode'] = isset($input['mode']) ? sanitize_text_field($input['mode']) : 'test';
        
        // Sanitize test credentials
        $sanitized['test_api_login_id'] = isset($input['test_api_login_id']) ? sanitize_text_field($input['test_api_login_id']) : '';
        $sanitized['test_transaction_key'] = isset($input['test_transaction_key']) ? sanitize_text_field($input['test_transaction_key']) : '';
        $sanitized['test_client_key'] = isset($input['test_client_key']) ? sanitize_text_field($input['test_client_key']) : '';
        
        // Sanitize live credentials
        $sanitized['live_api_login_id'] = isset($input['live_api_login_id']) ? sanitize_text_field($input['live_api_login_id']) : '';
        $sanitized['live_transaction_key'] = isset($input['live_transaction_key']) ? sanitize_text_field($input['live_transaction_key']) : '';
        $sanitized['live_client_key'] = isset($input['live_client_key']) ? sanitize_text_field($input['live_client_key']) : '';
        
        return $sanitized;
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles($hook) {
        if ('toplevel_page_authorizenet-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_style('authorizenet-admin-css', false);
       
    }
    
    /**
     * Render the settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        $options = get_option($this->option_name, array());
        $mode = isset($options['mode']) ? $options['mode'] : 'test';
        
        ?>
        <style>
                 .authnet-wrap {
                max-width: 900px;
                margin: 20px 0;
            }
            .authnet-header {
                background: #fff;
                padding: 20px 30px;
                border-left: 4px solid #2271b1;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .authnet-header h1 {
                margin: 0 0 10px 0;
                font-size: 24px;
            }
            .authnet-header p {
                margin: 0;
                color: #646970;
            }
            .authnet-mode-toggle {
                background: #fff;
                padding: 20px 30px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .authnet-toggle-container {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .authnet-toggle-switch {
                position: relative;
                width: 60px;
                height: 30px;
            }
            .authnet-toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }
            .authnet-toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: 0.4s;
                border-radius: 30px;
            }
            .authnet-toggle-slider:before {
                position: absolute;
                content: '';
                height: 22px;
                width: 22px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: 0.4s;
                border-radius: 50%;
            }
            input:checked + .authnet-toggle-slider {
                background-color: #2271b1;
            }
            input:checked + .authnet-toggle-slider:before {
                transform: translateX(30px);
            }
            .authnet-mode-label {
                font-size: 16px;
                font-weight: 600;
            }
            .authnet-mode-label.active {
                color: #2271b1;
            }
            .authnet-credentials-panel {
                background: #fff;
                padding: 30px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .authnet-credentials-panel h2 {
                margin-top: 0;
                padding-bottom: 15px;
                border-bottom: 2px solid #f0f0f1;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .authnet-mode-badge {
                font-size: 12px;
                padding: 4px 10px;
                border-radius: 3px;
                font-weight: 600;
                text-transform: uppercase;
            }
            .authnet-mode-badge.test {
                background: #fef7e0;
                color: #f0b429;
            }
            .authnet-mode-badge.live {
                background: #d5f3e5;
                color: #00a32a;
            }
            .authnet-form-row {
                margin-bottom: 20px;
            }
            .authnet-form-row label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #1d2327;
            }
            .authnet-form-row input[type='text'],
            .authnet-form-row input[type='password'] {
                width: 100%;
                max-width: 500px;
                padding: 10px 12px;
                font-size: 14px;
                border: 1px solid #dcdcde;
                border-radius: 4px;
            }
            .authnet-form-row input[type='text']:focus,
            .authnet-form-row input[type='password']:focus {
                border-color: #2271b1;
                outline: none;
                box-shadow: 0 0 0 1px #2271b1;
            }
            .authnet-form-row .description {
                margin-top: 5px;
                color: #646970;
                font-size: 13px;
            }
            .authnet-submit-container {
                background: #fff;
                padding: 20px 30px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            }
            .authnet-notice {
                background: #e7f5fe;
                border-left: 4px solid #2271b1;
                padding: 12px 15px;
                margin-bottom: 20px;
            }
            .authnet-notice p {
                margin: 0;
                color: #1d2327;
            }
        </style>
        <div class="wrap authnet-wrap">
            <div class="authnet-header">
                <h1>⚙️ Authorize.net Payment Gateway Settings</h1>
                <p>Cấu hình thông tin API để tích hợp Authorize.net với LifterLMS</p>
            </div>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>Cài đặt đã được lưu thành công!</strong></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="options.php">
                <?php settings_fields('authorizenet_settings_group'); ?>
                
                <!-- Mode Toggle -->
                <div class="authnet-mode-toggle">
                    <div class="authnet-toggle-container">
                        <span class="authnet-mode-label <?php echo $mode === 'test' ? 'active' : ''; ?>">
                            Test Mode
                        </span>
                        <label class="authnet-toggle-switch">
                            <input type="checkbox" 
                                   name="<?php echo $this->option_name; ?>[mode]" 
                                   value="live" 
                                   <?php checked($mode, 'live'); ?>
                                   id="authnet-mode-toggle">
                            <span class="authnet-toggle-slider"></span>
                        </label>
                        <span class="authnet-mode-label <?php echo $mode === 'live' ? 'active' : ''; ?>">
                            Live Mode
                        </span>
                    </div>
                </div>
                
                <!-- Test Credentials Panel -->
                <div class="authnet-credentials-panel">
                    <h2>
                        🧪 Test Environment Credentials
                        <span class="authnet-mode-badge test">Sandbox</span>
                    </h2>
                    
                    <div class="authnet-notice">
                        <p><strong>Lưu ý:</strong> Sử dụng thông tin từ Authorize.net Sandbox account để test.</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="test_api_login_id">API Login ID</label>
                        <input type="text" 
                               id="test_api_login_id" 
                               name="<?php echo $this->option_name; ?>[test_api_login_id]" 
                               value="<?php echo esc_attr(isset($options['test_api_login_id']) ? $options['test_api_login_id'] : ''); ?>"
                               placeholder="Nhập Test API Login ID">
                        <p class="description">API Login ID từ Authorize.net sandbox account</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="test_transaction_key">Transaction Key</label>
                        <input type="password" 
                               id="test_transaction_key" 
                               name="<?php echo $this->option_name; ?>[test_transaction_key]" 
                               value="<?php echo esc_attr(isset($options['test_transaction_key']) ? $options['test_transaction_key'] : ''); ?>"
                               placeholder="Nhập Test Transaction Key">
                        <p class="description">Transaction Key từ Authorize.net sandbox account</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="test_client_key">Client Key (Public Key)</label>
                        <input type="text" 
                               id="test_client_key" 
                               name="<?php echo $this->option_name; ?>[test_client_key]" 
                               value="<?php echo esc_attr(isset($options['test_client_key']) ? $options['test_client_key'] : ''); ?>"
                               placeholder="Nhập Test Client Key">
                        <p class="description">Public Client Key cho Accept.js integration</p>
                    </div>
                </div>
                
                <!-- Live Credentials Panel -->
                <div class="authnet-credentials-panel">
                    <h2>
                        🚀 Live Environment Credentials
                        <span class="authnet-mode-badge live">Production</span>
                    </h2>
                    
                    <div class="authnet-notice" style="background: #fef7e0; border-color: #f0b429;">
                        <p><strong>⚠️ Cảnh báo:</strong> Đây là thông tin production thực tế. Hãy bảo mật cẩn thận!</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="live_api_login_id">API Login ID</label>
                        <input type="text" 
                               id="live_api_login_id" 
                               name="<?php echo $this->option_name; ?>[live_api_login_id]" 
                               value="<?php echo esc_attr(isset($options['live_api_login_id']) ? $options['live_api_login_id'] : ''); ?>"
                               placeholder="Nhập Live API Login ID">
                        <p class="description">API Login ID từ Authorize.net production account</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="live_transaction_key">Transaction Key</label>
                        <input type="password" 
                               id="live_transaction_key" 
                               name="<?php echo $this->option_name; ?>[live_transaction_key]" 
                               value="<?php echo esc_attr(isset($options['live_transaction_key']) ? $options['live_transaction_key'] : ''); ?>"
                               placeholder="Nhập Live Transaction Key">
                        <p class="description">Transaction Key từ Authorize.net production account</p>
                    </div>
                    
                    <div class="authnet-form-row">
                        <label for="live_client_key">Client Key (Public Key)</label>
                        <input type="text" 
                               id="live_client_key" 
                               name="<?php echo $this->option_name; ?>[live_client_key]" 
                               value="<?php echo esc_attr(isset($options['live_client_key']) ? $options['live_client_key'] : ''); ?>"
                               placeholder="Nhập Live Client Key">
                        <p class="description">Public Client Key cho Accept.js integration</p>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="authnet-submit-container">
                    <?php submit_button('💾 Lưu Cài Đặt', 'primary large'); ?>
                </div>
            </form>
        </div>
        <?php
    }
}

// Initialize the class
new AuthorizeNet_Admin_Settings();

/**
 * Helper function to get Authorize.net credentials
 * Sử dụng trong code payment gateway của bạn
 */
function authorizenet_get_credentials() {
    $options = get_option('authorizenet_settings', array());
    $mode = isset($options['mode']) ? $options['mode'] : 'test';
    
    if ($mode === 'live') {
        return array(
            'mode' => 'live',
            'api_login_id' => isset($options['live_api_login_id']) ? $options['live_api_login_id'] : '',
            'transaction_key' => isset($options['live_transaction_key']) ? $options['live_transaction_key'] : '',
            'client_key' => isset($options['live_client_key']) ? $options['live_client_key'] : '',
        );
    } else {
        return array(
            'mode' => 'test',
            'api_login_id' => isset($options['test_api_login_id']) ? $options['test_api_login_id'] : '',
            'transaction_key' => isset($options['test_transaction_key']) ? $options['test_transaction_key'] : '',
            'client_key' => isset($options['test_client_key']) ? $options['test_client_key'] : '',
        );
    }
}

/**
 * Helper function to check if in test mode
 */
function authorizenet_is_test_mode() {
    $options = get_option('authorizenet_settings', array());
    $mode = isset($options['mode']) ? $options['mode'] : 'test';
    return $mode === 'test';
}
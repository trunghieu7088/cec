<?php
/**
 * Plugin Name: Init DB - Complete Course CE Hours
 * Description: Tạo bảng complete_course_ce_hours để lưu CE hours hoàn thành khóa học.
 */

// Thêm menu con vào Settings
add_action('admin_menu', 'register_ce_hours_menu');
function register_ce_hours_menu() {
    add_submenu_page(
        'tools.php',
        'Create CE Hours Table',
        'Create CE Hours Table',
        'manage_options',
        'ce-hours-db',
        'ce_hours_db_page'
    );
}

// Hàm render trang admin
function ce_hours_db_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'complete_course_ce_hours';
    $message = '';

    // Xử lý submit form
    if (isset($_POST['init_db']) && check_admin_referer('init_ce_hours_action', 'init_ce_hours_nonce')) {
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT(20) UNSIGNED NOT NULL,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                date DATETIME NOT NULL,
                ce_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                PRIMARY KEY (id),
                KEY user_id (user_id),
                KEY course_id (course_id),
                KEY date (date)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            if ($wpdb->last_error) {
                $message = '<div class="error"><p>Error: ' . esc_html($wpdb->last_error) . '</p></div>';
            } else {
                $message = '<div class="updated"><p>Table <strong>' . esc_html($table_name) . '</strong> has been created successfully!</p></div>';
            }
        } else {
            $message = '<div class="notice notice-info"><p>Table <strong>' . esc_html($table_name) . '</strong> already exists!</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Init Database: Complete Course CE Hours</h1>
        <?php echo $message; ?>
        <form method="post" action="">
            <?php wp_nonce_field('init_ce_hours_action', 'init_ce_hours_nonce'); ?>
            <p>Click the button below to create the table: <strong><?php echo esc_html($table_name); ?></strong></p>
            <p><strong>Structure:</strong> ID, user_id, course_id, date (datetime), ce_hours (decimal)</p>
            <input type="submit" name="init_db" class="button button-primary" value="Init CE Hours DB">
        </form>
    </div>
    <?php
}

// Tự động tạo bảng khi kích hoạt plugin (nếu cần)
register_activation_hook(__FILE__, 'create_ce_hours_table');
function create_ce_hours_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'complete_course_ce_hours';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            course_id BIGINT(20) UNSIGNED NOT NULL,
            date DATETIME NOT NULL,
            ce_hours DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY course_id (course_id),
            KEY date (date)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
<?php
add_action('admin_menu', 'register_course_completion_menu');
function register_course_completion_menu() {
    add_submenu_page(
        'options-general.php', // Menu cha: Settings
        'Course Completion DB', // Tiêu đề trang
        'Course Completion', // Tên menu
        'manage_options', // Quyền truy cập
        'course-completion-db', // Slug
        'course_completion_db_page' // Hàm render trang
    );
}

// Hàm render trang admin
function course_completion_db_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    $message = '';

    // Xử lý khi form được submit
    if (isset($_POST['init_db']) && check_admin_referer('init_db_action', 'init_db_nonce')) {
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Tạo bảng
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $table_name (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                completion_code VARCHAR(6) NOT NULL,
                completed_date DATETIME NOT NULL,
                course_id BIGINT(20) UNSIGNED NOT NULL,
                is_convert TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // Kiểm tra lỗi
            if ($wpdb->last_error) {
                $message = '<div class="error"><p>Error:' . esc_html($wpdb->last_error) . '</p></div>';
            } else {
                $message = '<div class="updated"><p>Table ' . esc_html($table_name) . ' has been created!</p></div>';
            }
        } else {
            $message = '<div class="notice notice-info"><p>Table ' . esc_html($table_name) . ' already existed!</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Init Database Course Completion</h1>
        <?php echo $message; ?>
        <form method="post" action="">
            <?php wp_nonce_field('init_db_action', 'init_db_nonce'); ?>
            <p>Click to create the table <strong><?php echo esc_html($table_name); ?></strong></p>
            <input type="submit" name="init_db" class="button button-primary" value="Init DB">
        </form>
    </div>
    <?php
}

// Kích hoạt plugin hoặc kiểm tra version để đảm bảo bảng được tạo đúng
register_activation_hook(__FILE__, 'create_course_completion_table');
function create_course_completion_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            completion_code VARCHAR(6) NOT NULL,
            completed_date DATETIME NOT NULL,
            course_id BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
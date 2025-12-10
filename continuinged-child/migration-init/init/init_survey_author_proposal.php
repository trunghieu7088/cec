<?php
// ===============================================
// Thêm menu vào Tools để tạo 2 bảng custom
// Chỉ cần dán vào functions.php của theme
// ===============================================

if ( ! defined('ABSPATH') ) exit;

// Thêm menu vào Tools
add_action('admin_menu', 'cst_add_tools_menu');
function cst_add_tools_menu() {
    add_submenu_page(
        'tools.php',
        'Create Survey Tables',
        'Create Survey Tables',
        'manage_options',
        'cst-create-tables',
        'cst_render_admin_page'
    );
}

// Trang hiển thị
function cst_render_admin_page() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    global $wpdb;

    $table1 = $wpdb->prefix . 'survey_responses';
    $table2 = $wpdb->prefix . 'author_proposals';

    $exists1 = $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $table1) ) == $table1;
    $exists2 = $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $table2) ) == $table2;

    ?>
    <div class="wrap">
        <h1>Create Custom Tables</h1>
        <p>Use the button below to create the two required tables for survey responses and author course proposals.</p>

        <div class="card" style="max-width: 700px;">
            <h2>Table Status</h2>
            <ul>
                <li><strong><?php echo esc_html($table1); ?></strong> → <?php echo $exists1 ? '<span style="color:green;">Already exists</span>' : '<span style="color:#b33;">Not found</span>'; ?></li>
                <li><strong><?php echo esc_html($table2); ?></strong> → <?php echo $exists2 ? '<span style="color:green;">Already exists</span>' : '<span style="color:#b33;">Not found</span>'; ?></li>
            </ul>

            <?php if ( $exists1 && $exists2 ): ?>
                <div class="notice notice-success inline">
                    <p><strong>All tables are already created and ready to use!</strong></p>
                </div>
            <?php else: ?>
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('cst_create_tables_action', 'cst_nonce'); ?>
                    <input type="hidden" name="action" value="cst_create_tables">
                    <p>
                        <button type="submit" class="button button-primary button-large">
                            Create Missing Tables Now
                        </button>
                    </p>
                    <p class="description">This action only creates tables that do not exist yet. It is safe to run multiple times.</p>
                </form>
            <?php endif; ?>
        </div>

        <?php
        // Hiển thị thông báo thành công nếu vừa tạo xong
        if ( isset($_GET['cst_success']) && $_GET['cst_success'] == 1 ) {
            echo '<div class="notice notice-success is-dismissible"><p>Custom tables have been created successfully!</p></div>';
        }
        ?>
    </div>
    <?php
}

// Xử lý khi bấm nút tạo bảng
add_action('admin_post_cst_create_tables', 'cst_handle_create_tables');
function cst_handle_create_tables() {
    if ( ! current_user_can('manage_options') ) {
        wp_die('Permission denied.');
    }

    check_admin_referer('cst_create_tables_action', 'cst_nonce');

    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = $wpdb->get_charset_collate();

    // Bảng 1: wp_survey_responses
    $table1 = $wpdb->prefix . 'survey_responses';
    $sql1 = "CREATE TABLE `$table1` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `survey_type` varchar(50) DEFAULT 'general',
        `survey_date` datetime NOT NULL,
        `user_id` bigint(20) UNSIGNED DEFAULT NULL,
        `user_name` varchar(100) DEFAULT NULL,
        `user_email` varchar(100) DEFAULT NULL,
        `user_phone` varchar(20) DEFAULT NULL,
        `course_id` bigint(20) UNSIGNED DEFAULT NULL,
        `survey_data` longtext NOT NULL,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` varchar(255) DEFAULT NULL,
        `referrer` varchar(255) DEFAULT NULL,
        `status` varchar(20) DEFAULT 'submitted',
        `notify_new_courses` tinyint(1) DEFAULT 0,
        `created_at` datetime NOT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `survey_type` (`survey_type`),
        KEY `survey_date` (`survey_date`),
        KEY `user_id` (`user_id`)
    ) $charset_collate;";

    // Bảng 2: wp_author_proposals
    $table2 = $wpdb->prefix . 'author_proposals';
    $sql2 = "CREATE TABLE `$table2` (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `submitted_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `phone` varchar(50) NOT NULL,
        `address` text,
        `course_name` varchar(255) NOT NULL,
        `course_level` enum('introductory','intermediate','advanced') NOT NULL,
        `hours` int(11) NOT NULL,
        `description` text NOT NULL,
        `objectives` text NOT NULL,
        `outline` text NOT NULL,
        `outline_file` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded outline file',
        `diversity` text NOT NULL,
        `references_text` text NOT NULL,
        `is_first_time` tinyint(1) NOT NULL DEFAULT 1,
        `cv_file` varchar(255) DEFAULT NULL COMMENT 'Path to uploaded CV file',
        `has_conflict` tinyint(1) NOT NULL DEFAULT 0,
        `conflict_explanation` text,
        `apa_statement` text NOT NULL,
        `status` enum('pending','reviewed','approved','rejected') NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`id`),
        KEY `status` (`status`),
        KEY `submitted_date` (`submitted_date`),
        KEY `email` (`email`)
    ) $charset_collate;";

    // Thực thi tạo bảng (chỉ tạo nếu chưa có)
    dbDelta( $sql1 );
    dbDelta( $sql2 );

    // Quay lại trang với thông báo thành công
    wp_redirect( add_query_arg( 'cst_success', '1', wp_get_referer() ) );
    exit;
}
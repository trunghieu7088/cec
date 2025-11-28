<?php 
// ===================================================================
// Tạo bảng llms_email_queue + menu admin nhỏ (chỉ dành cho theme)
// ===================================================================

if ( ! function_exists( 'init_llms_email_queue_db_menu' ) ) :
add_action( 'admin_menu', 'init_llms_email_queue_db_menu' );
function init_llms_email_queue_db_menu() {
    add_submenu_page(
        'tools.php',
        'Create Email Queue Table',
        'Create Email Queue Table',
        'manage_options',
        'llms-email-queue-db',
        'llms_email_queue_db_page'
    );
}
endif;

// Trang admin
function llms_email_queue_db_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'llms_email_queue';
    $message    = '';

    // Xử lý tạo bảng khi submit
    if ( isset( $_POST['init_llms_queue'] ) && check_admin_referer( 'init_llms_queue_action', 'init_llms_queue_nonce' ) ) {
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            queue_id varchar(100) NOT NULL,
            course_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            user_email varchar(255) NOT NULL,
            course_data longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            error_msg text,
            created_at datetime NOT NULL,
            sent_at datetime,
            PRIMARY KEY (id),
            KEY queue_id (queue_id),
            KEY course_id (course_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        if ( $wpdb->last_error ) {
            $message = '<div class="error notice"><p><strong>Lỗi:</strong> ' . esc_html( $wpdb->last_error ) . '</p></div>';
        } else {
            $message = '<div class="updated notice"><p>Bảng <strong>' . esc_html( $table_name ) . '</strong> đã được tạo hoặc đã tồn tại.</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Khởi tạo bảng LLMS Email Queue</h1>
        
        <?php echo $message; ?>

        <div class="card" style="max-width:850px;">
            <p>Bảng: <code><?php echo esc_html( $table_name ); ?></code></p>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'init_llms_queue_action', 'init_llms_queue_nonce' ); ?>
                <p>
                    <input type="submit" name="init_llms_queue" class="button button-primary" value="Tạo / Kiểm tra bảng llms_email_queue">
                </p>
            </form>

            <h3>Cấu trúc bảng:</h3>
            <ul style="font-family:Consolas,Monaco,monospace;background:#f9f9f9;padding:15px;border:1px solid #ddd;">
                <li>id (bigint AUTO_INCREMENT PK)</li>
                <li>queue_id (varchar 100)</li>
                <li>course_id (bigint)</li>
                <li>user_id (bigint)</li>
                <li>user_email (varchar 255)</li>
                <li>course_data (longtext)</li>
                <li>status (varchar 20, default 'pending')</li>
                <li>error_msg (text)</li>
                <li>created_at (datetime)</li>
                <li>sent_at (datetime)</li>
            </ul>
        </div>
    </div>
    <?php
}

// (Tùy chọn) Tự động tạo bảng khi theme được kích hoạt lần đầu
add_action( 'after_switch_theme', 'create_llms_email_queue_table_on_theme_activation' );
function create_llms_email_queue_table_on_theme_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'llms_email_queue';

    if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            queue_id varchar(100) NOT NULL,
            course_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            user_email varchar(255) NOT NULL,
            course_data longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            error_msg text,
            created_at datetime NOT NULL,
            sent_at datetime,
            PRIMARY KEY (id),
            KEY queue_id (queue_id),
            KEY course_id (course_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
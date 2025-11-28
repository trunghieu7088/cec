<?php
add_action('admin_menu', 'register_course_completion_menu');
function register_course_completion_menu() {
    add_submenu_page(
        'tools.php',
        'Create Completion Code Table',
        'Create Completion Code Table',
        'manage_options',
        'course-completion-db',
        'course_completion_db_page'
    );
}

// Hàm tạo/xóa bảng chung
function course_completion_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        completion_code VARCHAR(6) NOT NULL,
        completed_date DATETIME NOT NULL,
        course_id BIGINT(20) UNSIGNED NOT NULL,
        is_convert TINYINT(1) NOT NULL DEFAULT 0,
        score_test DECIMAL(5,2) NULL DEFAULT NULL,
        PRIMARY KEY (id),
        KEY course_id (course_id),
        KEY completion_code (completion_code)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    return $wpdb->last_error === '' ? true : false;
}

function course_completion_drop_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Trang admin
function course_completion_db_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    $message = '';

    // Xử lý Init DB (tạo bảng nếu chưa có)
    if (isset($_POST['init_db']) && check_admin_referer('init_db_action', 'init_db_nonce')) {
        if (course_completion_create_table()) {
            $message = '<div class="updated"><p>Bảng <strong>' . esc_html($table_name) . '</strong> đã được tạo thành công (hoặc đã tồn tại và được cập nhật cấu trúc)!</p></div>';
        } else {
            $message = '<div class="error"><p>Lỗi khi tạo bảng: ' . esc_html($wpdb->last_error) . '</p></div>';
        }
    }

    // Xử lý Xóa & tạo lại DB
    if (isset($_POST['reset_db']) && check_admin_referer('reset_db_action', 'reset_db_nonce')) {
        course_completion_drop_table();
        if (course_completion_create_table()) {
            $message = '<div class="updated"><p>Bảng đã được <strong>xóa và tạo lại thành công</strong> với cấu trúc mới (có cột score_test)!</p></div>';
        } else {
            $message = '<div class="error"><p>Lỗi khi tạo lại bảng: ' . esc_html($wpdb->last_error) . '</p></div>';
        }
    }

    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
    ?>

    <div class="wrap">
        <h1>Quản lý Database Course Completion</h1>

        <?php echo $message; ?>

        <div class="card" style="max-width: 800px; margin: 20px 0;">
            <h2>Trạng thái bảng</h2>
            <p>
                Bảng: <code><?php echo esc_html($table_name); ?></code><br>
                <strong style="color: <?php echo $table_exists ? 'green' : 'red'; ?>">
                    <?php echo $table_exists ? 'Đã tồn tại' : 'Chưa tồn tại'; ?>
                </strong>
            </p>

            <!-- Nút tạo bảng (nếu chưa có hoặc muốn cập nhật cấu trúc) -->
            <form method="post" style="display:inline-block; margin-right: 10px;">
                <?php wp_nonce_field('init_db_action', 'init_db_nonce'); ?>
                <input type="submit" name="init_db" class="button button-primary" value="Tạo / Cập nhật DB">
                <p class="description">Chỉ tạo bảng nếu chưa có, hoặc cập nhật cấu trúc (thêm cột score_test nếu thiếu)</p>
            </form>

            <!-- Nút XÓA & TẠO LẠI (có xác nhận) -->
            <form method="post" style="display:inline-block;" onsubmit="return confirm('⚠️ CẢNH BÁO: Toàn bộ dữ liệu trong bảng course completion sẽ bị XÓA VĨNH VIỄN!\n\nBạn có chắc chắn muốn tiếp tục không?');">
                <?php wp_nonce_field('reset_db_action', 'reset_db_nonce'); ?>
                <input type="submit" name="reset_db" class="button button-danger" value="Xóa & Tạo lại DB" style="background:#d63638; color:white; border:none;">
                <p class="description" style="color:#d63638;"><strong>Xóa hoàn toàn</strong> bảng và tạo lại với cột score_test mới</p>
            </form>
        </div>

        <?php if ($table_exists): ?>
            <h3>Cấu trúc bảng hiện tại:</h3>
            <?php
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
            echo '<ul>';
            foreach ($columns as $col) {
                $null = $col->Null == 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col->Default !== null ? "DEFAULT " . $col->Default : '';
                echo '<li><code>' . esc_html($col->Field) . ' ' . esc_html($col->Type) . ' ' . $null . ' ' . $default . '</code></li>';
            }
            echo '</ul>';
            ?>
        <?php endif; ?>
    </div>

    <style>
        .button-danger:hover { background:#b32d2e !important; }
    </style>
    <?php
}

// Khi kích hoạt plugin → tạo bảng với cấu trúc mới (có score_test)
register_activation_hook(__FILE__, 'course_completion_activate');
function course_completion_activate() {
    course_completion_create_table();
}
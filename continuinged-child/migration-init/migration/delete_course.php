<?php
// === 1. Thêm menu con trong Tools ===
function add_delete_all_courses_tool() {
    add_management_page(
        'Xóa toàn bộ Course',          // Page title
        'Xóa hết Course',              // Menu title
        'manage_options',              // Capability (chỉ admin)
        'delete-all-courses',          // Menu slug
        'render_delete_all_courses_page' // Hàm hiển thị trang
    );
}
add_action('admin_menu', 'add_delete_all_courses_tool');

// === 2. Nội dung trang + form ===
function render_delete_all_courses_page() {
    // Chỉ admin mới vào được (dù đã kiểm tra capability rồi nhưng thêm 1 lớp nữa cho chắc)
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    echo '<div class="wrap">
        <h1>Xóa vĩnh viễn toàn bộ Course</h1>
        <div class="card" style="max-width:600px;">
            <p><strong>Số lượng course hiện có:</strong> ' . wp_count_posts('course')->publish +  wp_count_posts('course')->pending  + wp_count_posts('course')->draft + wp_count_posts('course')->private . '</p>
            
            <form method="post" onsubmit="return confirm(\'CẢNH BÁO: Tất cả course sẽ bị XÓA VĨNH VIỄN ngay lập tức, không vào thùng rác, không thể khôi phục!\n\nBạn có chắc chắn muốn tiếp tục không?\');">
                ' . wp_nonce_field('do_delete_all_courses', '_nonce', true, false) . '
                <p>
                    <input type="submit" name="delete_all_courses_now" class="button button-primary button-hero" style="background:#d63638;border-color:#b32d2e;color:white;" value="XÓA HẲN TOÀN BỘ COURSE NGAY BÂY GIỜ">
                </p>
            </form>
            
            <p><small>Sau khi bấm nút đỏ trên, toàn bộ course sẽ bị xóa vĩnh viễn trong vòng vài giây (tùy số lượng).</small></p>
        </div>
    </div>';
    
    // === 3. Xử lý khi bấm nút ===
    if (isset($_POST['delete_all_courses_now'])) {
        if (!wp_verify_nonce($_POST['_nonce'], 'do_delete_all_courses')) {
            wp_die('Nonce không hợp lệ!');
        }

        global $wpdb;
        
        // Lấy tất cả ID của course (bỏ auto-draft cho chắc)
        $course_ids = $wpdb->get_col("
            SELECT ID FROM $wpdb->posts 
            WHERE post_type = 'course' 
            AND post_status != 'auto-draft'
        ");

        $deleted = 0;
        if (!empty($course_ids)) {
            foreach ($course_ids as $id) {
                wp_delete_post($id, true); // true = xóa vĩnh viễn, không vào thùng rác
                $deleted++;
            }
        }

        echo '<div class="updated notice is-dismissible"><p>
                <strong>HOÀN TẤT!</strong> Đã xóa vĩnh viễn <strong>' . $deleted . '</strong> course.
              </p></div>';
    }
}
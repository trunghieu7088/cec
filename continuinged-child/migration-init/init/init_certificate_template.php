<?php
// Thêm menu vào Tools trong admin
add_action('admin_menu', 'cec_add_certificate_menu');

function cec_add_certificate_menu() {
    add_management_page(
        'Create Certificate Template',     // Page title
        'Create certificate template',     // Menu title
        'manage_options',                  // Capability cần thiết
        'cec-create-certificate',          // Menu slug
        'cec_render_certificate_page'      // Hàm render trang
    );
}

function cec_render_certificate_page() {
    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    // Xử lý form submit
    if (isset($_POST['cec_create_certificate']) && wp_verify_nonce($_POST['cec_nonce'], 'cec_create_certificate')) {
        
        $post_id = wp_insert_post([
            'post_title'   => 'CEC Certificate of Completion',
            'post_status'  => 'publish',
            'post_type'    => 'llms_certificate',   // LearnDash/LifterLMS certificate post type
            'post_author'  => get_current_user_id(),
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            echo '<div class="updated"><p><strong>Thành công!</strong> Đã tạo chứng chỉ mới với ID: <code>' . $post_id . '</code></p></div>';
            
            // Optional: Tự động redirect đến trang edit certificate
            // wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit'));
            // exit;
        } else {
            echo '<div class="error"><p>Có lỗi xảy ra khi tạo chứng chỉ.</p></div>';
        }
    }
    ?>

    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('cec_create_certificate', 'cec_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Tạo chứng chỉ mẫu</th>
                    <td>
                        <p>Khi nhấn nút bên dưới, hệ thống sẽ tạo một chứng chỉ mới với tiêu đề <strong>"CEC Certificate of Completion"</strong> và trạng thái <strong>Publish</strong>.</p>
                        <p>Bạn có thể chỉnh sửa nội dung chứng chỉ (template HTML/CSS) ngay sau khi tạo.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Tạo chứng chỉ mới', 'primary', 'cec_create_certificate'); ?>
        </form>
    </div>

    <?php
}
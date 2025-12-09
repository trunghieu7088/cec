<?php
// Thêm menu vào Tools
function cct_add_admin_menu() {
    add_management_page(
        'Create Course Transient',          // Page title
        'Create Course Transient',          // Menu title
        'manage_options',                   // Capability
        'create-course-transient',          // Menu slug
        'cct_render_admin_page'             // Callback function
    );
}
add_action( 'admin_menu', 'cct_add_admin_menu' );

// Trang admin
function cct_render_admin_page() {
    // Kiểm tra quyền
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Bạn không có quyền truy cập trang này.' );
    }

    $message = '';
    $execution_time = '';

    // Xử lý khi submit form
     delete_transient( 'all_courses_search_data' );
    if ( isset( $_POST['cct_rebuild_transient'] ) && wp_verify_nonce( $_POST['cct_nonce'], 'cct_rebuild' ) ) {
        
        // Xóa transient cũ trước
        delete_transient( 'all_courses_search_data' );

        // Gọi hàm xây dựng lại transient (hàm của bạn)
        // Vì hàm gốc trả về JSON qua wp_ajax, ta sẽ gọi trực tiếp logic bên trong
        // Ta sẽ copy logic cần thiết vào đây để chạy độc lập

        $start_time = microtime(true);

        $courses = get_posts([
            'post_type'      => 'course',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);

        $data = [];
        foreach ( $courses as $post_id ) {
            $categories = get_the_terms( $post_id, 'course_cat' );
            $cat_names = [];
            $cat_slugs = [];

            if ( $categories && ! is_wp_error( $categories ) ) {
                foreach ( $categories as $cat ) {
                    $cat_names[] = $cat->name;
                    $cat_slugs[] = $cat->slug;
                }
            }

            $data[] = [
                'id'              => $post_id,
                'title'           => html_entity_decode( get_the_title( $post_id ) ),
                'url'             => get_permalink( $post_id ),
                'categories'      => $cat_names,
                'cat_slugs'       => $cat_slugs,
                'categories_str'  => implode(', ', $cat_names),
            ];
        }

        // Lưu transient mới: 6 tháng
        $set = set_transient( 'all_courses_search_data', $data, 6 * MONTH_IN_SECONDS );

        $execution_time = round( ( microtime(true) - $start_time ) * 1000, 2 );

        if ( $set ) {
            $message = sprintf(
                '<div class="notice notice-success"><p><strong>Thành công!</strong> Đã tạo lại transient với <strong>%d</strong> khóa học. Thời gian: <strong>%s ms</strong>.</p></div>',
                count( $data ),
                $execution_time
            );
        } else {
            $message = '<div class="notice notice-error"><p>Lỗi khi lưu transient!</p></div>';
        }
    }
    ?>

    <div class="wrap">
        <h1>Tạo lại Transient Khóa học</h1>

        <?php echo $message; ?>

        <div class="card" style="max-width: 600px;">
            <h2>Transient: <code>all_courses_search_data</code></h2>
            <p>Nhấn nút bên dưới để <strong>xóa transient cũ</strong> và <strong>tạo lại dữ liệu mới</strong> từ tất cả khóa học.</p>
            
            <form method="post">
                <?php wp_nonce_field( 'cct_rebuild', 'cct_nonce' ); ?>
                <p>
                    <input type="submit" 
                           name="cct_rebuild_transient" 
                           class="button button-primary button-large" 
                           value="Tạo lại Transient ngay" 
                           onclick="this.value='Đang xử lý...'; this.disabled=true; this.form.submit();">
                </p>
            </form>

            <?php if ( $execution_time ) : ?>
                <p><em>Thời gian thực thi lần cuối: <strong><?php echo $execution_time; ?> ms</strong></em></p>
            <?php endif; ?>

            <hr>
            <p><small>Transient sẽ tự động hết hạn sau 6 tháng. Bạn chỉ cần dùng công cụ này khi thay đổi nhiều khóa học hoặc muốn làm mới dữ liệu tìm kiếm ngay lập tức.</small></p>
        </div>
    </div>

    <?php
}
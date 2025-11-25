<?php
/**
 * LifterLMS Course Migration Script
 * Migrate courses from old database table to wp_posts
 * 
 * Usage: Run this in WordPress admin or via WP-CLI
 */

function migrate_courses_to_lifterlms() {
    global $wpdb;
    
    // Lấy tất cả courses từ bảng cũ
    $old_courses = $wpdb->get_results("SELECT * FROM courses", ARRAY_A);
    
    if (empty($old_courses)) {
        echo "Không tìm thấy course nào để migrate.\n";
        return;
    }
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($old_courses as $course) {
        try {
            // Xác định post_status
            $post_status = (isset($course['real_status']) && strtolower($course['real_status']) === 'publish') 
                ? 'publish' 
                : 'pending';
            
            // Chuyển đổi FirstPublished sang format WordPress
            $post_date = !empty($course['FirstPublished']) 
                ? date('Y-m-d H:i:s', strtotime($course['FirstPublished'])) 
                : current_time('mysql');
            
            // Tạo course post
            $post_data = array(
                'post_title'    => $course['CourseName'],
                'post_type'     => 'course',
                'post_status'   => $post_status,
                'post_date'     => $post_date,
                'post_date_gmt' => get_gmt_from_date($post_date),
            );
            
            $post_id = wp_insert_post($post_data, true);
            
            if (is_wp_error($post_id)) {
                throw new Exception($post_id->get_error_message());
            }
            
            // Lưu post meta
            update_post_meta($post_id, 'course_stable_id', $course['CourseId']);
            update_post_meta($post_id, '_llms_ce_hours', $course['CreditHours']);
            
            // TODO: Xử lý CoursePrice ở đây
            // update_post_meta($post_id, '_llms_price', $course['CoursePrice']);
            
            if (!empty($course['LastRevised'])) {
                update_post_meta($post_id, '_course_last_revised', $course['LastRevised']);
            }
            
            if (!empty($course['copyright_text'])) {
                update_post_meta($post_id, '_course_copyright', $course['copyright_text']);
            }
            
            if (!empty($course['main_content'])) {
                update_post_meta($post_id, '_course_main_content', $course['main_content']);
            }
            
            if (!empty($course['learning_objectives'])) {
                update_post_meta($post_id, '_course_objectives', $course['learning_objectives']);
            }
            
            if (!empty($course['introduction'])) {
                update_post_meta($post_id, '_course_introduction', $course['introduction']);
            }
            
            if (!empty($course['outline'])) {
                update_post_meta($post_id, '_course_outline', $course['outline']);
            }
            
            // Xử lý Category
            if (!empty($course['category'])) {
                // Tách các category bằng dấu phẩy
                $categories = array_map('trim', explode(',', $course['category']));
                $category_ids = array();
                
                foreach ($categories as $cat_name) {
                    if (empty($cat_name)) continue;
                    
                    $category_term = get_term_by('name', $cat_name, 'course_cat');
                    if ($category_term) {
                        $category_ids[] = $category_term->term_id;
                    }
                }
                
                // Gán tất cả categories cho course
                if (!empty($category_ids)) {
                    wp_set_object_terms($post_id, $category_ids, 'course_cat');
                }
            }
            
            // Xử lý Difficulty
            if (!empty($course['difficulty'])) {
                $difficulty_term = get_term_by('name', $course['difficulty'], 'course_difficulty');
                if ($difficulty_term) {
                    wp_set_object_terms($post_id, $difficulty_term->term_id, 'course_difficulty');
                }
            }
            
            // Xử lý Author/Instructor
            if (!empty($course['AuthorId'])) {
                $author_ids = process_course_instructors($course['AuthorId']);
                if (!empty($author_ids)) {
                    update_post_meta($post_id, '_llms_instructors', $author_ids);
                }
            }

            //xử lý label highlight
             if (!empty($course['Highlight'])) {
                update_post_meta($post_id, '_status_update_label', $course['Highlight']);
            }

            //xử lý course plan
            create_course_access_plan( $post_id, $course['CoursePrice'], $course['CourseName'] );
            
            $success_count++;
            echo "✓ Migrated: {$course['CourseName']} (ID: {$post_id})\n";
            
        } catch (Exception $e) {
            $error_count++;
            echo "✗ Error migrating course {$course['CourseId']}: {$e->getMessage()}\n";
        }
    }
    
    echo "\n=== Migration Complete ===\n";
    echo "Success: {$success_count}\n";
    echo "Errors: {$error_count}\n";
}

/**
 * Xử lý danh sách instructors cho course
 * 
 * @param string|int $author_id AuthorId từ bảng cũ (có thể là 1 ID hoặc nhiều IDs phân cách bởi dấu phẩy)
 * @return array Mảng serialized format LifterLMS
 */
function process_course_instructors($author_id) {
    global $wpdb;
    
    // Xử lý trường hợp nhiều authors (nếu có)
    $author_ids = is_array($author_id) ? $author_id : explode(',', $author_id);
    $instructors = array();
    
    foreach ($author_ids as $index => $old_author_id) {
        $old_author_id = trim($old_author_id);
        
        // Tìm user có meta key author_stable_id
        $user_id = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->usermeta} 
            WHERE meta_key = 'author_stable_id' 
            AND meta_value = %s 
            LIMIT 1",
            $old_author_id
        ));
        
        if ($user_id) {
            $instructors[$index] = array(
                'label'      => 'Author',
                'visibility' => 'visible',
                'id'         => intval($user_id)
            );
        }
    }
    
    return !empty($instructors) ? $instructors : array();
}

/**
 * Helper function để chạy migration
 * Có thể gọi từ admin page hoặc WP-CLI
 */
function run_lifterlms_migration() {
    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Tăng timeout và memory limit
    set_time_limit(0);
    ini_set('memory_limit', '512M');
    
    echo "<pre>";
    migrate_courses_to_lifterlms();
    echo "</pre>";
}

// Uncomment dòng dưới để chạy migration
// add_action('admin_init', 'run_lifterlms_migration');

/**
 * WP-CLI Command (nếu dùng WP-CLI)
 * Usage: wp migrate-lifterlms-courses
 */
/*
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('migrate-lifterlms-courses', function() {
        migrate_courses_to_lifterlms();
    });
} */


//handing access plan & price
function create_course_access_plan( $course_id, $price, $title ) {
    
    // Kiểm tra course tồn tại
    if ( ! get_post( $course_id ) || get_post_type( $course_id ) !== 'course' ) {
        return false;
    }
    
    // Chuẩn bị dữ liệu cho access plan
    $access_plan_data = array(
        'title'   => sanitize_text_field('Access Plan for: '. $title ),
        'product_id'=> $course_id,
        'access_expiration'=>'lifetime',
        'availability'=>'open',
        'enroll_text'=>'Take the course',
        'frequency'=>0,
        'is_free'=>'no',
        'on_sale'=>'no',
        'price'=>floatval( $price ),
    );
    
    // Tạo access plan
    $access_plan_id = llms_insert_access_plan( $access_plan_data );
    
    // Kiểm tra có lỗi không
    if ( is_wp_error( $access_plan_id ) ) {
        error_log( 'Lỗi tạo access plan: ' . $access_plan_id->get_error_message() );
        return false;
    }  
  
    
    return $access_plan_id;
}

//form handle
// Thêm menu trong admin
add_action('admin_menu', 'add_lifterlms_migration_menu');

function add_lifterlms_migration_menu() {
    add_management_page(
        'Migrate Courses to LifterLMS',
        'Migrate Courses',
        'manage_options',
        'lifterlms-course-migration',
        'lifterlms_migration_admin_page'
    );
}

// Trang admin + form
function lifterlms_migration_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    echo '<div class="wrap">';
    echo '<h1>Migrate Courses từ bảng cũ → LifterLMS</h1>';

    // Xử lý submit form
    if (isset($_POST['run_migration']) && wp_verify_nonce($_POST['migration_nonce'], 'run_lifterlms_migration')) {
        echo '<div class="updated"><p><strong>Đang chạy migration... Vui lòng chờ</strong></p></div>';
        echo '<pre style="background:#fff;padding:15px;border:1px solid #ccc;max-height:600px;overflow:auto;">';

        // Tăng limit để chạy lâu
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        migrate_courses_to_lifterlms(); // Hàm cũ của bạn giữ nguyên

        echo '</pre>';
        echo '<p><a href="' . admin_url('tools.php?page=lifterlms-course-migration') . '">← Quay lại</a></p>';
    } else {
        // Hiển thị form xác nhận
        ?>
        <div class="card" style="max-width:700px;">
            <h2>Xác nhận chạy Migration</h2>
            <p><strong>Cảnh báo:</strong> Quá trình này sẽ tạo các course mới trong LifterLMS từ bảng <code>courses</code> cũ.</p>
            <p>Chỉ chạy <strong>một lần duy nhất</strong>. Nếu chạy lại sẽ tạo bản sao!</p>

            <form method="post">
                <?php wp_nonce_field('run_lifterlms_migration', 'migration_nonce'); ?>
                <input type="hidden" name="run_migration" value="1">
                <p class="submit">
                    <input type="submit" class="button button-primary button-large" value="Bắt đầu Migration ngay" 
                           onclick="return confirm('Bạn có chắc chắn muốn chạy migration?\n\nHành động này không thể hoàn tác dễ dàng.');">
                </p>
            </form>
        </div>
        <?php
    }

    echo '</div>';
}
<?php
/**
 * LifterLMS Course Migration Script
 * Migrate courses from old database table to wp_posts
 * 
 * Usage: Run this in WordPress admin or via WP-CLI
 */

function migrate_courses_to_lifterlms() {
    global $wpdb;
    
        $allowed_ids = [
        163, 161, 50, 116, 149, 66, 130, 129, 160, 40,
        145, 143, 142, 134, 133, 118, 117, 132, 110, 141,
        89, 151, 162, 108, 90, 159, 82, 147, 144, 152,
        83, 79, 155, 148, 146, 153, 154, 102, 97, 150,
        76, 77, 84, 107, 114, 115, 78, 124, 101, 71, 156,157,158
    ];

        //north carolina course ids
        //$allowed_ids = [ 156,157,158]; 
        


        $placeholders = str_repeat('%d,', count($allowed_ids));     // tạo chuỗi %d,%d,%d,...
        $placeholders = rtrim($placeholders, ',');                 // bỏ dấu phẩy cuối

        $sql = "SELECT * FROM courses WHERE `CourseId` IN ($placeholders) ORDER BY FIELD(`CourseId`, " . implode(',', $allowed_ids) . ")";

        $old_courses = $wpdb->get_results( 
            $wpdb->prepare( $sql, $allowed_ids ),   // truyền mảng $allowed_ids vào prepare
            ARRAY_A 
        );

    // Lấy tất cả courses từ bảng cũ
    //$old_courses = $wpdb->get_results("SELECT * FROM courses", ARRAY_A);
    
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
                'post_content' => $course['main_content'],
                'post_type'     => 'course',
                'post_status'   => $post_status,
                'post_date'     => $post_date,
                'post_date_gmt' => get_gmt_from_date($post_date),
                'post_author' =>1,
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
                    //xử lý riêng cho nccourse ( north carolina course)
                    if($cat_name=='ncourse')
                    {
                        update_post_meta($post_id,'_north_carolina_course',true);
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
            if (!empty($course['AuthorId']) || !empty($course['AndAuthorName']) || !empty($course['WithAuthorName'])) {
                $author_ids = process_course_instructors(
                    $course['AuthorId'],
                    $course['AndAuthorName'] ?? '',
                    $course['WithAuthorName'] ?? ''
                );
                if (!empty($author_ids)) {
                    update_post_meta($post_id, '_llms_instructors', $author_ids);
                }
            }

            //xử lý label highlight
             if (!empty($course['Highlight'])) {
                update_post_meta($post_id, '_status_update_label', $course['Highlight']);
            }

            //xử lý alternate copy right
            if (!empty($course['AlternateCopyright'])) {
                update_post_meta($post_id, '_alternate_copyright', $course['alternate_copyright']);
            }

            //xử lý course plan
            create_course_access_plan( $post_id, $course['CoursePrice'], $course['CourseName'] );
            
            $content_instance=create_content_course($post_id);
            import_create_single_choice_question( $content_instance['quiz_id'], $course['CourseId'] );
            
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
 * @param string|int $author_id AuthorId từ bảng cũ
 * @param string $and_author_name Tên từ cột AndAuthorName
 * @param string $with_author_name Tên từ cột WithAuthorName
 * @return array Mảng serialized format LifterLMS
 */
function process_course_instructors($author_id, $and_author_name = '', $with_author_name = '') {
    global $wpdb;
    
    $instructors = array();
    $index = 0;
    
    // 1. Xử lý AuthorId (logic cũ)
    $author_ids = is_array($author_id) ? $author_id : explode(',', $author_id);
    
    foreach ($author_ids as $old_author_id) {
        $old_author_id = trim($old_author_id);
        
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
            $index++;
        }
    }
    
    // 2. Xử lý AndAuthorName
    if (!empty($and_author_name)) {
        $clean_name = extract_author_name($and_author_name);
        $user_id = find_user_by_display_name($clean_name);
        
        if ($user_id) {
            $instructors[$index] = array(
                'label'      => 'Author',
                'visibility' => 'visible',
                'id'         => intval($user_id)
            );
            $index++;
        }
    }
    
    // 3. Xử lý WithAuthorName
    if (!empty($with_author_name)) {
        $clean_name = extract_author_name($with_author_name);
        $user_id = find_user_by_display_name($clean_name);
        
        if ($user_id) {
            $instructors[$index] = array(
                'label'      => 'Author',
                'visibility' => 'visible',
                'id'         => intval($user_id)
            );
            $index++;
        }
    }
    
    return !empty($instructors) ? $instructors : array();
}

/**
 * Lấy tên tác giả sạch (bỏ học vị)
 * 
 * @param string $full_name Tên đầy đủ có học vị, ví dụ: "Stephanie Knatz Peck, Ph.D."
 * @return string Tên sạch: "Stephanie Knatz Peck"
 */
function extract_author_name($full_name) {
    // Loại bỏ khoảng trắng thừa
    $full_name = trim($full_name);
    
    // Tách theo dấu phẩy và lấy phần đầu tiên
    $parts = explode(',', $full_name);
    $clean_name = trim($parts[0]);
    
    return $clean_name;
}

/**
 * Tìm user theo display name
 * 
 * @param string $display_name Tên hiển thị cần tìm
 * @return int|false User ID hoặc false nếu không tìm thấy
 */
function find_user_by_display_name($display_name) {
    global $wpdb;
    
    $user_id = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->users} 
        WHERE display_name = %s 
        LIMIT 1",
        $display_name
    ));
    
    return $user_id ? intval($user_id) : false;
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

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('migrate-lifterlms-courses', function() {
        migrate_courses_to_lifterlms();
    });
} 


//handing access plan & price
function create_course_access_plan( $course_id, $price, $title ) {
    
    // Kiểm tra course tồn tại
    if ( ! get_post( $course_id ) || get_post_type( $course_id ) !== 'course' ) {
        return false;
    }
    
    // Chuẩn bị dữ liệu cho access plan với TẤT CẢ parameters cần thiết
    $access_plan_data = array(
        // REQUIRED
        'product_id'           => absint( $course_id ),
        
        // Basic Info
        'title'                => sanitize_text_field( 'One Time' ),
        'content'              => '', // Plan description (optional)
        
        // Pricing - CHÚ Ý: price phải > 0 nếu is_free = 'no'
        'is_free'              => 'no',
        'price'                => floatval( $price ),
        
        // Payment Schedule
        'frequency'            => 0, // 0 = one-time payment
        'period'               => 'year', // year|month|week|day
        'length'               => 0, // 0 = không giới hạn (for recurring)
        
        // Access Settings
        'access_expiration'    => 'lifetime', // lifetime|limited-period|limited-date
        'access_length'        => 1, // Chỉ dùng khi access_expiration = 'limited-period'
        'access_period'        => 'year', // year|month|week|day
        
        // Availability
        'availability'         => 'open', // open|members
        'availability_restrictions' => array(), // Array of membership IDs
        
        // Display
        'visibility'           => 'visible', // visible|hidden|featured
        'enroll_text'          => 'Take the course',
        
        // Trial (chỉ cho recurring plans)
        'trial_offer'          => 'no', // yes|no
        'trial_price'          => 0,
        'trial_length'         => 1,
        'trial_period'         => 'year',
        
        // Sale
        'on_sale'              => 'no', // yes|no
        'sale_price'           => 0,
        'sale_start'           => '', // MM/DD/YYYY
        'sale_end'             => '',
        
        // Redirect Settings
        'checkout_redirect_type'   => 'self', // self|page|url
        'checkout_redirect_page'   => '', // WP Page ID (nếu type = 'page')
        'checkout_redirect_url'    => '', // URL (nếu type = 'url')
        'checkout_redirect_forced' => 'no', // yes|no
        
        // Menu order
        'menu_order'           => 1,
    );
    
    // Tạo access plan
    $access_plan_id = llms_insert_access_plan( $access_plan_data );
    
    // Kiểm tra có lỗi không
    if ( is_wp_error( $access_plan_id ) ) {
        error_log( 'Lỗi tạo access plan: ' . $access_plan_id->get_error_message() );
        return false;
    }  
  
    //ép tác giả để fix bug
    wp_update_post(array('ID'=>$access_plan_id,'post_author'=>1));
  

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

//handling section, lesson, quiz, questions
function create_content_course( $course_id, $section_title = 'Section mới', $lesson_title = 'Bài học miễn phí', $quiz_title = '' ) {

    // Kiểm tra course tồn tại
    $course = llms_get_post( $course_id );
    if ( ! $course || 'course' !== $course->get( 'type' ) ) {
        return new WP_Error( 'invalid_course', 'Course ID không hợp lệ hoặc không không tồn tại.' );
    }

    // 1. TẠO SECTION
  //  $sections   = $course->get_sections( 'ids' );
    $next_order =1;

    $section_title=$course->get('title').' main section';
    $lesson_title=$course->get('title').' main lesson';

    $section_data = array(
        'post_title'  => wp_strip_all_tags( $section_title ),
        'post_author' =>1,
        'post_type'   => 'section',
        'post_status' => 'publish',
      //  'menu_order'  => $next_order,
        'meta_input'  => array(
            '_llms_parent_course' => $course_id,
            '_llms_order'         => $next_order,
        ),
    );

    //$section_id = LLMS_Post_Handler::create( $section_data );
    $section_id=wp_insert_post($section_data);
    if ( is_wp_error( $section_id ) ) {
        return $section_id;
    }

    // 2. TẠO LESSON
    $lesson_data = array(
        'post_title'  => wp_strip_all_tags( $lesson_title ),
        'post_author' =>1,
        'post_type'   => 'lesson',
        'post_status' => 'publish',
       // 'menu_order'  => 1,
        'meta_input'  => array(
            '_llms_parent_course'    => $course_id,
            '_llms_parent_section'   => $section_id,
            '_llms_order'            => 1,
            '_llms_free_lesson'      => 'yes',
            '_llms_quiz_enabled'     => 'yes',
            '_llms_has_prerequisite' => 'no',
        ),
    );

    //$lesson_id = LLMS_Post_Handler::create( $lesson_data );
    $lesson_id = wp_insert_post($lesson_data);
    if ( is_wp_error( $lesson_id ) ) {
        wp_delete_post( $section_id, true );
        return $lesson_id;
    }

    // 3. TẠO QUIZ TỰ ĐỘNG CHO LESSON VỪA TẠO
    if ( empty( $quiz_title ) ) {
        $quiz_title = 'Quiz - ' . $lesson_title;
    }

    $quiz_data = array(
        'post_title'  => wp_strip_all_tags( $quiz_title ),
        'post_type'   => 'llms_quiz',
        'post_author' =>1,
        'post_status' => 'publish',
        //'menu_order'  => 0,
        'meta_input'  => array(
            '_llms_lesson_id'       => $lesson_id,           // Liên kết với lesson
            '_llms_passing_percent' => 75,                   // Điểm đỗ cố định 75%
            '_llms_time_limit'      => 'no',                    // Không giới hạn thời gian (mặc định tốt)
            '_llms_show_correct_answer'    => 'no',                // Hiển thị đáp án đúng/sai
            '_llms_random_questions'  => 'no',                 // Không xáo trộn đáp án
            '_llms_limit_attempts'  => 'no',                 // Cho làm lại không giới hạn
        ),
    );

   // $quiz_id = LLMS_Post_Handler::create( $quiz_data );
    $quiz_id = wp_insert_post( $quiz_data );
    if ( is_wp_error( $quiz_id ) ) {
        // Nếu tạo quiz lỗi thì vẫn giữ lại lesson/section (vì vẫn dùng được)
        // Nhưng trả về lỗi để bạn biết
        return new WP_Error( 'quiz_create_failed', 'Tạo Quiz thất bại: ' . $quiz_id->get_error_message(), array(
            'section_id' => $section_id,
            'lesson_id'  => $lesson_id,
            'quiz_id'    => false,
        ) );
    }

    //update lesson to link quiz
    update_post_meta( $lesson_id, '_llms_quiz', $quiz_id );

    // Thành công hoàn toàn
    return array(
        'section_id' => $section_id,
        'lesson_id'  => $lesson_id,
        'quiz_id'    => $quiz_id,
    );
}



/* claude */
function llms_create_multiple_choice_question( $quiz_id, $question_text, $choices = array(), $args = array() ) {
    
    // Kiểm tra quiz có tồn tại không
    $quiz = llms_get_post( $quiz_id );
    if ( ! $quiz || 'llms_quiz' !== $quiz->get( 'type' ) ) {
        return new WP_Error( 'invalid_quiz', 'Quiz ID không hợp lệ' );
    }
    
    // Merge với các tham số mặc định
    $defaults = array(
        'title'              => $question_text,
        'question_type'      => 'choice', // choice = multiple choice
        'points'             => 1,
        'multi_choices'      => 'no', // 'no' = single choice, 'yes' = multiple choices
        'description_enabled' => 'no',
        'clarifications_enabled' => 'no',
        'video_enabled'      => 'no',
        'parent_id'          => $quiz_id,
    );
    
    $question_data = wp_parse_args( $args, $defaults );
    
    // Tạo question mới
    $question = new LLMS_Question( 'new', $question_data );
    
    if ( ! $question->get( 'id' ) ) {
        return new WP_Error( 'question_create_failed', 'Không thể tạo câu hỏi' );
    }
    
    // Thêm các lựa chọn (choices)
    if ( ! empty( $choices ) && is_array( $choices ) ) {
        foreach ( $choices as $choice ) {
            $choice_data = array(
                'choice'  => isset( $choice['text'] ) ? $choice['text'] : $choice,
                'correct' => isset( $choice['correct'] ) ? $choice['correct'] : false,
                'marker'  => isset( $choice['marker'] ) ? $choice['marker'] : '',
            );
            
            $choice_id = $question->create_choice( $choice_data );
            
            if ( ! $choice_id ) {
                error_log( 'Không thể tạo choice cho question ID: ' . $question->get( 'id' ) );
            }
        }
    }
    
    // Thêm question vào quiz
    //$quiz->add_question( $question->get( 'id' ) );
    update_post_meta($choice_id,'_llms_parent_id',$quiz_id);
    
    return $question->get( 'id' );
}
/* end claude */

function example_create_single_choice_question($quiz_id) {
    //$quiz_id = 123; // Thay bằng quiz ID của bạn
    
    $question_text = 'WordPress được viết bằng ngôn ngữ lập trình nào?';
    
    $choices = array(
        array(
            'text'    => 'Python',
            'correct' => false,
        ),
        array(
            'text'    => 'PHP',
            'correct' => true, // Đáp án đúng
        ),
        array(
            'text'    => 'Java',
            'correct' => false,
        ),
        array(
            'text'    => 'Ruby',
            'correct' => false,
        ),
    );
    
    $args = array(
        'points' => 1, // Số điểm cho câu hỏi này
        'multi_choices' => 'no', // Single choice
    );
    
    $question_id = llms_create_multiple_choice_question( $quiz_id, $question_text, $choices, $args );
    
    if ( is_wp_error( $question_id ) ) {
        echo 'Lỗi: ' . $question_id->get_error_message();
    } else {
        echo 'Đã tạo câu hỏi thành công! Question ID: ' . $question_id;
    }
}


function import_create_single_choice_question($quiz_id, $course_id) {
    global $wpdb;
    
    // Lấy tất cả câu hỏi từ bảng testquestions theo CourseId
    $table_name = 'testquestions';
    $questions = $wpdb->get_results( 
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE CourseId = %d ORDER BY QuestionNumber ASC",
            $course_id
        ),
        ARRAY_A
    );
    
    if (empty($questions)) {
        echo 'Không tìm thấy câu hỏi nào cho Course ID: ' . $course_id;
        return;
    }
    
    $created_count = 0;
    $error_count = 0;

    // Mảng markers cho A, B, C, D, E...
    $markers = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
    
    foreach ($questions as $question_data) {
        // Lấy text của câu hỏi
        $question_text = $question_data['Question'];
        
        // Parse các choices (ngăn cách bởi dấu |)
        $choices_array = explode('|', $question_data['Choices']);
        
        // Lấy đáp án đúng (Answer là số thứ tự, bắt đầu từ 1)
        $correct_answer_index = intval($question_data['Answer']) - 1;
        
        // Tạo mảng choices theo format của LifterLMS
        $choices = array();
        foreach ($choices_array as $index => $choice_text) {
            $choices[] = array(
                'text'    => trim($choice_text),
                'correct' => ($index === $correct_answer_index), // So sánh index
                'marker'  => isset($markers[$index]) ? $markers[$index] : '', // Gán marker A, B, C...
            );
        }
        
        $args = array(
            'points' => 1,
            'multi_choices' => 'no',
        );
        
        // Tạo câu hỏi
        $question_id = llms_create_multiple_choice_question($quiz_id, $question_text, $choices, $args);
        
        if (is_wp_error($question_id)) {
            echo 'Lỗi tạo câu hỏi ID ' . $question_data['TestQuestionId'] . ': ' . $question_id->get_error_message() . '<br>';
            $error_count++;
        } else {
            // Lưu QuestionNumber và Help vào post meta
            update_post_meta($question_id, '_question_number', $question_data['QuestionNumber']);
            update_post_meta($question_id, '_question_help', $question_data['Help']);
            
            // Có thể lưu thêm TestQuestionId gốc để tham chiếu
            update_post_meta($question_id, '_original_test_question_id', $question_data['TestQuestionId']);
            
            $created_count++;
        }
    }
    
    echo "Hoàn thành migration! Đã tạo {$created_count} câu hỏi, {$error_count} lỗi.";
}

// Cách sử dụng:
// example_create_single_choice_question($quiz_id, $course_id);


//migrate by url


add_action('admin_init', 'handle_single_course_migration_url');

function handle_single_course_migration_url() {
    // Kiểm tra có param migrate_single_course không
    if (!isset($_GET['migrate_single_course']) || $_GET['migrate_single_course'] != 1) {
        return;
    }
    
    // Kiểm tra quyền admin
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Kiểm tra có old_courseid không
    if (!isset($_GET['old_courseid']) || empty($_GET['old_courseid'])) {
        wp_die('Thiếu tham số old_courseid. Ví dụ: ?migrate_single_course=1&old_courseid=157');
    }
    
    $old_course_id = intval($_GET['old_courseid']);
    
    // Tăng timeout và memory
    set_time_limit(0);
    ini_set('memory_limit', '512M');
    
    echo '<h1>Migrate Single Course: ID = ' . $old_course_id . '</h1>';
    echo '<pre>';
    
    // Gọi hàm migrate cho 1 course duy nhất
    migrate_single_course_by_id($old_course_id);
    
    echo '</pre>';
    echo '<p><a href="' . admin_url() . '">← Quay về Dashboard</a></p>';
    
    exit; // Dừng execution
}

/**
 * Migrate 1 course duy nhất theo CourseId
 * Copy logic từ migrate_courses_to_lifterlms() nhưng chỉ xử lý 1 ID
 */
function migrate_single_course_by_id($course_id) {
    global $wpdb;
    
    // Lấy course từ bảng cũ
    $course = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM courses WHERE CourseId = %d", $course_id),
        ARRAY_A
    );
    
    if (!$course) {
        echo "❌ Không tìm thấy course với ID: {$course_id}\n";
        return false;
    }
    
    try {
        // Copy nguyên logic từ foreach loop trong migrate_courses_to_lifterlms()
        $post_status = (isset($course['real_status']) && strtolower($course['real_status']) === 'publish') 
            ? 'publish' 
            : 'pending';
        
        $post_date = !empty($course['FirstPublished']) 
            ? date('Y-m-d H:i:s', strtotime($course['FirstPublished'])) 
            : current_time('mysql');
        
        $post_data = array(                
            'post_title'    => $course['CourseName'],
            'post_content'  => $course['main_content'],
            'post_type'     => 'course',
            'post_status'   => $post_status,
            'post_date'     => $post_date,
            'post_date_gmt' => get_gmt_from_date($post_date),
            'post_author'   => 1,
        );
        
        $post_id = wp_insert_post($post_data, true);
        
        if (is_wp_error($post_id)) {
            throw new Exception($post_id->get_error_message());
        }
        
        // Lưu post meta
        update_post_meta($post_id, 'course_stable_id', $course['CourseId']);
        update_post_meta($post_id, '_llms_ce_hours', $course['CreditHours']);
        
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
            $categories = array_map('trim', explode(',', $course['category']));
            $category_ids = array();
            
            foreach ($categories as $cat_name) {
                if (empty($cat_name)) continue;
                
                $category_term = get_term_by('name', $cat_name, 'course_cat');
                if ($category_term) {
                    $category_ids[] = $category_term->term_id;
                }
                
                if ($cat_name == 'ncourse') {
                    update_post_meta($post_id, '_north_carolina_course', true);
                }
            }
            
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
        if (!empty($course['AuthorId']) || !empty($course['AndAuthorName']) || !empty($course['WithAuthorName'])) {
            $author_ids = process_course_instructors(
                $course['AuthorId'],
                $course['AndAuthorName'] ?? '',
                $course['WithAuthorName'] ?? ''
            );
            if (!empty($author_ids)) {
                update_post_meta($post_id, '_llms_instructors', $author_ids);
            }
        }
        
        // Xử lý label highlight
        if (!empty($course['Highlight'])) {
            update_post_meta($post_id, '_status_update_label', $course['Highlight']);
        }
        
        // Xử lý alternate copyright
        if (!empty($course['AlternateCopyright'])) {
            update_post_meta($post_id, '_alternate_copyright', $course['alternate_copyright']);
        }
        
        // Xử lý course plan
        create_course_access_plan($post_id, $course['CoursePrice'], $course['CourseName']);
        
        $content_instance = create_content_course($post_id);
        import_create_single_choice_question($content_instance['quiz_id'], $course['CourseId']);
        
        echo "✅ Migrate thành công: {$course['CourseName']} (WordPress Post ID: {$post_id})\n";
        echo "📝 Course ID cũ: {$course['CourseId']}\n";
        echo "🔗 Xem course: " . get_permalink($post_id) . "\n";
        
        return $post_id;
        
    } catch (Exception $e) {
        echo "❌ Lỗi khi migrate course {$course['CourseId']}: {$e->getMessage()}\n";
        return false;
    }
}
<?php
// Generate unique 6-digit completion code
function generate_unique_completion_code() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    $max_attempts = 100;
    
    for ($i = 0; $i < $max_attempts; $i++) {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE completion_code = %s",
            $code
        ));
        
        if (!$exists) {
            return $code;
        }
    }
    
    return false;
}

// AJAX handler to grade quiz 
function grade_quiz_submission() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quiz_submit_nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        return;
    }
    
    // Validate course_id
    if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
        wp_send_json_error(array('message' => 'Course ID is required'));
        return;
    }
    
    // Validate answers
    if (!isset($_POST['answers']) || !is_array($_POST['answers'])) {
        wp_send_json_error(array('message' => 'Invalid answers format'));
        return;
    }
    
    $course_id = intval($_POST['course_id']);
    $submitted_answers = $_POST['answers'];
    
    // Get correct answers from database
    $course_data_manager = CourseLessonData::get_instance();
    $question_list = $course_data_manager->get_course_structured_data($course_id);
    
    if (!$question_list) {
        wp_send_json_error(array('message' => 'Course questions not found'));
        return;
    }
    
    // Grade answers
    $results = array();
    $correct_count = 0;
    $total_questions = count($question_list);
    
    foreach ($question_list as $question) {
        $question_id = $question['question_id'];
        $submitted_choice_id = isset($submitted_answers[$question_id]) ? $submitted_answers[$question_id] : null;
        
        if ($submitted_choice_id === null || $submitted_choice_id==='') {
            $results[$question_id] = array(
                'is_correct' => false,
                'submitted' => false
            );
            continue;
        }
        
        // Find correct answer
        $is_correct = false;
        foreach ($question['choices'] as $choice) {
            // So sánh không phân biệt kiểu dữ liệu, đảm bảo cả hai được convert sang cùng kiểu

            // Kiểm tra cả id và correct flag

            $choice_id = isset($choice['id']) ? $choice['id'] : '';

            $is_choice_correct = isset($choice['correct']) ? $choice['correct'] : false;
        

            // Convert correct flag sang boolean nếu là string

            if (is_string($is_choice_correct)) {

                $is_choice_correct = ($is_choice_correct === 'yes' || $is_choice_correct === '1' || $is_choice_correct === 'true');

            }
        
            // So sánh choice_id với submitted_choice_id (không phân biệt kiểu)

            if ($choice_id == $submitted_choice_id && $is_choice_correct) {

                $is_correct = true;

                $correct_count++;

                break;

            }
        }
        
        $results[$question_id] = array(
            'is_correct' => $is_correct,
            'submitted' => true
        );
    }
    
    // Calculate score
    $score_percentage = $total_questions > 0 ? round(($correct_count / $total_questions) * 100) : 0;
    $is_passed = $score_percentage >= 75;
    
    // If passed, generate completion code
    $completion_code = null;
    $print_certificate_url = null;
    
    if ($is_passed) {
        $completion_code = generate_unique_completion_code();
        
        if ($completion_code) {
            // Insert completion record
            global $wpdb;
            $table_name = $wpdb->prefix . 'course_completion_code';
            
            $wpdb->insert(
                $table_name,
                array(
                    'completion_code' => $completion_code,
                    'completed_date' => current_time('mysql'),
                    'course_id' => $course_id,
                    'is_convert' => 0,
                    'score_test'=> (int)$score_percentage,
                ),
                array('%s', '%s', '%d', '%d','%d')
            );
            
            // Set cookie for 1 day
            $cookie_name = 'completion_code_ck_' . $completion_code;
            $cookie_value = 'true';
            $cookie_expire = time() + (1 * 24 * 60 * 60);
            
            setcookie(
                $cookie_name,
                $cookie_value,
                $cookie_expire,
                '/',
                '',
                is_ssl(),
                true
            );
            
            $print_certificate_url = get_purchase_certificate_page_url() . '?completion_code=' . $completion_code;
        }
    }
    
    wp_send_json_success(array(
        'results' => $results,
        'correct_count' => $correct_count,
        'incorrect_count' => $total_questions - $correct_count,
        'total_questions' => $total_questions,
        'score_percentage' => $score_percentage,
        'is_passed' => $is_passed,
        'completion_code' => $completion_code,
        'print_certificate_url' => $print_certificate_url
    ));
}
add_action('wp_ajax_grade_quiz_submission', 'grade_quiz_submission');
add_action('wp_ajax_nopriv_grade_quiz_submission', 'grade_quiz_submission');

//save user completion code.
function save_user_course_completion_meta( $user_id, $course_ids ) {
  
    if ( ! is_array( $course_ids ) || empty( $course_ids ) || ! is_numeric( $user_id ) ) {
        return; 
    }

    $meta_key = 'course_complete_not_paid';
      
    update_user_meta( $user_id, $meta_key, $course_ids );
    
}

//get completion code list 
function get_user_completed_courses( $user_id ) {
    $meta_key = 'course_complete_not_paid';    
 
    $course_list = get_user_meta( $user_id, $meta_key, true );    
    return is_array( $course_list ) ? $course_list : array();
}

function save_completion_codes_on_login( ) {
    if(is_user_logged_in())
    {
        $current_user=wp_get_current_user();
     $user_id = $current_user->ID;
    $meta_key = 'course_complete_not_paid';
    $cookie_prefix = 'completion_code_ck_';
    
    // 1. Lấy mảng các khóa học hiện có trong user meta
    // (Sử dụng hàm get_user_completed_courses nếu bạn đã định nghĩa nó ở trên,
    // nếu không, ta sử dụng hàm gốc của WP)
    $existing_courses = get_user_meta( $user_id, $meta_key, true );
    if ( ! is_array( $existing_courses ) ) {
        $existing_courses = array();
    }

    $updated_courses = $existing_courses;
    $cookies_to_delete = array();

    // 2. Duyệt qua tất cả các cookie để tìm completion code
    foreach ( $_COOKIE as $name => $value ) {
        if ( strpos( $name, $cookie_prefix ) === 0 ) {
            
            // Tên cookie có dạng: completion_code_ck_123456
            // Lấy ID khóa học (ví dụ: '123456')
            $course_id = str_replace( $cookie_prefix, '', $name );

            // Đảm bảo ID khóa học là một giá trị hợp lệ (chỉ để đề phòng)
            if ( ! empty( $course_id ) && is_numeric( $course_id ) ) {
                
                // 3. Thêm ID khóa học vào mảng nếu nó chưa tồn tại
                if ( ! in_array( $course_id, $updated_courses ) ) {
                    $updated_courses[] = $course_id;
                }
                
                // Đánh dấu cookie này cần được xóa
                $cookies_to_delete[] = $name;
            }
        }
    }

    // 4. Lưu mảng mới vào user meta
    if ( count( $updated_courses ) > count( $existing_courses ) ) {
        // Chỉ update nếu có thêm khóa học mới để giảm thiểu thao tác database
        update_user_meta( $user_id, $meta_key, array_unique( $updated_courses ) );
    }

    // 5. Hủy (xóa) các cookie đã tìm thấy
    if ( ! empty( $cookies_to_delete ) ) {
        // setcookie() phải được gọi trước khi bất kỳ output nào được gửi đi.
        // Đây là lý do action hook 'wp_login' hoạt động tốt.
        $past_time = time() - 3600; // Đặt thời gian hết hạn trong quá khứ
        $cookie_path = COOKIEPATH ? COOKIEPATH : '/';
        $cookie_domain = COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
        $is_secure = is_ssl();
        
        foreach ( $cookies_to_delete as $cookie_name ) {
            setcookie( $cookie_name, '', $past_time, $cookie_path, $cookie_domain, $is_secure, true );
        }
    }


    }
   
}

add_action( 'init', 'save_completion_codes_on_login', 999, 2 );

//function to get completion code
function get_completion_code($completion_code)
{
    global $wpdb;
        $table_name = $wpdb->prefix . 'course_completion_code';
        $completion_record = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE completion_code = %s",
        $completion_code
    ));

    return $completion_record;
    //false or $completion_code object ->id, is_convert,..
}

//remove unpaid course from user meta
function remove_course_from_complete_not_paid($user_id, $completion_code) {
    // Lấy dữ liệu hiện tại từ user meta
    $courses = get_user_meta($user_id, 'course_complete_not_paid', true);
    
    // Kiểm tra nếu không có dữ liệu
    if (empty($courses) || !is_array($courses)) {
        return false;
    }
    
    // Tìm và xóa course_id khỏi mảng
    $key = array_search($completion_code, $courses);
    
    if ($key !== false) {
        unset($courses[$key]);
        
        // Đánh lại index của mảng (0, 1, 2,...)
        $courses = array_values($courses);
        
        // Cập nhật lại user meta
        update_user_meta($user_id, 'course_complete_not_paid', $courses);
        
        return true;
    }
    
    return false;
}
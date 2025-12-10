<?php
/**
 * Course Evaluation Functions
 * Add this code to your theme's functions.php or create a custom plugin
 */

// Enqueue scripts
add_action('wp_enqueue_scripts', 'enqueue_course_evaluation_scripts');
function enqueue_course_evaluation_scripts() {
    // Only load on course evaluation page
    if (is_page_template('template-pages/page-course-evaluation.php')) {
        
        // Enqueue custom script
        wp_enqueue_script(
            'course-evaluation-ajax',
            get_stylesheet_directory_uri() . '/assets/js/course-evaluation.js',
            array('jquery'),
            '1.0.1',
            true
        );
        
        // Localize script with AJAX URL and nonce
        wp_localize_script('course-evaluation-ajax', 'ceAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('course_evaluation_nonce')
        ));
    }
}

// Handle AJAX submission
add_action('wp_ajax_submit_course_evaluation', 'handle_course_evaluation_submission');
add_action('wp_ajax_nopriv_submit_course_evaluation', 'handle_course_evaluation_submission');

function handle_course_evaluation_submission() {
    global $wpdb;
    
    // Start session if needed
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'course_evaluation_nonce')) {
        wp_send_json_error(array('message' => 'Invalid security token'));
        return;
    }

    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'You must be logged in to submit evaluation'));
        return;
    }

    // Validate course ID
    $course_id = isset($_POST['course_id']) ? absint($_POST['course_id']) : 0;
    if (!$course_id) {
        wp_send_json_error(array('message' => 'Invalid course ID'));
        return;
    }

    // Validate required fields
    $validation_result = validate_evaluation_data($_POST);
    if (!$validation_result['valid']) {
        wp_send_json_error(array('message' => $validation_result['message']));
        return;
    }

    // Get current user info
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $user_name = $user->display_name ?: '';
    $user_email = $user->user_email ?: '';
    $user_phone='';
    if($user_id)
    {
        $get_phone=get_user_meta($user_id,'llms_phone',true); 
        if($get_phone)
        {
            $user_phone=$get_phone;
        }
    }
    // Sanitize and collect survey data
    $survey_data = sanitize_course_evaluation_data($_POST);

    // Prepare data for database insert
    $table_name = $wpdb->prefix . 'survey_responses';
    
    $insert_data = array(
        'survey_type' => 'course_feedback',
        'survey_date' => current_time('mysql'),
        'user_id' => $user_id,
        'user_name' => sanitize_text_field($user_name),
        'user_email' => sanitize_email($user_email),
        'user_phone' => $user_phone,
        'course_id' => $course_id,
        'survey_data' => wp_json_encode($survey_data, JSON_UNESCAPED_UNICODE),
        'ip_address' => get_client_ip(),
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field($_SERVER['HTTP_USER_AGENT']), 0, 255) : '',
        'referrer' => isset($_SERVER['HTTP_REFERER']) ? substr(esc_url_raw($_SERVER['HTTP_REFERER']), 0, 255) : '',
        'status' => 'submitted',
        'notify_new_courses' => 0,
        'created_at' => current_time('mysql'),
        'updated_at' => current_time('mysql')
    );

    // Format for wpdb insert
    $format = array(
        '%s', // survey_type
        '%s', // survey_date
        '%d', // user_id
        '%s', // user_name
        '%s', // user_email
        '%s', // user_phone
        '%d', // course_id
        '%s', // survey_data
        '%s', // ip_address
        '%s', // user_agent
        '%s', // referrer
        '%s', // status
        '%d', // notify_new_courses
        '%s', // created_at
        '%s'  // updated_at
    );

    // Insert into database
    $result = $wpdb->insert($table_name, $insert_data, $format);

    if ($result) {
        $response_id = $wpdb->insert_id;

        // Log submission
        log_evaluation_submission($response_id, $survey_data, $course_id, $user_id);

        // Send notification email to admin
        send_evaluation_notification_email($course_id, $response_id, $survey_data, $user_name);

        // Optional: Send thank you email to user
        // send_thank_you_email($user_email, $user_name, $course_id);

        wp_send_json_success(array(
            'message' => 'Thank you for your feedback! Your evaluation has been submitted successfully.',
            'response_id' => $response_id,
            'redirect_url' => site_url('customer-account'),
        ));
    } else {
        // Log error for debugging
        error_log('Course Evaluation DB Error: ' . $wpdb->last_error);
        error_log('Course Evaluation Data: ' . print_r($insert_data, true));
        
        wp_send_json_error(array('message' => 'Failed to save evaluation. Please try again or contact support.'));
    }
}

// Validate evaluation data
function validate_evaluation_data($post_data) {
    $errors = array();

    // Check professional status
    $professional_status_fields = array('r10', 'r11', 'r12', 'r14', 'r13');
    $has_professional_status = false;
    foreach ($professional_status_fields as $field) {
        if (!empty($post_data[$field])) {
            $has_professional_status = true;
            break;
        }
    }
    if (!$has_professional_status) {
        $errors[] = 'Please select your professional status';
    }

    // Check reasons for taking course
    $reason_fields = array('r20', 'r21', 'r22', 'r23', 'r24');
    $has_reason = false;
    foreach ($reason_fields as $field) {
        if (!empty($post_data[$field])) {
            $has_reason = true;
            break;
        }
    }
    if (!$has_reason) {
        $errors[] = 'Please select at least one reason for taking this course';
    }

    // Check overall quality
    if (empty($post_data['r30'])) {
        $errors[] = 'Please rate the overall quality of the course';
    }

    // Check learning objectives
    $learning_objectives = array('r12753', 'r12752', 'r12751', 'r12750', 'r12748', 'r12749');
    foreach ($learning_objectives as $objective) {
        if (empty($post_data[$objective])) {
            $errors[] = 'Please rate all learning objectives';
            break;
        }
    }

    // Check learning experience questions
    $experience_questions = array('r80', 'r200', 'r210', 'r240', 'r100', 'r360', 'r370', 'r380');
    foreach ($experience_questions as $question) {
        if (empty($post_data[$question])) {
            $errors[] = 'Please answer all learning experience questions';
            break;
        }
    }

    // Check service questions
    $service_questions = array('r230', 'r110');
    foreach ($service_questions as $question) {
        if (empty($post_data[$question])) {
            $errors[] = 'Please answer all service questions';
            break;
        }
    }

    // Check decision factors
    $decision_factors = array('r300', 'r310', 'r320', 'r330', 'r340');
    foreach ($decision_factors as $factor) {
        if (empty($post_data[$factor])) {
            $errors[] = 'Please rate all decision factors';
            break;
        }
    }

    if (!empty($errors)) {
        return array(
            'valid' => false,
            'message' => implode(', ', $errors)
        );
    }

    return array('valid' => true);
}

// Sanitize survey data
function sanitize_course_evaluation_data($post_data) {
    $survey_data = array();
    
    // Professional Status
    $survey_data['professional_status'] = array(
        'psychologist' => !empty($post_data['r10']) ? 1 : 0,
        'social_worker' => !empty($post_data['r11']) ? 1 : 0,
        'mft' => !empty($post_data['r12']) ? 1 : 0,
        'counselor' => !empty($post_data['r14']) ? 1 : 0,
        'other' => !empty($post_data['r13']) ? 1 : 0,
        'other_text' => !empty($post_data['r13text']) ? sanitize_text_field($post_data['r13text']) : ''
    );
    
    // Reasons for Taking Course
    $survey_data['reasons'] = array(
        'interest' => !empty($post_data['r20']) ? 1 : 0,
        'author_reputation' => !empty($post_data['r21']) ? 1 : 0,
        'job_activities' => !empty($post_data['r22']) ? 1 : 0,
        'ce_hours' => !empty($post_data['r23']) ? 1 : 0,
        'other' => !empty($post_data['r24']) ? 1 : 0,
        'other_text' => !empty($post_data['r24text']) ? sanitize_text_field($post_data['r24text']) : ''
    );
    
    // Overall Quality
    $survey_data['overall_quality'] = !empty($post_data['r30']) ? intval($post_data['r30']) : 0;
    
    // Learning Objectives
    $survey_data['learning_objectives'] = array(
        'objective_1' => !empty($post_data['r12753']) ? intval($post_data['r12753']) : 0,
        'objective_2' => !empty($post_data['r12752']) ? intval($post_data['r12752']) : 0,
        'objective_3' => !empty($post_data['r12751']) ? intval($post_data['r12751']) : 0,
        'objective_4' => !empty($post_data['r12750']) ? intval($post_data['r12750']) : 0,
        'objective_5' => !empty($post_data['r12748']) ? intval($post_data['r12748']) : 0,
        'objective_6' => !empty($post_data['r12749']) ? intval($post_data['r12749']) : 0
    );
    
    // Learning Experience
    $survey_data['learning_experience'] = array(
        'taught_at_level' => !empty($post_data['r80']) ? intval($post_data['r80']) : 0,
        'clear_organized' => !empty($post_data['r200']) ? intval($post_data['r200']) : 0,
        'current_developments' => !empty($post_data['r210']) ? intval($post_data['r210']) : 0,
        'how_much_learned' => !empty($post_data['r240']) ? intval($post_data['r240']) : 0,
        'take_another_course' => !empty($post_data['r100']) ? intval($post_data['r100']) : 0,
        'disability_accommodations' => !empty($post_data['r360']) ? intval($post_data['r360']) : 0,
        'time_match_hours' => !empty($post_data['r370']) ? intval($post_data['r370']) : 0,
        'content_useful' => !empty($post_data['r380']) ? intval($post_data['r380']) : 0
    );
    
    // Service
    $survey_data['service'] = array(
        'user_friendly' => !empty($post_data['r230']) ? intval($post_data['r230']) : 0,
        'take_another' => !empty($post_data['r110']) ? intval($post_data['r110']) : 0
    );
    
    // Decision Factors
    $survey_data['decision_factors'] = array(
        'author_reputation' => !empty($post_data['r300']) ? intval($post_data['r300']) : 0,
        'course_quality' => !empty($post_data['r310']) ? intval($post_data['r310']) : 0,
        'ease_of_use' => !empty($post_data['r320']) ? intval($post_data['r320']) : 0,
        'reminders' => !empty($post_data['r330']) ? intval($post_data['r330']) : 0,
        'rewards_program' => !empty($post_data['r340']) ? intval($post_data['r340']) : 0,
        'other' => !empty($post_data['r350']) ? intval($post_data['r350']) : 0,
        'other_text' => !empty($post_data['r350text']) ? sanitize_text_field($post_data['r350text']) : ''
    );
    
    // Comments
    $survey_data['comments'] = array(
        'rewards_feedback' => !empty($post_data['r340text']) ? sanitize_textarea_field($post_data['r340text']) : '',
        'additional_comments' => !empty($post_data['r120text']) ? sanitize_textarea_field($post_data['r120text']) : '',
        'course_suggestions' => !empty($post_data['r130text']) ? sanitize_textarea_field($post_data['r130text']) : ''
    );
    
    // Add metadata
    $survey_data['metadata'] = array(
        'survey_type' => 'course_evaluation',
        'course_id' => !empty($post_data['course_id']) ? intval($post_data['course_id']) : 0,
        'submitted_date' => current_time('mysql'),
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field($_SERVER['HTTP_USER_AGENT']), 0, 255) : '',
        'version' => '1.0'
    );
    
    return $survey_data;
}

// Get client IP address
function get_client_ip() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Get the first IP in the list
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ip_list[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Validate IP address
    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    
    return $ip ? sanitize_text_field($ip) : '';
}

// Log submission to file
function log_evaluation_submission($response_id, $survey_data, $course_id, $user_id) {
    $log_dir = WP_CONTENT_DIR . '/uploads/evaluations/';
    
    // Create directory if it doesn't exist
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
        
        // Protect directory with .htaccess
        $htaccess = $log_dir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "deny from all\n");
        }
    }
    
    $log_file = $log_dir . 'course_' . $course_id . '_' . date('Y-m') . '.log';
    
    $log_entry = sprintf(
        "[%s] Response ID: %d | User ID: %d | Course ID: %d | Overall Quality: %d | Learning Avg: %.2f\n",
        current_time('mysql'),
        $response_id,
        $user_id,
        $course_id,
        $survey_data['overall_quality'],
        calculate_average_rating($survey_data['learning_objectives'])
    );
    
    error_log($log_entry, 3, $log_file);
}

// Calculate average rating
function calculate_average_rating($ratings) {
    if (empty($ratings)) {
        return 0;
    }
    
    $sum = 0;
    $count = 0;
    
    foreach ($ratings as $rating) {
        if ($rating > 0) {
            $sum += $rating;
            $count++;
        }
    }
    
    return $count > 0 ? ($sum / $count) : 0;
}

// Send email notification to admin
function send_evaluation_notification_email($course_id, $response_id, $survey_data, $user_name) {
    $admin_email = get_option('admin_email');
    
    // Get course info
    $course = get_post($course_id);
    $course_title = $course ? $course->post_title : 'Unknown Course';
    
    $quality_labels = array(1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Excellent');
    $quality = isset($quality_labels[$survey_data['overall_quality']]) ? $quality_labels[$survey_data['overall_quality']] : 'N/A';
    
    $subject = sprintf('[Course Evaluation] %s - %s Rating', $course_title, $quality);
    
    $learning_avg = calculate_average_rating($survey_data['learning_objectives']);
    $experience_avg = calculate_average_rating($survey_data['learning_experience']);
    
    $message = sprintf(
        "New course evaluation received:\n\n" .
        "Course: %s (ID: %d)\n" .
        "Student: %s\n" .
        "Response ID: %d\n" .
        "Submitted: %s\n\n" .
        "=== RATINGS ===\n" .
        "Overall Quality: %s (%d/4)\n" .
        "Learning Objectives Avg: %.2f/5\n" .
        "Learning Experience Avg: %.2f/5\n\n" .
        "=== COMMENTS ===\n" .
        "Additional Comments:\n%s\n\n" .
        "Course Suggestions:\n%s\n\n" .
        "CERewards Feedback:\n%s\n\n" .
        "---\n" .
        "View full details in admin panel: %s",
        $course_title,
        $course_id,
        $user_name,
        $response_id,
        current_time('mysql'),
        $quality,
        $survey_data['overall_quality'],
        $learning_avg,
        $experience_avg,
        $survey_data['comments']['additional_comments'] ?: 'None',
        $survey_data['comments']['course_suggestions'] ?: 'None',
        $survey_data['comments']['rewards_feedback'] ?: 'None',
        admin_url('admin.php?page=survey-responses&response_id=' . $response_id)
    );
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($admin_email, $subject, $message, $headers);
}

// Optional: Send thank you email to user
function send_thank_you_email($user_email, $user_name, $course_id) {
    $course = get_post($course_id);
    $course_title = $course ? $course->post_title : 'the course';
    
    $subject = 'Thank You for Your Course Evaluation';
    
    $message = sprintf(
        "Dear %s,\n\n" .
        "Thank you for taking the time to complete the course evaluation for \"%s\".\n\n" .
        "Your feedback is invaluable and helps us improve our courses and services. " .
        "We have shared your feedback with the course author.\n\n" .
        "You can now access your certificate from your account dashboard.\n\n" .
        "Best regards,\n" .
        "The ContinuingEdCourses.Net Team",
        $user_name,
        $course_title
    );
    
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    wp_mail($user_email, $subject, $message, $headers);
}

/**
 * Kiểm tra xem một phản hồi khảo sát có tồn tại cho người dùng và khóa học cụ thể hay không.
 *
 * @param int $user_id ID của người dùng.
 * @param int $course_id ID của khóa học.
 * @return bool Trả về TRUE nếu bản ghi tồn tại, FALSE nếu không.
 */
function check_survey_response_exists( $user_id, $course_id ) {
    // Luôn đảm bảo bạn có quyền truy cập vào biến toàn cục $wpdb
    global $wpdb;

    // Định nghĩa tên bảng
    $table_name = $wpdb->prefix . 'survey_responses';

    // Chuẩn bị và thực thi truy vấn: Chỉ SELECT COUNT(*) để kiểm tra sự tồn tại
    // Sử dụng prepare() để bảo mật (ngăn chặn SQL Injection)
    $query = $wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND course_id = %d",
        $user_id,
        $course_id
    );

    // Sử dụng get_var() để lấy giá trị đơn (số lượng hàng) từ truy vấn
    $count = $wpdb->get_var( $query );

    // Nếu $count > 0, nghĩa là bản ghi tồn tại
    return ( $count > 0 );
}

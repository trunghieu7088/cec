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

// AJAX handler to create completion record
function create_course_completion_record() {
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
    
    $course_id = intval($_POST['course_id']);
    
    // Validate score
    if (!isset($_POST['score']) || $_POST['score'] < 75) {
        wp_send_json_error(array('message' => 'Score must be at least 75% to generate completion code'));
        return;
    }
    
    // Generate unique completion code
    $completion_code = generate_unique_completion_code();
    
    if (!$completion_code) {
        wp_send_json_error(array('message' => 'Failed to generate unique completion code'));
        return;
    }
    
    // Insert record
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_completion_code';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'completion_code' => $completion_code,
            'completed_date' => current_time('mysql'),
            'course_id' => $course_id,
            'is_convert' => 0
        ),
        array('%s', '%s', '%d', '%d')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Failed to create completion record'));
        return;
    }

    // Set cookie for 1 day
        $cookie_name = 'completion_code_ck_' . $completion_code;
        $cookie_value = 'true';
        $cookie_expire = time() + (1 * 24 * 60 * 60); // 1 day from now

        setcookie(
            $cookie_name,
            $cookie_value,
            $cookie_expire,
            '/',
            '',
            is_ssl(),
            true // HttpOnly flag for security
        );

    
    wp_send_json_success(array(
        'completion_code' => $completion_code,
        'message' => 'Completion record created successfully',
        'print_certificate_url'=>get_custom_page_url_by_template('page-purchase-certificate.php').'?completion_code='.$completion_code, //cần fix chỗ này
    ));
}
add_action('wp_ajax_create_completion_record', 'create_course_completion_record');
add_action('wp_ajax_nopriv_create_completion_record', 'create_course_completion_record');
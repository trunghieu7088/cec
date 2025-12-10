<?php
/**
 * Add this code to your theme's functions.php
 */

// Enqueue scripts and styles
function enqueue_author_proposal_scripts() {
    if (is_page_template('template-pages/page-proposal-author.php')) {
        // jQuery Validation
//        wp_enqueue_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js', array('jquery'), '1.19.5', true);
        
        wp_enqueue_script( 'proposal-author-jquery-validation', get_stylesheet_directory_uri() . '/assets/js/jquery-validation.js', array( 'jquery' ), '1.0.0', true );  

        // Custom script
        wp_enqueue_script('author-proposal-script', get_stylesheet_directory_uri() . '/assets/js/author-proposal.js', array('jquery', 'proposal-author-jquery-validation'), '1.0.0', true);
        
        // Localize script for AJAX
        wp_localize_script('author-proposal-script', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('author_proposal_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_author_proposal_scripts');

// Handle AJAX form submission
add_action('wp_ajax_submit_author_proposal', 'handle_author_proposal_submission');
add_action('wp_ajax_nopriv_submit_author_proposal', 'handle_author_proposal_submission');

function handle_author_proposal_submission() {
    global $wpdb;
    
    // Verify nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'author_proposal_nonce')) {
        wp_send_json_error(array('message' => 'Security verification failed.'));
    }
    
    // Validate required fields
    $required_fields = array(
        'fullName', 'email', 'phone', 'courseName', 'courseLevel', 
        'creditHours', 'courseDescription', 'learningObjectives', 
        'courseOutline', 'culturalDiversity', 'references', 'apaStatement'
    );
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            wp_send_json_error(array('message' => "Field '{$field}' is required."));
        }
    }
    
    // Validate email
    if (!is_email($_POST['email'])) {
        wp_send_json_error(array('message' => 'Invalid email address.'));
    }
    
    // Handle CV file upload
    $cv_file_path = null;
    if (isset($_FILES['cvFile']) && $_FILES['cvFile']['error'] === UPLOAD_ERR_OK) {
        $cv_upload = handle_file_upload($_FILES['cvFile'], 'cv');
        if (is_wp_error($cv_upload)) {
            wp_send_json_error(array('message' => $cv_upload->get_error_message()));
        }
        $cv_file_path = $cv_upload;
    } else {
        wp_send_json_error(array('message' => 'CV file is required.'));
    }
    
    // Prepare data for database
    $table_name = $wpdb->prefix . 'author_proposals';
    
    $data = array(
        'submitted_date' => current_time('mysql'),
        'name' => sanitize_text_field($_POST['fullName']),
        'email' => sanitize_email($_POST['email']),
        'phone' => sanitize_text_field($_POST['phone']),
        'address' => isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : null,
        'course_name' => sanitize_text_field($_POST['courseName']),
        'course_level' => sanitize_text_field($_POST['courseLevel']),
        'hours' => intval($_POST['creditHours']),
        'description' => sanitize_textarea_field($_POST['courseDescription']),
        'objectives' => sanitize_textarea_field($_POST['learningObjectives']),
        'outline' => sanitize_textarea_field($_POST['courseOutline']),
        'diversity' => sanitize_textarea_field($_POST['culturalDiversity']),
        'references_text' => sanitize_textarea_field($_POST['references']), // JSON string
        'is_first_time' => 1, // Always 1 as per requirement
        'cv_file' => $cv_file_path,
        'has_conflict' => isset($_POST['hasConflict']) ? intval($_POST['hasConflict']) : 0,
        'conflict_explanation' => isset($_POST['conflictDetails']) ? sanitize_textarea_field($_POST['conflictDetails']) : null,
        'apa_statement' => sanitize_textarea_field($_POST['apaStatement']),
        'status' => 'pending'
    );
    
    $format = array(
        '%s', // submitted_date
        '%s', // name
        '%s', // email
        '%s', // phone
        '%s', // address
        '%s', // course_name
        '%s', // course_level
        '%d', // hours
        '%s', // description
        '%s', // objectives
        '%s', // outline
        '%s', // diversity
        '%s', // references_text
        '%d', // is_first_time
        '%s', // cv_file
        '%d', // has_conflict
        '%s', // conflict_explanation
        '%s', // apa_statement
        '%s'  // status
    );
    
    // Insert into database
    $result = $wpdb->insert($table_name, $data, $format);
    
    if ($result === false) {
        // Delete uploaded file if database insert fails
        if ($cv_file_path && file_exists($cv_file_path)) {
            unlink($cv_file_path);
        }
        
        wp_send_json_error(array(
            'message' => 'Failed to save proposal. Please try again.',
            'error' => $wpdb->last_error
        ));
    }
    
    // Send notification email to admin
    $admin_email = get_option('admin_email');
    $subject = 'New Course Proposal Submission - ' . sanitize_text_field($_POST['courseName']);
    $message = "A new course proposal has been submitted.\n\n";
    $message .= "Author: " . sanitize_text_field($_POST['fullName']) . "\n";
    $message .= "Email: " . sanitize_email($_POST['email']) . "\n";
    $message .= "Course: " . sanitize_text_field($_POST['courseName']) . "\n";
    $message .= "Credit Hours: " . intval($_POST['creditHours']) . "\n\n";
    $message .= "Please log in to the admin panel to review this proposal.\n";
    $message .= "Proposal ID: " . $wpdb->insert_id;
    
    wp_mail($admin_email, $subject, $message);
    
    // Send confirmation email to author
    $author_email = sanitize_email($_POST['email']);
    $author_subject = 'Course Proposal Received - ContinuingEdCourses.Net';
    $author_message = "Dear " . sanitize_text_field($_POST['fullName']) . ",\n\n";
    $author_message .= "Thank you for submitting your course proposal for \"" . sanitize_text_field($_POST['courseName']) . "\".\n\n";
    $author_message .= "We have received your submission and our advisory board will review it within 2-3 business days.\n\n";
    $author_message .= "You will receive feedback and next steps via email.\n\n";
    $author_message .= "Proposal Reference ID: " . $wpdb->insert_id . "\n\n";
    $author_message .= "Best regards,\n";
    $author_message .= "ContinuingEdCourses.Net Team";
    
    wp_mail($author_email, $author_subject, $author_message);
    
    wp_send_json_success(array(
        'message' => 'Your proposal has been submitted successfully! You will receive a confirmation email shortly.',
        'proposal_id' => $wpdb->insert_id
    ));
}

/**
 * Handle file upload
 */
function handle_file_upload($file, $type = 'cv') {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', 'File upload failed.');
    }
    
    // Validate file size (5MB max)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        return new WP_Error('file_too_large', 'File size must not exceed 5MB.');
    }
    
    // Validate file type
    $allowed_types = array('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return new WP_Error('invalid_file_type', 'Only PDF and Word documents are allowed.');
    }
    
    // Get file extension
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = array('pdf', 'doc', 'docx');
    
    if (!in_array($file_ext, $allowed_exts)) {
        return new WP_Error('invalid_extension', 'Invalid file extension.');
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = wp_upload_dir();
    $custom_dir = $upload_dir['basedir'] . '/author-proposals/' . date('Y/m');
    
    if (!file_exists($custom_dir)) {
        wp_mkdir_p($custom_dir);
    }
    
    // Generate unique filename
    $filename = $type . '_' . uniqid() . '_' . sanitize_file_name($file['name']);
    $filepath = $custom_dir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Return relative path from uploads directory
        $relative_path = str_replace($upload_dir['basedir'], '', $filepath);
        return $relative_path;
    }
    
    return new WP_Error('move_failed', 'Failed to save uploaded file.');
}

/**
 * Create custom database table on theme activation
 */
function create_author_proposals_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'author_proposals';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        submitted_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT DEFAULT NULL,
        course_name VARCHAR(255) NOT NULL,
        course_level ENUM('introductory', 'intermediate', 'advanced') NOT NULL,
        hours INT NOT NULL,
        description TEXT NOT NULL,
        objectives TEXT NOT NULL,
        outline TEXT NOT NULL,
        outline_file VARCHAR(255) DEFAULT NULL COMMENT 'Path to uploaded outline file',
        diversity TEXT NOT NULL,
        references_text TEXT NOT NULL COMMENT 'APA formatted references, separated by newlines or JSON array',
        is_first_time TINYINT(1) NOT NULL DEFAULT 1,
        cv_file VARCHAR(255) DEFAULT NULL COMMENT 'Path to uploaded CV file',
        has_conflict TINYINT(1) NOT NULL DEFAULT 0,
        conflict_explanation TEXT DEFAULT NULL,
        apa_statement TEXT NOT NULL,
        status ENUM('pending', 'reviewed', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
        PRIMARY KEY (id),
        KEY email (email)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Run table creation on theme activation
//add_action('after_switch_theme', 'create_author_proposals_table');

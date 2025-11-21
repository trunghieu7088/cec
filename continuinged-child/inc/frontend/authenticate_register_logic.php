<?php
/**
 * AJAX Handler for Login
 */
function purchase_login_handler() {
    check_ajax_referer('purchase_login_nonce', 'nonce');
    
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    
    $user = wp_authenticate($username, $password);
    
    if (is_wp_error($user)) {
        wp_send_json_error(array(
            'message' => 'Invalid username or password. Please try again.'
        ));
    }
    
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    
    wp_send_json_success(array(
        'message' => 'Login successful!',
        'user' => array(
            'id' => $user->ID,
            'name' => $user->display_name
        )
    ));
}
add_action('wp_ajax_nopriv_purchase_login', 'purchase_login_handler');
add_action('wp_ajax_purchase_login', 'purchase_login_handler');


/**
 * AJAX Handler for Signup
 */
function purchase_signup_handler() {
    check_ajax_referer('purchase_signup_nonce', 'nonce');
    
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $fullname = sanitize_text_field($_POST['fullname']);
    $address= sanitize_text_field($_POST['address']);
    $license=sanitize_text_field($_POST['license']);
    $license_state=$_POST['license_state'];
    $zip=$_POST['zip'];
    $city=$_POST['city'];
    $state=$_POST['state'];
    $phone=$_POST['phone'];
    $newsletter=$_POST['newsletter'];
    
    // Validate
    if (username_exists($username)) {
        wp_send_json_error(array(
            'message' => 'Username already exists. Please choose another one.'
        ));
    }
    
    if (email_exists($email)) {
        wp_send_json_error(array(
            'message' => 'Email already registered. Please use another email or login.'
        ));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Please enter a valid email address.'
        ));
    }
    
    if (strlen($password) < 6) {
        wp_send_json_error(array(
            'message' => 'Password must be at least 6 characters long.'
        ));
    }
    
    // Create user
  /*  $user_id = wp_create_user($username, $password, $email);
    
    if (is_wp_error($user_id)) {
        wp_send_json_error(array(
            'message' => 'Registration failed: ' . $user_id->get_error_message()
        ));
    } */
    
    //use the lifter lms hook instead of wp 
    $user_id = llms_register_user( 
        array(
            'email_address' => $email,
            'password'      => $password,
            'first_name'=>$fullname,
            'last_name'=>$fullname,
            'password_confirm'  => $password,
            'user_login' =>$username,
            'country'=>1,
            'llms_billing_country'=>'US',
            'llms_billing_city'=>$city,
            'llms_billing_address_1'=>$address,
            'llms_billing_state'=>$state,
            'llms_billing_zip'=>$zip,
            'llms_phone'=>$phone,
            'license'=>$license,
            'license_state'=>$license_state,
            'full_name'=>$fullname,
            'email_address_confirm'=>$email
            
            // other fields
        ),
        'registration',  // screen
        true             // auto signon
    );

    if (is_wp_error($user_id)) {
        wp_send_json_error(array(
            'message' => 'Registration failed: ' . $user_id->get_error_message()
        ));
    } 
    
    if($user_id)
    {
        //manually update user meta license number because lifterlms does not have this field
        update_user_meta($user_id,'license_number',$license);
        update_user_meta($user_id,'email_me',$newsletter);
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $fullname,            
        ));
    }
    // Log user in
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    wp_send_json_success(array(
        'message' => 'Account created successfully!',
        'user' => array(
            'id' => $user_id,
            'name' => $fullname
        )
    ));
}
add_action('wp_ajax_nopriv_purchase_signup', 'purchase_signup_handler');
add_action('wp_ajax_purchase_signup', 'purchase_signup_handler');


/**
 * AJAX Handler for Update Password
 */
function update_user_password_handler() {
    // Verify nonce for security
    check_ajax_referer('update_password_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => 'You must be logged in to change your password.'
        ));
    }
    
    $user_id = get_current_user_id();
    
    // Sanitize input
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if (empty($new_password) || empty($confirm_password)) {
        wp_send_json_error(array(
            'message' => 'Please fill in all password fields.'
        ));
    }
    
    if (strlen($new_password) < 6) {
        wp_send_json_error(array(
            'message' => 'Password must be at least 6 characters long.'
        ));
    }
    
    if (strlen($new_password) > 50) {
        wp_send_json_error(array(
            'message' => 'Password must not exceed 50 characters.'
        ));
    }
    
    if ($new_password !== $confirm_password) {
        wp_send_json_error(array(
            'message' => 'Passwords do not match. Please try again.'
        ));
    }
    
    // Check password strength (optional)
    $strength = 0;
    if (preg_match('/[a-z]/', $new_password) && preg_match('/[A-Z]/', $new_password)) {
        $strength++;
    }
    if (preg_match('/\d/', $new_password)) {
        $strength++;
    }
    if (preg_match('/[^a-zA-Z0-9]/', $new_password)) {
        $strength++;
    }
    
    // Optional: Enforce minimum strength
    // if ($strength < 2) {
    //     wp_send_json_error(array(
    //         'message' => 'Password is too weak. Please use a mix of uppercase, lowercase, numbers, and special characters.'
    //     ));
    // }
    
    // Update password
    wp_set_password($new_password, $user_id);
    
    // Log the user back in (since wp_set_password logs them out)
    $user = get_user_by('id', $user_id);
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    // Send success response
    wp_send_json_success(array(
        'message' => 'Password updated successfully! You have been automatically logged back in.',
        'user_id' => $user_id
    ));
}
add_action('wp_ajax_update_user_password', 'update_user_password_handler');

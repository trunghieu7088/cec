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

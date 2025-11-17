<?php
/**
 * AJAX Handler for Update User Profile
 *
 */

function update_user_profile_handler() {
    check_ajax_referer('update_user_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => 'You must be logged in to update your profile.'
        ));
    }
    
    $user_id = get_current_user_id();
    
    // Sanitize input data
    $fullname = sanitize_text_field($_POST['fullname']);
    $email = sanitize_email($_POST['email']);
    $license = sanitize_text_field($_POST['license']);
    $license_state = sanitize_text_field($_POST['license_state']);
    $phone = sanitize_text_field($_POST['phone']);
    $address = sanitize_text_field($_POST['address']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $zip = sanitize_text_field($_POST['zip']);
    
    // Validate email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Please enter a valid email address.'
        ));
    }
    
    // Check if email is already used by another user
    $email_exists = email_exists($email);
    if ($email_exists && $email_exists != $user_id) {
        wp_send_json_error(array(
            'message' => 'This email is already registered to another account.'
        ));
    }
    
    // Update user data
    $user_data = array(
        'ID' => $user_id,
        'display_name' => $fullname,
        'user_email' => $email
    );
    
    $updated = wp_update_user($user_data);
    
    if (is_wp_error($updated)) {
        wp_send_json_error(array(
            'message' => 'Profile update failed: ' . $updated->get_error_message()
        ));
    }
    
    // Update user meta (LifterLMS fields)
    update_user_meta($user_id, 'llms_billing_city', $city);
    update_user_meta($user_id, 'llms_billing_address_1', $address);
    update_user_meta($user_id, 'llms_billing_state', $state);
    update_user_meta($user_id, 'llms_billing_zip', $zip);
    update_user_meta($user_id, 'llms_phone', $phone);
    update_user_meta($user_id, 'signup_license', $license);
    update_user_meta($user_id, 'license_state', $license_state);
    
    // Send success response
    wp_send_json_success(array(
        'message' => 'Your profile has been updated successfully!',
        'user' => array(
            'id' => $user_id,
            'name' => $fullname,
            'email' => $email
        )
    ));
}

// Register AJAX actions
add_action('wp_ajax_update_user_profile', 'update_user_profile_handler');

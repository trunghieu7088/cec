<?php

function create_order_for_exist_user( $plan_id, $gateway_id = 'manual') {
    
    $custom_data=wp_get_current_user();    
    $data = array(
        'plan_id'         => $plan_id,
        'payment_gateway' => $gateway_id,
        'coupon_code'     => '',
        'agree_to_terms'  => 'yes',
        'customer'        => array(
            // KHÔNG có user_id => tự động TẠO USER MỚI
            //nếu tạo mới thì cần thêm các data khác như là email, confirm email , password, confirm password, user_login
            //đồng thời ko được thêm user_id nếu tạo mới --> quan trọng nhất vì hàm llms_setup_pending_order sẽ xài user_id để check
            /* sample code for creat new user 
            'email_address' => $email,
            'password'      => $password,         
            'password_confirm'  => $password,
            'user_login' =>$username,
            */
            
             'user_id' => get_current_user_id(),
             'first_name'=>$custom_data->first_name,
             'last_name' => $custom_data->last_name,
             'llms_billing_address_1'=>'address ne',
             'llms_billing_country'=>'US',
            'llms_billing_city'=>'saigon city',
            'llms_billing_state'=>'Arizona',
            'llms_billing_zip'=>'70000',
        )
    );
    
    // Setup pending order - Sẽ TỰ ĐỘNG TẠO USER
    $setup = llms_setup_pending_order($data);
    
    if (is_wp_error($setup)) {
        return $setup;
    }
    
    // Tạo order (luôn là 'new' vì user mới)
    $order = new LLMS_Order('new');
    
    if (!$order->get('id')) {
        return new WP_Error('order_creation_failed', 'Failed to create order');
    }
    
    // Init order
    $order->init(
        $setup['person'],   // User đã được tạo tự động!
        $setup['plan'],
        $setup['gateway'],
        $setup['coupon']
    );
    
    // Nếu là free enrollment hoặc manual payment
  /*  if ($gateway_id === 'manual' || $setup['plan']->is_free()) {
        
        // Record free transaction
        $order->record_transaction(array(
            'amount'         => 0,
            'status'         => 'llms-txn-succeeded',
            'payment_type'   => 'single',
            'transaction_id' => 'free-' . time(),
        ));
        
        // Start access
        $order->start_access();
        $order->set_status('active');
        
        // Add note
        $order->add_note('Free enrollment - order created programmatically with new user');
    } */
    
    return array(
        'order'   => $order,
        'user_id' => $setup['person']->get('id'), // ID của user vừa tạo
    );
}



/**
 * Process Certificate Purchase - Create Order and Award Certificate
 */
function ajax_process_certificate_purchase() {
    // Check nonce
    check_ajax_referer('process_payment_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(array(
            'message' => 'You must be logged in to purchase a certificate.'
        ));
    }
    
    $user_id = get_current_user_id();
    
    // Get completion code from request
    $completion_code = isset($_POST['completion_code']) ? sanitize_text_field($_POST['completion_code']) : '';
    
    if (empty($completion_code)) {
        wp_send_json_error(array(
            'message' => 'Invalid completion code.'
        ));
    }
    
    global $wpdb;
    
    // Get completion record
    $table_name = $wpdb->prefix . 'course_completion_code';
    $completion_record = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE completion_code = %s",
        $completion_code
    ));
    
    if (!$completion_record) {
        wp_send_json_error(array(
            'message' => 'Completion code not found.'
        ));
    }
    
    // Check if already converted
    if ($completion_record->is_convert == 1) {
        wp_send_json_error(array(
            'message' => 'This completion code has already been used.'
        ));
    }
    
    $course_id = intval($completion_record->course_id);
    
    // Get course access plan
    $course = llms_get_post($course_id);
    if (!$course) {
        wp_send_json_error(array(
            'message' => 'Course not found.'
        ));
    }
    
    // Get first access plan
    $product = new LLMS_Product( $course_id );
    $plans = $product->get_access_plans();
    if (empty($plans)) {
        wp_send_json_error(array(
            'message' => 'No access plan found for this course.'
        ));
    }
    
    $plan_id = $plans[0]->get('id'); // Get first plan ID
    
    // Create order using existing function
    $order_result = create_order_for_exist_user($plan_id, 'manual');
    
    if (is_wp_error($order_result)) {
        wp_send_json_error(array(
            'message' => 'Failed to create order: ' . $order_result->get_error_message()
        ));
    }
    
    $order = $order_result['order'];
    
    // Record manual payment transaction
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    
    $order->record_transaction(array(
        'amount'         => $total_amount,
        'status'         => 'llms-txn-succeeded',
        'payment_type'   => 'single',
        'transaction_id' => 'manual-' . $completion_code . '-' . time(),
        'gateway_source' => 'Manual Purchase Certificate',
    ));
    
    // Start access and set order status
    $order->start_access();
    $order->set_status('active');
    
    // Add order note
    $order->add_note(sprintf(
        'Certificate purchased via completion code: %s. Amount: $%s',
        $completion_code,
        number_format($total_amount, 2)
    ));
          
    // Award Certificate
    $certificate_id = 159; // Certificate template ID
    $certificate_awarded = false;
    $completion_code_instance=get_completion_code($completion_code);
    if (class_exists('LLMS_Engagement_Handler')) {
        try {
            $earned_certificate= LLMS_Engagement_Handler::handle_certificate(array(
                $user_id,
                $certificate_id,
                $course_id,
                null
            ));
            $certificate_awarded = true;        
            
            if ($earned_certificate) {
                update_post_meta($earned_certificate->get('id'), '_custom_completion_code', $completion_code);
                update_post_meta($earned_certificate->get('id'), 'score_test', $completion_code_instance->score_test);
             }
        } catch (Exception $e) {
            error_log('Certificate award error: ' . $e->getMessage());
        }
    }
    
    // Mark completion code as converted
    $wpdb->update(
        $table_name,
        array(
            'is_convert' => 1,           
        ),
        array('completion_code' => $completion_code),
        array('%d', '%d'),
        array('%s')
    );
    
    // Delete the completion cookie
    $cookie_name = 'completion_code_ck_' . $completion_code;
    setcookie($cookie_name, '', time() - 3600, '/');
    
    // Get certificate URL (if available)
    $certificate_url = '';
    if ($certificate_awarded) {
        // Try to get the awarded certificate
        $earned_certificates = llms_get_certificate(array(
            'user_id' => $user_id,
            'post_id' => $course_id,
        ));
        
        if (!empty($earned_certificates)) {
            $certificate_url = get_permalink($earned_certificates[0]->get('id'));
        }
    }
    
    // remove course from meta user for unpaid course list.
    remove_course_from_complete_not_paid($user_id, $completion_code);

    // Send success response
    wp_send_json_success(array(
        'message' => 'Certificate purchased successfully!',
        'order_id' => $order->get('id'),
        'certificate_awarded' => $certificate_awarded,
        'certificate_url' => $certificate_url,
       // 'redirect_url' => $certificate_url ? $certificate_url : get_permalink(get_option('lifterlms_myaccount_page_id'))
        'redirect_url' => site_url('customer-account'),
    ));
}
add_action('wp_ajax_process_certificate_payment', 'ajax_process_certificate_purchase');


add_action('llms_user_enrolled_in_course', function($user_id, $course_id) {
    global $wpdb;
    
    // 1. Insert completion status
    $wpdb->replace(
        $wpdb->prefix . 'lifterlms_user_postmeta',
        array(
            'user_id' => $user_id,
            'post_id' => $course_id,
            'meta_key' => '_is_complete',
            'meta_value' => 'yes',
            'updated_date' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s')
    );
    
    // 2. Insert completion date
    $wpdb->replace(
        $wpdb->prefix . 'lifterlms_user_postmeta',
        array(
            'user_id' => $user_id,
            'post_id' => $course_id,
            'meta_key' => '_completion_date',
            'meta_value' => current_time('mysql'),
            'updated_date' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s')
    );   

    //add CE hours for the users
    $ce_hours=get_post_meta($course_id,'_llms_ce_hours',true) ? get_post_meta($course_id,'_llms_ce_hours',true) : 0;
    
    $inserted_id = insert_ce_hours_record($user_id, $course_id,date('Y-m-d H:i:s'),$ce_hours);
    
 
    
}, 90, 2);


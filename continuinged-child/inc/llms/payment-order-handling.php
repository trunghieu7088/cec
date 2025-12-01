<?php

require_once 'authorize/vendor/autoload.php'; // Đảm bảo đã composer require authorizenet/authorizenet

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

function create_order_for_exist_user( $plan_id, $gateway_id = 'manual',$coupon_id=0 ) {
    
     //xử lý coupon nếu có
    $coupon_apply=false;
    if ($coupon_id > 0) {
        $coupon_info = get_post($coupon_id);
        if ($coupon_info && $coupon_info->post_type === 'llms_coupon') {
            $coupon_apply = $coupon_info->post_title;
        }
    }

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
             'llms_billing_address_1'=>get_user_meta($custom_data->ID,'llms_billing_address_1',true),
             'llms_billing_country'=>get_user_meta($custom_data->ID,'llms_billing_country',true),
            'llms_billing_city'=>get_user_meta($custom_data->ID,'llms_billing_city',true),
            'llms_billing_state'=>get_user_meta($custom_data->ID,'llms_billing_state',true),
            'llms_billing_zip'=>get_user_meta($custom_data->ID,'llms_billing_zip',true),
        ),        
    );

    // add coupon code if has
    if($coupon_apply)
    {
        $data['coupon_code']=$coupon_apply;
    }
    
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
 * Process payment with Authorize.Net Accept.js
 */
function process_authorizenet_payment($payment_nonce, $payment_value, $amount, $user_data) {
    
    // Get Authorize.Net credentials

    $credentials = authorizenet_get_credentials();
    $api_login_id = $credentials['api_login_id'];
    $transaction_key = $credentials['transaction_key'];
    $mode = $credentials['mode'] ? $credentials['mode'] : 'test'; // 'test' or 'live'
    
    // Set environment    
    if($mode==='test')
    {
        $environment = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
    }
    else
    {
        $environment = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
    }
     
      
    // Merchant Authentication
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName($api_login_id);
    $merchantAuthentication->setTransactionKey($transaction_key);
    
    // Create opaque data
    $opaqueData = new AnetAPI\OpaqueDataType();
    $opaqueData->setDataDescriptor($payment_nonce);
    $opaqueData->setDataValue($payment_value);
    
    $paymentOne = new AnetAPI\PaymentType();
    $paymentOne->setOpaqueData($opaqueData);
    
    // Billing information
    $billTo = new AnetAPI\CustomerAddressType();
    $billTo->setFirstName($user_data['first_name']);
    $billTo->setLastName($user_data['last_name']);
    $billTo->setAddress($user_data['address']);
    $billTo->setCity($user_data['city']);
    $billTo->setState($user_data['state']);
    $billTo->setZip($user_data['zip']);
    $billTo->setCountry('US');
    
    // Create transaction request
    $transactionRequestType = new AnetAPI\TransactionRequestType();
    $transactionRequestType->setTransactionType("authCaptureTransaction");
    $transactionRequestType->setAmount(number_format((float)$amount, 2, '.', ''));
    $transactionRequestType->setPayment($paymentOne);
    $transactionRequestType->setBillTo($billTo);
    
    // Create request
    $request = new AnetAPI\CreateTransactionRequest();
    $request->setMerchantAuthentication($merchantAuthentication);
    $request->setTransactionRequest($transactionRequestType);
    
    // Execute transaction
    $controller = new AnetController\CreateTransactionController($request);
    $response = $controller->executeWithApiResponse($environment);
    
    if ($response == null || $response->getMessages()->getResultCode() != "Ok") {
        $errorMessages = $response ? $response->getMessages()->getMessage() : [];
        $errorText = $errorMessages ? $errorMessages[0]->getText() : 'Unknown error';
        
        return array(
            'success' => false,
            'message' => 'Transaction failed: ' . $errorText,
            'code' => $response ? $response->getMessages()->getResultCode() : 'Error'
        );
    }
    
    $transactionResponse = $response->getTransactionResponse();
    
    if ($transactionResponse == null || $transactionResponse->getResponseCode() != "1") {
        $errors = $transactionResponse ? $transactionResponse->getErrors() : null;
        $errorText = $errors ? $errors[0]->getErrorText() : 'Transaction declined';
        
        return array(
            'success' => false,
            'message' => $errorText,
            'responseCode' => $transactionResponse ? $transactionResponse->getResponseCode() : 'Unknown'
        );
    }
    
    // Success
    return array(
        'success' => true,
        'transactionId' => $transactionResponse->getTransId(),
        'amount' => $amount,
        'authCode' => $transactionResponse->getAuthCode(),
        'message' => $transactionResponse->getMessages()[0]->getDescription(),
        'accountNumber' => $transactionResponse->getAccountNumber(),
        'accountType' => $transactionResponse->getAccountType()
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
    $current_user = wp_get_current_user();
    
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


       // Get payment data from Accept.js
    $payment_nonce = isset($_POST['payment_nonce']) ? sanitize_text_field($_POST['payment_nonce']) : '';
    $payment_value = isset($_POST['payment_value']) ? sanitize_text_field($_POST['payment_value']) : '';
    $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    $ce_discount_amount = isset($_POST['ce_discount_amount']) ? floatval($_POST['ce_discount_amount']) : 0;
    $coupon_id = isset($_POST['coupon_id']) ? intval($_POST['coupon_id']) : 0;
    
    if (empty($payment_nonce) || empty($payment_value)) {
        wp_send_json_error(array(
            'message' => 'Payment information is missing.'
        ));
    }
    
    // Prepare user data for payment
    $user_data = array(
        'first_name' => $current_user->first_name ?: $current_user->display_name,
        'last_name' => $current_user->last_name ?: '',
        'address' => get_user_meta($user_id, 'llms_billing_address_1', true) ?: 'N/A',
        'city' => get_user_meta($user_id, 'llms_billing_city', true) ?: 'N/A',
        'state' => get_user_meta($user_id, 'llms_billing_state', true) ?: 'NY',
        'zip' => get_user_meta($user_id, 'llms_billing_zip', true) ?: '10001'
    );

    $credentials = authorizenet_get_credentials();

     if (empty($credentials['api_login_id']) || 
        empty($credentials['transaction_key']) || 
        empty($credentials['client_key'])) {
        
        wp_send_json_error(array(
            'message' => 'Payment gateway not configured properly. Please check Authorize.net settings.'
        ));
    }
    
    // Process payment with Authorize.Net
    //$payment_result = process_authorizenet_payment($payment_nonce, $payment_value, $total_amount, $user_data);

     /*if (!$payment_result['success']) {
        wp_send_json_error(array(
            'message' => $payment_result['message']
        ));
    } */
    
    
    // Create order using existing function
    $order_result = create_order_for_exist_user($plan_id, 'manual', $coupon_id);
    
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
        'transaction_id' => 'manual-' . $completion_code . '-' . time(), //comment dòng này khi thực hiện transaction với authorize trên live.
        'gateway_source' => 'Manual Purchase Certificate', //comment dòng này khi thực hiện transaction với authorize trên live.
        
        // khi test thì trên site thì commment out cái này
        /* 'transaction_id' => $payment_result['transactionId'],  // ✅ TRANSACTION ID THẬT
        'gateway_source' => 'Authorize.Net',
        'gateway_source_description' => sprintf(
            'Auth: %s | Account: %s (%s)',
            $payment_result['authCode'],
            $payment_result['accountNumber'],
            $payment_result['accountType']
        ) */
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

    //update payment gateway manually
    update_post_meta($order->get('id'), '_llms_payment_gateway', 'Authorize.net');
    //update ce hours
    update_post_meta($order->get('id'), 'ce_discount_amount', $ce_discount_amount);

          
    // Award Certificate
    $certificate_id = cec_get_latest_certificate_id(); // Certificate template ID
    if(!$certificate_id) {
        wp_send_json_error(array(
            'message' => 'Certificate template not found.'
        ));
    }
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



// AJAX handler for updating price with discount code
add_action('wp_ajax_update_purchase_price', 'handle_update_purchase_price');
add_action('wp_ajax_nopriv_update_purchase_price', 'handle_update_purchase_price');

function handle_update_purchase_price() {
    // Verify nonce
    check_ajax_referer('update_price_nonce', 'nonce');
    
    // Get parameters
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $discount_code = isset($_POST['discount_code']) ? sanitize_text_field(trim($_POST['discount_code'])) : '';
    $mail_certificate = isset($_POST['mail_certificate']) ? intval($_POST['mail_certificate']) : 0;
    $user_id = get_current_user_id();
    
    // Validate course ID
    if (!$course_id) {
        wp_send_json_error(array(
            'message' => 'Invalid course ID.'
        ));
    }
    
    // Get course and price info
    $product = new LLMS_Product($course_id);
    $plans = $product->get_access_plans();
    
    if (empty($plans)) {
        wp_send_json_error(array(
            'message' => 'No access plan found for this course.'
        ));
    }
    
    $plan = $plans[0];
    $plan_id = $plan->get('id');
    $base_price = floatval($plan->get('price'));
    
    // Initialize calculation variables
    $ce_discount_percent = 0;
    $ce_discount_amount = 0;
    $coupon_discount_percent = 0;
    $coupon_discount_amount = 0;
    $coupon_id = 0;
    $coupon_info = null;
    
    // Calculate CE Rewards discount (if user is logged in)
    if ($user_id > 0) {
        $ce_info = calculate_ce_rewards_discount($user_id);
        $ce_discount_percent = $ce_info['discount'];
        $ce_discount_amount = ($base_price * $ce_discount_percent) / 100;
    }
    
    // Validate and calculate discount code
    $coupon_validation_message = '';
    if (!empty($discount_code)) {
        $validation_result = validate_and_calculate_coupon(
            $discount_code, 
            $course_id, 
            $plan_id, 
            $base_price
        );
        
        if ($validation_result['valid']) {
            $coupon_id = $validation_result['coupon_id'];
            $coupon_info = $validation_result['coupon_info'];
            $coupon_discount_percent = $validation_result['discount_percent'];
            $coupon_discount_amount = $validation_result['discount_amount'];
            $coupon_validation_message = 'Coupon applied successfully!';
        } else {
            $coupon_validation_message = $validation_result['message'];
        }
    }
    
    // Calculate mail fee
    $mail_fee = $mail_certificate ? 9.00 : 0.00;
    
    // Calculate final price
    $total_discount_amount = $ce_discount_amount + $coupon_discount_amount;
    $subtotal = $base_price - $total_discount_amount;
    $final_price = max(0, $subtotal + $mail_fee);
    
    // Get currency
    $currency = get_currency_of_llms();
    $currency_sign = html_entity_decode($currency['sign']); // ← Thêm decode

    
    // Prepare response
    wp_send_json_success(array(
        'base_price' => $base_price,
        'ce_discount_percent' => $ce_discount_percent,
        'ce_discount_amount' => $ce_discount_amount,
        'coupon_discount_percent' => $coupon_discount_percent,
        'coupon_discount_amount' => $coupon_discount_amount,
        'coupon_id' => $coupon_id,
        'coupon_code' => $discount_code,
        'coupon_valid' => !empty($coupon_id),
        'coupon_message' => $coupon_validation_message,
        'mail_fee' => $mail_fee,
        'total_discount_amount' => $total_discount_amount,
        'subtotal' => $subtotal,
        'final_price' => $final_price,
        'currency_sign' => $currency_sign,
        'currency_code' => $currency['code'],
        'formatted' => array(
            'base_price' => $currency_sign . number_format($base_price, 2),
            'ce_discount' => $currency_sign . number_format($ce_discount_amount, 2),
            'coupon_discount' => $currency_sign .'-'. number_format($coupon_discount_amount, 2),
            'mail_fee' => $currency_sign . number_format($mail_fee, 2),
            'final_price' => $currency_sign . number_format($final_price, 2)
        )
    ));
}

function validate_and_calculate_coupon($coupon_code, $course_id, $plan_id, $base_price) {
    // Get coupon by exact title
    $coupon_post = get_llms_coupon_by_title_exact($coupon_code);
    
    if (!$coupon_post) {
        return array(
            'valid' => false,
            'message' => 'Invalid coupon code.',
            'coupon_id' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0
        );
    }
    
    // Create LLMS_Coupon object
    $coupon = new LLMS_Coupon($coupon_post->ID);
    
    // Validate coupon for this plan
    $is_valid = $coupon->is_valid($plan_id);
    
    if ($is_valid !== true) {
        // Get error message from validation
        $error_message = 'This coupon cannot be used for this course.';
        
        if (is_string($is_valid)) {
            $error_message = $is_valid;
        } elseif (is_wp_error($is_valid)) {
            $error_message = $is_valid->get_error_message();
        }
        
        return array(
            'valid' => false,
            'message' => $error_message,
            'coupon_id' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0
        );
    }
    
    // Calculate discount
    $discount_type = $coupon->get('discount_type'); // 'percent' or 'dollar'
    $coupon_amount = floatval($coupon->get('coupon_amount'));
    
    $discount_amount = 0;
    $discount_percent = 0;
    
    if ($discount_type === 'percent') {
        $discount_percent = $coupon_amount;
        $discount_amount = ($base_price * $coupon_amount) / 100;
    } else {
        // Dollar amount
        $discount_amount = min($coupon_amount, $base_price);
        $discount_percent = ($discount_amount / $base_price) * 100;
    }
    
    return array(
        'valid' => true,
        'message' => 'Coupon applied successfully!',
        'coupon_id' => $coupon_post->ID,
        'coupon_info' => $coupon,
        'discount_type' => $discount_type,
        'discount_percent' => $discount_percent,
        'discount_amount' => $discount_amount,
        'coupon_amount' => $coupon_amount
    );
}


//display custom fields for admin panel

add_action('lifterlms_order_meta_box_after_payment_information', function($order) {
   
    $currency = get_currency_of_llms();
    $ce_hours_discount = get_post_meta($order->get('id'), 'ce_discount_amount', true);
    $final_total = floatval($order->get('total')) - floatval($ce_hours_discount);
    if ($ce_hours_discount) {
        echo '<div class="llms-order-meta-box-section">';     
        echo '<p><strong>CE Hours Discount:</strong> ' . '-'. $currency['sign'].$ce_hours_discount . '</p>';
        echo '<h4>Final Total: '. $currency['sign'].$final_total.'</h4>';
        echo '</div>';
    }
});
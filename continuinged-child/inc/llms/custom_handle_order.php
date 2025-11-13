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


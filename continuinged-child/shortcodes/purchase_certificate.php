<?php
/**
 * Purchase Certificate Page
 * Shortcode: [purchase_certificate]
 */

function purchase_certificate_shortcode($atts) {
    
     global $wpdb;
    
    // Get completion code from URL
    $completion_code = isset($_GET['completion_code']) ? sanitize_text_field($_GET['completion_code']) : '';
    
    // Validate completion code
    if (empty($completion_code)) {
        return '<div class="container mt-5">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Invalid or missing completion code. Please complete the course quiz first.
        </div>
         </div>';
    }
    
    // Get course_id from completion_code table
    $table_name = $wpdb->prefix . 'course_completion_code';
    $completion_record = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE completion_code = %s",
        $completion_code
    ));
    
    if (!$completion_record) {
        return '<div class="container mt-5">
            <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Completion code not found or has expired.
        </div>
         </div>';
    }
    
    // Check if already converted (purchased)
    if ($completion_record->is_convert == 1) {
        return '
        <div class="container mt-5">
        <div class="alert alert-warning">
            <i class="bi bi-info-circle-fill me-2"></i>
            This completion code has already been used to purchase a certificate.
        </div>
        </div>';
    }
    
    // Verify cookie exists
    $cookie_name = 'completion_code_ck_' . $completion_code;
    
    if (!isset($_COOKIE[$cookie_name])) {
            $is_logged_in = is_user_logged_in();
            $user_id = $is_logged_in ? get_current_user_id() : 0;
            
            if ( $is_logged_in && $user_id > 0 ) {
                
                // Người dùng đã đăng nhập, tiến hành kiểm tra User Meta
                $completed_courses = get_user_completed_courses( $user_id );
                
                // Kiểm tra xem $completion_code (ID khóa học) có trong mảng meta không
                if ( ! in_array( (string)$completion_code, $completed_courses ) ) {
                    // KHÔNG CÓ cookie VÀ KHÔNG CÓ trong meta
                    
                    return '<div class="container mt-5"><div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Session expired. Please retake the quiz to generate a new completion code.
                    </div></div>';
                }
                
                // Nếu $completion_code CÓ trong meta, thì:
                // Logic tiếp theo của bạn sẽ được thực thi (đi tiếp)
                // Ví dụ: echo '<p class="alert alert-success">Xác minh meta thành công, đi tiếp!</p>';
                
            } else {
                
                // KHÔNG có cookie VÀ KHÔNG đăng nhập
                
                return '<div class="container mt-5"><div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Session expired. Please retake the quiz to generate a new completion code.
                </div></div>';
            }        
    }
 
    
    $course_id = intval($completion_record->course_id);
    
    // Get course data using core-features.php function
    $course_manager = my_lifterlms_courses();
    $course_data = $course_manager->get_single_course_data($course_id);
    
    if (!$course_data) {
        return '<div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Course not found. Please contact support.
        </div>';
    }
    
    // Extract course information
    $course_name = $course_data['post_title'];
    $hours = !empty($course_data['llmscehours']) ? floatval($course_data['llmscehours']) : 0;
    
    // Get price from access plans
    $price = 0.00; // Default price    
    $final_price=0.00;
    if (!empty($course_data['access_plans'])) {
       $price=(float)$course_data['access_plans']->price;
       $final_price=$price;
    }
    
    $current_user = wp_get_current_user();
    $is_logged_in = is_user_logged_in();
    $state_list=get_llms_states_by_country();
    $discount_ce=0;
    $currency=get_currency_of_llms();    
    
     // If logged in, calculate CERewards discount
    if($is_logged_in){        
        $current_reward_info = calculate_ce_rewards_discount($current_user->ID);         
        $discount_info=apply_ce_rewards_discount($price, $current_user->ID,0);        
        //apply to discount and calculate final price
        if($discount_info )
        {            
            $discount_ce_amount=$discount_info['discount_amount'];            
            $final_price=$discount_info['final_price'];       
        }
    }

    //test code coupon
    $discount_code_single_id=11234;
    $check_plan_id=13584;
    $single_coupon = new LLMS_Coupon( $discount_code_single_id );
    $validation_coupon = $single_coupon->is_valid( $discount_code_single_id );
    if ( $validation_coupon === true ) {
        echo 'coupon is valid';
    }
    else
    {
        echo 'ko xai dc giam gia';
    }
    //end test
    ob_start();
    ?>
    
    <section class="purchase-certificate-section">
        <div class="container">
            <h2 class="page-title"><i class="bi bi-award-fill me-2"></i>Purchase Your Certificate</h2>
            
            <div class="purchase-info-box">
                <ul>
                    <li><i class="bi bi-check-circle-fill me-2"></i>After purchasing the course, you can print your Certificate of Completion immediately, or you can print your certificate at any time in the future by simply signing in to your account from any computer. For an additional fee we will print your certificate and mail it to you.</li>
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>If you do not complete your payment now, the results of this test will be lost and you will have to take the test again.</li>
                </ul>
                <p class="contact-info">
                    Please call us at <strong>858-484-4304</strong> or email us at 
                    <a href="mailto:contact@ContinuingEdCourses.Net">contact@ContinuingEdCourses.Net</a> 
                    if you have any questions about paying for your certificate.
                </p>
            </div>

            <!-- Course Details Card -->
            <div class="purchase-card">
                <div class="card-header">
                    <h3><i class="bi bi-book-fill me-2"></i>Course Details</h3>
                </div>
                <div class="card-body">
                    <div class="course-info">
                        <div class="course-title">
                            <?php echo esc_html($course_name); ?>
                            - Completion Code <span class="completion-code"><?php echo esc_html($completion_code); ?></span>                      
                        </div>
                        <div class="course-date">
                              Date: <?php echo date('m/d/Y', strtotime($completion_record->completed_date)); ?>
                        </div>
                    </div>

                    <div class="price-breakdown">
                        <table class="table">
                            <tbody>
                                <tr>
                                     <td>Course Fee (<?php echo esc_html($hours); ?> Hours)</td>
                                    <td class="text-end"><strong>$<?php echo number_format($price, 2); ?></strong></td>
                                </tr>
                                <?php if ($is_logged_in): ?>
                                <tr class="discount-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>Your <strong>CERewards™</strong> discount</span>
                                            <span class="badge bg-success ms-2">Member</span>                                        
                                        </div>
                                                                                     
                                            <?php if(isset($current_reward_info)): ?>    
                                                 <div class="d-flex" style="color:#000000;">                                                                                       
                                                <span>  You have completed <strong><?php echo number_format($current_reward_info['total_hours'], 0); ?> hours</strong> of courses.                                
                                                You are receiving an automatic <strong><?php echo $current_reward_info['discount']; ?>%</strong> 
                                                on your courses.</span>
                                               </div>   
                                            <?php endif; ?>
                                        
                                    </td>
                                    <td class="text-end" style="color:#000000;"><?php echo $currency['sign']; echo ($discount_ce_amount > 0) ? '-' : ''; echo number_format($discount_ce_amount,2); ?></td>
                                </tr>
                                <?php else: ?>
                                <tr class="discount-row">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span>Please sign in to see your <strong>CERewards™</strong> discount</span>
                                        </div>
                                    </td>
                                    <td class="text-end">$0.00</td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <label for="discount-code" class="mb-0 me-2">Discount code:</label>
                                            <input type="text" id="discount-code" name="discount_code" placeholder="Enter code" class="form-control form-control-sm" style="max-width: 200px;">
                                        </div>
                                    </td>
                                    <td class="text-end" id="discount-code-amount">$0.00</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="mail-certificate" name="mail_certificate" value="1">
                                            <label class="form-check-label" for="mail-certificate">
                                                I can't ever print the certificate, mail it to me instead ($9)
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end" id="mail-fee">$0.00</td>
                                </tr>
                                <tr class="total-row">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong id="final-total-amount" class="total-amount"><?php echo $currency['sign'].number_format($final_price, 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="update-price-btn">
                                <i class="bi bi-arrow-clockwise me-1"></i>Update Price
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Details Card -->
            <?php if(!is_user_logged_in()): ?>
            <div class="purchase-card">
                <div class="card-header">
                    <h3><i class="bi bi-person-fill me-2"></i>Customer Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Login Section -->
                        <div class="col-lg-6 border-end-lg">
                            <h4 class="section-title">I have an account:</h4>
                            
                            <?php if ($is_logged_in): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    You are logged in as <strong><?php echo esc_html($current_user->display_name); ?></strong>
                                    <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="btn btn-sm btn-outline-secondary ms-2">Logout</a>
                                </div>
                            <?php else: ?>
                                <form id="purchaseLoginForm" class="customer-form" data-nonce="<?php echo wp_create_nonce('purchase_login_nonce'); ?>">
                                    <div class="mb-3">
                                        <label for="login_username" class="form-label">Username <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="login_username" name="username" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="login_password" class="form-label">Password <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="login_password" name="password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                        </button>
                                    </div>
                                    
                                    <div class="text-center">
                                        <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-decoration-none">
                                            <i class="bi bi-key-fill me-1"></i>Retrieve Username/Password
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Sign Up Section -->
                        <div class="col-lg-6">
                            <h4 class="section-title">I am a new customer:</h4>
                            
                            <?php if ($is_logged_in): ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    You are already registered and logged in.
                                </div>
                            <?php else: ?>
                                <form id="purchaseSignupForm" class="customer-form" data-nonce="<?php echo wp_create_nonce('purchase_signup_nonce'); ?>">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_fullname" class="form-label">Full Name (to print on your certificate) <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="signup_fullname" name="fullname" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_license" class="form-label">License Number</label>
                                            <input type="text" class="form-control" id="signup_license" name="license">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_license_state" class="form-label">License State</label>
                                            <select class="form-select form-card-chooser" id="signup_license_state" name="license_state">
                                                <?php foreach($state_list as $state_item):?>
                                                    <option value="<?php echo $state_item; ?>"> 
                                                        <?php echo $state_item; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_email" class="form-label">Email (receipt is sent here) <span class="required">*</span></label>
                                            <input type="email" class="form-control" id="signup_email" name="email" required>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="signup_phone" name="phone">
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="signup_address" name="address">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="signup_city" name="city">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_state" class="form-label">State</label>
                                            <select class="form-select form-card-chooser" id="signup_state" name="state">
                                                <?php foreach($state_list as $state_item):?>
                                                    <option value="<?php echo $state_item; ?>"> 
                                                        <?php echo $state_item; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_zip" class="form-label">Zip</label>
                                            <input type="text" class="form-control" id="signup_zip" name="zip">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_username" class="form-label">New Username <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="signup_username" name="username" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_password" class="form-label">New Password <span class="required">*</span></label>
                                            <input type="password" class="form-control" id="signup_password" name="password" required>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="signup_newsletter" name="newsletter" checked>
                                                <label class="form-check-label" for="signup_newsletter">
                                                    Email me when new courses are available
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-person-plus-fill me-2"></i>Create Account
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(is_user_logged_in()): ?>
                <?php 
                $city=get_user_meta($current_user->ID,'llms_billing_city',true);
                $address=get_user_meta($current_user->ID,'llms_billing_address_1',true);
                $state=get_user_meta($current_user->ID,'llms_billing_state',true);
                $zip=get_user_meta($current_user->ID,'llms_billing_zip',true);                
                $phone=get_user_meta($current_user->ID,'llms_phone',true);
                $license_number= get_user_meta($current_user->ID,'license_number',true);
                $license_state=get_user_meta($current_user->ID,'license_state',true);
                
            
                ?>
              <div class="purchase-card">
                <div class="card-header">
                    <h3><i class="bi bi-person-fill me-2"></i>Customer Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 border-end-lg">                            
                            <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    You are logged in as <strong><?php echo esc_html($current_user->display_name); ?></strong>
                                    <a href="<?php echo wp_logout_url(get_permalink()); ?>" class="btn btn-sm btn-outline-secondary ms-2">Logout</a>
                            </div>
                            <form id="updateUserForm" class="customer-form" data-nonce="<?php echo wp_create_nonce('update_user_nonce'); ?>">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_fullname" class="form-label">Full Name (to print on your certificate) <span class="required">*</span></label>
                                            <input type="text" class="form-control" id="signup_fullname" name="fullname" value="<?php echo $current_user->display_name; ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_license" class="form-label">License Number</label>
                                            <input type="text" class="form-control" id="signup_license" name="license" value="<?php echo $license_number; ?>">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_license_state" class="form-label">License State</label>
                                            <select class="form-select form-card-chooser" id="signup_license_state" name="license_state">                                             
                                                <!-- Add more states -->
                                                 <?php foreach($state_list as $state_item):?>
                                                    <option <?php if($license_state==$state_item) echo 'selected'; ?> value="<?php echo $state_item; ?>"> 
                                                        <?php echo $state_item; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_email" class="form-label">Email (receipt is sent here) <span class="required">*</span></label>
                                            <input type="email" class="form-control" id="signup_email" name="email" value="<?php echo $current_user->user_email; ?>" required>
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="signup_phone" name="phone" value="<?php echo $phone; ?>">
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="signup_address" name="address" value="<?php echo $address; ?>">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="signup_city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="signup_city" name="city" value="<?php echo $city; ?>">
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="signup_state" class="form-label">State</label>
                                            <select class="form-select form-card-chooser" id="signup_state" name="state">
                                                 <?php foreach($state_list as $state_item):?>
                                                    <option <?php if($state==$state_item) echo 'selected'; ?> value="<?php echo $state_item; ?>"> 
                                                        <?php echo $state_item; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <label for="signup_zip" class="form-label">Zip</label>
                                            <input type="text" class="form-control" id="signup_zip" name="zip" value="<?php echo $zip; ?>">
                                        </div>
                                        
                                        <div class="col-md-12 mb-3">
                                            <label for="signup_username" class="form-label">Username</label>
                                            <input type="text" class="form-control disabled" disabled="disabled" id="signup_username" value="<?php echo $current_user->user_login; ?>" name="username" required>
                                        </div>                                       
                                        
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-person-lines-fill me-2"></i>Update Account
                                            </button>
                                        </div>
                                    </div>
                            </form>

                        </div>     

                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Payment Details Card -->
            <div class="purchase-card">
                <div class="card-header">
                    <h3><i class="bi bi-credit-card-fill me-2"></i>Payment Details</h3>
                </div>
                <div class="card-body">
                    <div class="payment-cards mb-3">
                        <span class="me-2">Credit/Debit cards accepted:</span>                       
                        <i class="bi bi-credit-card-fill text-primary" style="font-size: 1.5rem;"></i>
                        <i class="bi bi-credit-card-2-front-fill text-warning ms-1" style="font-size: 1.5rem;"></i>
                        <i class="bi bi-credit-card-2-back-fill text-info ms-1" style="font-size: 1.5rem;"></i>
                    </div>
                    
                    <form id="paymentForm" 
                                class="payment-form" 
                                data-nonce="<?php echo wp_create_nonce('process_payment_nonce'); ?>"
                                data-completion-code="<?php echo esc_attr($completion_code); ?>"
                                data-course-id="<?php echo esc_attr($course_id); ?>">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="card_number" class="form-label">Card Number <span class="required">*</span></label>
                                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="card_month" class="form-label">Expires <span class="required">*</span></label>
                                <select class="form-select form-card-chooser" id="card_month" name="card_month" required>
                                    <option value="">MM</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2 mb-3">
                                <label for="card_year" class="form-label">&nbsp;</label>
                                <select class="form-select form-card-chooser" id="card_year" name="card_year" required>
                                    <option value="">YYYY</option>
                                    <?php 
                                    $current_year = date('Y');
                                    for($i = 0; $i <= 10; $i++): 
                                    ?>
                                        <option value="<?php echo $current_year + $i; ?>"><?php echo $current_year + $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                              <div class="col-md-4 mb-3">
                                <label for="card_cvv" class="form-label">CVV <span class="required">*</span></label>
                                <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4" required>                                
                            </div>

                            <!-- pass value from ajax -->
                            <input type="hidden" id="base-price" value="<?php echo $price; ?>">
                            <input type="hidden" id="ce-discount-amount" value="<?php echo isset($discount_ce_amount) ? $discount_ce_amount : 0; ?>">
                            <input type="hidden" id="ce-discount-percent" value="<?php echo isset($current_reward_info['discount']) ? $current_reward_info['discount'] : 0; ?>">
                            <input type="hidden" id="course-id" value="<?php echo $course_id; ?>">
                            <input type="hidden" id="applied-coupon-id" value="">
                            <input type="hidden" id="mail-fee-value" value="9.00">
                            <input type="hidden" id="final-price-amount" name="final-price-amount" value="<?php echo $final_price; ?>">
                            <!-- end passs value from ajax -->
                            
                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-success btn-lg" id="purchase-btn">
                                    <i class="bi bi-lock-fill me-2"></i>Purchase Certificate</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <style>
        .form-card-chooser
        {
            padding:0px 10px;
            
        }
        .purchase-certificate-section {
            padding: 40px 0;
            background: #f8f9fa;
        }

        .page-title {
            color: #2c5f7c;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .purchase-info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .purchase-info-box ul {
            margin-bottom: 15px;
            padding-left: 0;
            list-style: none;
        }

        .purchase-info-box li {
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .purchase-info-box .contact-info {
            margin-bottom: 0;
            text-align: center;
            font-size: 0.95rem;
        }

        .purchase-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .purchase-card .card-header {
            background: linear-gradient(135deg, #4a90af 0%, #2c5f7c 100%);
            color: white;
            padding: 15px 20px;
        }

        .purchase-card .card-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .purchase-card .card-body {
            padding: 25px;
        }

        .course-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .course-title {
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .completion-code {
            background: #4a90af;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .course-date {
            color: #666;
            font-size: 0.9rem;
        }

        .price-breakdown .table {
            margin-bottom: 15px;
        }

        .price-breakdown .table td {
            padding: 10px 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .price-breakdown .discount-row td {
            color: #198754;
        }

        .price-breakdown .total-row {
            background: #f8f9fa;
            font-size: 1.2rem;
        }

        .price-breakdown .total-row td {
            padding: 15px 5px;
            border-top: 2px solid #2c5f7c;
            border-bottom: none;
        }

        .section-title {
            font-size: 1.1rem;
            color: #2c5f7c;
            margin-bottom: 20px;
            padding-bottom: 10px;            
        }

        .customer-form .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .required {
            color: #dc3545;
        }

        .border-end-lg {
            border-right: 1px solid #dee2e6;
            padding-right: 30px;
        }

        @media (max-width: 991px) {
            .border-end-lg {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
                padding-right: 15px;
                padding-bottom: 30px;
                margin-bottom: 30px;
            }
        }

        .payment-cards {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .payment-form {
            max-width: 600px;
        }

        #purchase-btn {
            min-width: 300px;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4a90af;
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 175, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a90af 0%, #2c5f7c 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3d7a94 0%, #234d65 100%);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #19692c 100%);
        }

        .alert {
            border-radius: 6px;
        }

        /* Loading State */
        .btn.loading {
            position: relative;
            color: transparent;
        }

        .btn.loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner {
            to { transform: rotate(360deg); }
        }

        /* Validation Styles */
        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem);
        }

        .form-control.is-valid,
        .form-select.is-valid {
            border-color: #198754;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem);
            padding-right: calc(1.5em + 0.75rem);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .invalid-feedback.d-block {
            display: block;
        }
    </style>

   

    <?php
    return ob_get_clean();
}
add_shortcode('purchase_certificate', 'purchase_certificate_shortcode');


?>
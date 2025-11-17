<?php
/**
 * Customer Account Page
 * Shortcode: [customer_account]
 */

function customer_account_shortcode($atts) {
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '<div class="container mt-5">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Please <a href="' .get_login_page_url(). '">login</a> to access your account.
            </div>
        </div>';
    }
    
    global $wpdb;
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $state_list=get_llms_states_by_country();
    
    // Get user meta information
    $email = $current_user->user_email;
    $full_name = $current_user->display_name;
    $license_number = get_user_meta($user_id, 'signup_license', true);
    $license_state = get_user_meta($user_id, 'license_state', true);
    $phone = get_user_meta($user_id, 'llms_phone', true);
    $address = get_user_meta($user_id, 'llms_billing_address_1', true);
    $city = get_user_meta($user_id, 'llms_billing_city', true);
    $state = get_user_meta($user_id, 'llms_billing_state', true);
    $zip = get_user_meta($user_id, 'llms_billing_zip', true);
    
    // Get CERewards information
    $completed_hours = get_user_meta($user_id, 'completed_hours', true) ?: 0;
    $completed_hours = floatval($completed_hours);
    
    // Calculate discount tier
    $discount_percentage = 0;
    $next_tier_hours = 10;
    if ($completed_hours >= 10) {
        $discount_percentage = 5;
        $next_tier_hours = 20; // You can adjust this based on your tier system
    }
    
    // Get completed courses using core-features function
    $course_manager = my_lifterlms_courses();
    $courses_data = $course_manager->get_courses_of_student($user_id, 100);      
    $completed_courses = array();
    
    if ($courses_data && !empty($courses_data['results'])) {
        foreach ($courses_data['results'] as $course_id) {
           
            $student = llms_get_student($user_id);
            
            // Check if course is completed
          // if (llms_is_complete($user_id,$course_id,'course')) {
                $course_data = $course_manager->get_single_course_data($course_id);
               
                if ($course_data) {
                    // Get completion date
                    $completion_date = $student->get_completion_date($course_id);                                  
                    
                    $completed_courses[] = array(
                        'course_id' => $course_id,
                        'title' => $course_data['post_title'],
                        'hours' => !empty($course_data['llmscehours']) ? floatval($course_data['llmscehours']) : 0,
                        'completion_date' => $completion_date,
                        //'certificate_number' => $certificate_record ? $certificate_record->completion_code : '',
                        'certificate_number' => 'aaaaaaa',
                        'course_link' => $course_data['course_link']
                    );
                }
           // }
        }
    }
    
    ob_start();
    ?>
    
    <section class="customer-account-section">
        <div class="container">
            <h2 class="page-title"><i class="bi bi-person-circle me-2"></i>Customer Account</h2>
            
            <!-- CERewards Card -->
            <div class="account-card cerewards-card">
                <div class="card-header">
                    <h3><i class="bi bi-trophy-fill me-2"></i>CERewards™</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        With CERewards you automatically receive discounts as you complete more courses. 
                        <a href="#" class="text-decoration-none">Click here for more information</a>.
                    </p>
                    
                    <div class="rewards-info">
                        <div class="rewards-status">
                            <p class="mb-2">
                                You have completed <strong><?php echo number_format($completed_hours, 0); ?> hours</strong> of courses.
                            </p>
                            <p class="mb-0">
                                After you complete a total of <strong><?php echo $next_tier_hours; ?> hours</strong>, 
                                you will begin receiving an automatic <strong><?php echo ($discount_percentage > 0 ? $discount_percentage : 5); ?>%</strong> discount.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Completed But Not Yet Paid For -->
            <div class="account-card">
                <div class="card-header">
                    <h3><i class="bi bi-clock-history me-2"></i>Courses Completed But Not Yet Paid For</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table account-table">
                            <thead>
                                <tr>
                                    <th>Course Title</th>
                                    <th class="text-center">Hours</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Certificate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- This section will be populated by you later -->
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="bi bi-info-circle me-2"></i>No unpaid courses
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Courses Completed -->
            <div class="account-card">
                <div class="card-header">
                    <h3><i class="bi bi-check-circle-fill me-2"></i>Courses Completed</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table account-table">
                            <thead>
                                <tr>
                                    <th>Course Title</th>
                                    <th class="text-center">Hours</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Certificate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($completed_courses)): ?>
                                    <?php foreach ($completed_courses as $course): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo esc_url($course['course_link']); ?>" class="course-link">
                                                    <?php echo esc_html($course['title']); ?>
                                                </a>
                                            </td>
                                            <td class="text-center"><?php echo number_format($course['hours'], 0); ?></td>
                                            <td class="text-center">
                                                <?php 
                                                if ($course['completion_date']) {
                                                    echo date('m/d/Y', strtotime($course['completion_date']));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($course['certificate_number']): ?>
                                                    <a href="#" class="btn btn-sm btn-outline-primary certificate-link" 
                                                       data-certificate="<?php echo esc_attr($course['certificate_number']); ?>">
                                                        #<?php echo esc_html($course['certificate_number']); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="bi bi-inbox me-2"></i>No completed courses yet
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
             <?php if(is_user_logged_in()): ?>
                <?php 
                $city=get_user_meta($current_user->ID,'llms_billing_city',true);
                $address=get_user_meta($current_user->ID,'llms_billing_address_1',true);
                $state=get_user_meta($current_user->ID,'llms_billing_state',true);
                $zip=get_user_meta($current_user->ID,'llms_billing_zip',true);                
                $phone=get_user_meta($current_user->ID,'llms_phone',true);
                $license_number= get_user_meta($current_user->ID,'signup_license',true);
                $license_state=get_user_meta($current_user->ID,'license_state',true);
               
            
                ?>
              <div class="account-card">
                <div class="card-header">
                    <h3><i class="bi bi-person-fill me-2"></i>Account Information</h3>
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
                                        
                                        <div class="account-actions">
                                              <a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-right me-2"></i>Sign out
                                                </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-pencil-square me-2"></i>Update Account
                                            </button>
                                        </div>
                                    </div>
                            </form>

                        </div>     

                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <style>
        .customer-account-section {
            padding: 40px 0;
            background: #f8f9fa;
        }

        .page-title {
            color: #2c5f7c;
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .account-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .account-card .card-header {
            background: linear-gradient(135deg, #4a90af 0%, #2c5f7c 100%);
            color: white;
            padding: 15px 20px;
        }

        .account-card .card-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .account-card .card-body {
            padding: 25px;
        }

        /* CERewards Card */
        .cerewards-card .card-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        .rewards-info {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 15px;
        }

        .rewards-status p {
            line-height: 1.6;
        }

        /* Table Styles */
        .account-table {
            margin-bottom: 0;
        }

        .account-table thead {
            background: #f8f9fa;
        }

        .account-table thead th {
            border-bottom: 2px solid #2c5f7c;
            color: #2c5f7c;
            font-weight: 600;
            padding: 12px 15px;
        }

        .account-table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .account-table tbody tr:hover {
            background: #f8f9fa;
        }

        .course-link {
            color: #2c5f7c;
            text-decoration: none;
            font-weight: 500;
        }

        .course-link:hover {
            color: #4a90af;
            text-decoration: underline;
        }

        .certificate-link {
            font-size: 0.875rem;
            padding: 4px 12px;
        }

        /* Account Information Grid */
        .account-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .info-row label {
            font-weight: 600;
            color: #2c5f7c;
            min-width: 150px;
            margin-bottom: 0;
        }

        .info-row span {
            color: #333;
        }

        /* Account Actions */
        .account-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a90af 0%, #2c5f7c 100%);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3d7a94 0%, #234d65 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-outline-secondary {
            background: white;
            border: 1px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border-color: #4a90af;
            color: #4a90af;
        }

        .btn-outline-primary:hover {
            background: #4a90af;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .account-info-grid {
                grid-template-columns: 1fr;
            }
            
            .account-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-row label {
                margin-bottom: 5px;
            }
        }

        /* Empty State */
        .text-muted {
            color: #6c757d !important;
            font-style: italic;
        }

        /* Badge for CERewards */
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }
    </style>

    <?php
    return ob_get_clean();
}
add_shortcode('customer_account', 'customer_account_shortcode');
?>
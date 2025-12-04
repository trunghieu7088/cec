<?php
/**
 * Custom Ajax Login Form Shortcode
 * Usage: [custom_login_form]
 * Add this code to your theme's functions.php file
 */

function custom_login_form_shortcode($atts) {
    // Check if user is already logged in
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        return '<div class="contact-info-card">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        You are already logged in as <strong>' . esc_html($current_user->display_name) . '</strong>. 
                        <a href="' . wp_logout_url(get_permalink()) . '">Logout</a>
                    </div>
                </div>';
    }
    
    ob_start();
    ?>
    
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <!-- Sử dụng 'content-card' để lấy style khung trắng đổ bóng -->
                    <!-- Thêm 'fade-in visible' để có hiệu ứng xuất hiện -->
                    <div class="content-card login-card-wrapper fade-in visible">
                        
                        <!-- Tiêu đề sử dụng màu primary-blue -->
                        <h3 class="login-title">
                            <i class="bi bi-person-circle me-2"></i>Customer Account
                        </h3>
                        
                        <!-- Sử dụng 'note-box' từ CSS Index Page cho phần lưu ý -->
                        <div class="note-box mb-4">
                            <p class="m-0">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Note:</strong> You don't need to create an account until after you have completed a course. 
                                <a href="<?php echo esc_url(home_url('/#course')); ?>">Click here</a> to begin taking a course.
                            </p>
                        </div>

                        <!-- Alert message container -->
                        <div id="login-alert" class="alert alert-danger" style="display: none;"></div>

                        <form id="customLoginForm">
                            
                            <div class="mb-3">
                                <label for="user_login" class="form-label">Username <span class="required">*</span></label>
                                <input type="text" 
                                    class="form-control" 
                                    id="user_login" 
                                    name="username" 
                                    autocomplete="username"
                                    placeholder="Enter your username">
                                <div class="invalid-feedback">Please enter your username.</div>
                            </div>

                            <div class="mb-3">
                                <label for="user_pass" class="form-label">Password <span class="required">*</span></label>
                                <input type="password" 
                                    class="form-control" 
                                    id="user_pass" 
                                    name="password" 
                                    autocomplete="current-password"
                                    placeholder="Enter your password">
                                <div class="invalid-feedback">Please enter your password.</div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" 
                                    class="form-check-input" 
                                    id="rememberme" 
                                    name="rememberme" 
                                    value="1">
                                <label class="form-check-label" for="rememberme">
                                    Remember Me
                                </label>
                            </div>

                            <!-- Sử dụng class 'btn-submit' từ CSS Contact Page -->
                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn-submit" id="loginBtn">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>

                            <div class="login-footer text-center">
                                <p class="mb-3 mt-4">
                                    <a href="<?php echo get_custom_page_url_by_template('page-forgot-password.php'); ?>" class="forgot-link">
                                        <i class="bi bi-key-fill me-1"></i>I have forgotten my username or password
                                    </a>
                                </p>
                                <div class="conflict-notice mt-4 text-start small-help">
                                    <p class="mb-0">
                                        For additional help email 
                                        <a href="mailto:CustomerService@ContinuingEdCourses.Net">CustomerService@ContinuingEdCourses.Net</a> 
                                        or call <strong>858-484-4304</strong>.
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <!--
     <style>
        /* Additional styles for login form */
        #customLoginForm .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        #customLoginForm a {
            color: var(--secondary-color);
            transition: color 0.3s;
        }
        
        #customLoginForm a:hover {
            color: var(--primary-color);
        }

        /* Validation styles */
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-valid {
            border-color: #198754;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
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

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 175, 0.25);
        }

        /* Loading spinner */
        #loginBtn.loading {
            position: relative;
            color: transparent;
        }

        #loginBtn.loading::after {
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
    </style>
    -->
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        'use strict';
        // Validate single field
        function validateField(field) {
            var value = field.val().trim();
            var isValid = value !== '';
            
            if (isValid) {
                field.removeClass('is-invalid').addClass('is-valid');
                field.next('.invalid-feedback').removeClass('d-block');
            } else {
                field.removeClass('is-valid').addClass('is-invalid');
                field.next('.invalid-feedback').addClass('d-block');
            }
            
            return isValid;
        }

        // Validate all fields
        function validateForm() {
            var username = $('#user_login');
            var password = $('#user_pass');
            
            var isUsernameValid = validateField(username);
            var isPasswordValid = validateField(password);
            
            return isUsernameValid && isPasswordValid;
        }

        // Real-time validation on blur
        $('#user_login, #user_pass').on('blur', function() {
            validateField($(this));
        });

        // Remove validation on input
        $('#user_login, #user_pass').on('input', function() {
            if ($(this).hasClass('is-invalid') || $(this).hasClass('is-valid')) {
                validateField($(this));
            }
        });

        // Show alert message
        function showAlert(message, type) {
            var iconClass = type === 'success' ? 'bi-check-circle-fill' : 
                          type === 'danger' ? 'bi-exclamation-triangle-fill' : 
                          'bi-exclamation-circle-fill';
            
            $('#login-alert')
                .removeClass('alert-success alert-danger alert-warning')
                .addClass('alert-' + type)
                .html('<i class="bi ' + iconClass + ' me-2"></i>' + message)
                .fadeIn();
            
            // Auto hide after 5 seconds for non-error messages
            if (type === 'success') {
                setTimeout(function() {
                    $('#login-alert').fadeOut();
                }, 5000);
            }
        }

        // Handle form submit
        $('#customLoginForm').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
          //  console.log('Form submitted');
            // Validate form
            if (!validateForm()) {
                showAlert('Please fill in all required fields.', 'warning');
                return false;
            }
            
            // Get form data
            var username = $('#user_login').val();
            var password = $('#user_pass').val();
            var remember = $('#rememberme').is(':checked') ? 1 : 0;
            
            // Disable button and show loading
            var $btn = $('#loginBtn');
            $btn.prop('disabled', true).addClass('loading').html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>');
            $('#login-alert').fadeOut();
            
            // Ajax request
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'custom_ajax_login',
                    username: username,
                    password: password,
                    remember: remember,
                    security: '<?php echo wp_create_nonce('ajax-login-nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.data.message, 'success');
                        
                        // Redirect after 1 second
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    } else {
                        showAlert(response.data.message, 'danger');
                        $btn.prop('disabled', false).removeClass('loading').html('<i class="bi bi-box-arrow-in-right me-2"></i>Sign In');
                        
                        // Mark fields as invalid
                       // $('#user_login, #user_pass').addClass('is-invalid');
                       $('#user_login, #user_pass').removeClass('is-valid');
                    }
                },
                error: function(xhr, status, error) {
                    showAlert('An error occurred. Please try again.', 'danger');
                    $btn.prop('disabled', false).removeClass('loading');
                    console.error('Ajax error:', error);
                }
            });
            
            return false;
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'custom_login_form_shortcode');


/**
 * Ajax Login Handler
 */
function custom_ajax_login() {
    // Check nonce
    check_ajax_referer('ajax-login-nonce', 'security');
    
    // Get POST data
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
    
    // Validate input
    if (empty($username) || empty($password)) {
        wp_send_json_error(array(
            'message' => 'Please enter both username and password.'
        ));
    }
    
    // Attempt login
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    );
    
    $user = wp_signon($creds, false);
    
    // Check for errors
    if (is_wp_error($user)) {
        wp_send_json_error(array(
            'message' => 'Invalid username or password. Please try again.'
        ));
    }
    
    // Success - set auth cookie
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);
    
    // Determine redirect URL
    $redirect_to = get_custom_page_url_by_template('page-account.php');
    
    // Allow filtering of redirect URL
    $redirect_to = apply_filters('custom_ajax_login_redirect', $redirect_to, $user);
    
    wp_send_json_success(array(
        'message' => 'Login successful! Redirecting...',
        'redirect' => $redirect_to
    ));
}
add_action('wp_ajax_nopriv_custom_ajax_login', 'custom_ajax_login');
add_action('wp_ajax_custom_ajax_login', 'custom_ajax_login');


/**
 * Custom login redirect filter
 */
function custom_ajax_login_redirect_filter($redirect_to, $user) {
    // You can customize redirect based on user role
    if (in_array('administrator', $user->roles)) {
        return admin_url();
    }
    return $redirect_to;
}
add_filter('custom_ajax_login_redirect', 'custom_ajax_login_redirect_filter', 10, 2);
?>
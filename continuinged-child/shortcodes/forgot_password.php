<?php
/**
 * Custom Forgot Password Form Shortcode
 * Usage: [custom_forgot_password_form]
 * Add this code to your theme's functions.php file
 */

function custom_forgot_password_form_shortcode($atts) {
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
    
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="contact-form-card">
                        <h3><i class="bi bi-key-fill me-2"></i>Reset Your Password</h3>
                        
                        <div class="info-box mb-4">
                            <p><i class="bi bi-info-circle-fill me-2"></i>
                                Enter your username or email address and we'll send you a link to reset your password.
                            </p>
                        </div>

                        <!-- Alert Messages -->
                        <div id="forgotPasswordAlert"></div>

                        <form id="customForgotPasswordForm">
                            
                            <div class="mb-3">
                                <label for="user_login" class="form-label">Username or Email <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="user_login" 
                                       name="user_login" 
                                       autocomplete="username"
                                       placeholder="Enter your username or email address"
                                       required>
                                <div class="invalid-feedback">
                                    Please enter your username or email address.
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary" id="resetPasswordBtn">
                                    <i class="bi bi-envelope-fill me-2"></i>Send Reset Link
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-2 mt-4">
                                    <a href="<?php echo get_custom_page_url_by_template('page-login.php'); ?>" class="text-decoration-none">
                                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                                    </a>
                                </p>
                                <p class="text-muted small mt-3">
                                    If you are having trouble, email 
                                    <a href="mailto:CustomerService@ContinuingEdCourses.Net">CustomerService@ContinuingEdCourses.Net</a> 
                                    or call 858-484-4304.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Additional styles for forgot password form */
        #customForgotPasswordForm .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        #customForgotPasswordForm a {
            color: var(--secondary-color);
            transition: color 0.3s;
        }
        
        #customForgotPasswordForm a:hover {
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

        /* Loading state */
        #resetPasswordBtn.loading {
            pointer-events: none;
            opacity: 0.65;
        }

        #resetPasswordBtn.loading .bi-envelope-fill {
            display: none;
        }

        #resetPasswordBtn.loading::before {
            content: "";
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#customForgotPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous messages
            $('#forgotPasswordAlert').html('');
            $('.form-control').removeClass('is-invalid is-valid');
            $('.invalid-feedback').removeClass('d-block');
            
            var userLogin = $('#user_login').val().trim();
            var $submitBtn = $('#resetPasswordBtn');
            
            // Basic validation
            if (userLogin === '') {
                $('#user_login').addClass('is-invalid');
                $('#user_login').next('.invalid-feedback').addClass('d-block');
                return false;
            }
            
            // Add loading state
            $submitBtn.addClass('loading').prop('disabled', true);
           // $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...');
            $submitBtn.html('Sending...');
            
            // AJAX request
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'custom_forgot_password',
                    user_login: userLogin,
                    nonce: '<?php echo wp_create_nonce('forgot-password-nonce'); ?>'
                },
                success: function(response) {
                    // Remove loading state
                    $submitBtn.removeClass('loading').prop('disabled', false);
                    $submitBtn.html('<i class="bi bi-envelope-fill me-2"></i>Send Reset Link');
                    
                    if (response.success) {
                        // Show success message
                        $('#forgotPasswordAlert').html(
                            '<div class="alert alert-success">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' +
                                response.data.message +
                            '</div>'
                        );
                        
                        // Mark field as valid
                        $('#user_login').addClass('is-valid');
                        
                        // Clear form
                        $('#customForgotPasswordForm')[0].reset();
                        
                        // Optional: Redirect after 3 seconds
                        setTimeout(function() {
                            window.location.href = '<?php echo esc_url(home_url('/login-page/')); ?>';
                        }, 3000);
                        
                    } else {
                        // Show error message
                        $('#forgotPasswordAlert').html(
                            '<div class="alert alert-danger">' +
                                '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                                response.data.message +
                            '</div>'
                        );
                        
                        // Mark field as invalid
                        $('#user_login').addClass('is-invalid');
                    }
                },
                error: function(xhr, status, error) {
                    // Remove loading state
                    $submitBtn.removeClass('loading').prop('disabled', false);
                    $submitBtn.html('<i class="bi bi-envelope-fill me-2"></i>Send Reset Link');
                    
                    // Show error message
                    $('#forgotPasswordAlert').html(
                        '<div class="alert alert-danger">' +
                            '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                            'An error occurred. Please try again later.' +
                        '</div>'
                    );
                }
            });
            
            return false;
        });
        
        // Remove validation on input
        $('#user_login').on('input', function() {
            $(this).removeClass('is-invalid is-valid');
            $(this).next('.invalid-feedback').removeClass('d-block');
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_forgot_password_form', 'custom_forgot_password_form_shortcode');


/**
 * AJAX Handler for Forgot Password
 */
function custom_forgot_password_ajax_handler() {
    // Verify nonce
    check_ajax_referer('forgot-password-nonce', 'nonce');
    
    // Get user login (username or email)
    $user_login = sanitize_text_field($_POST['user_login']);
    
    if (empty($user_login)) {
        wp_send_json_error(array(
            'message' => 'Please enter your username or email address.'
        ));
    }
    
    // Use WordPress built-in function to retrieve password
    $result = retrieve_password($user_login);
    
    if (is_wp_error($result)) {
        // Get error message
        $error_message = $result->get_error_message();
        
        // Customize error messages
        if (strpos($error_message, 'Invalid username or email') !== false) {
            $error_message = 'No account found with that username or email address.';
        }
        
        wp_send_json_error(array(
            'message' => $error_message
        ));
    } else {
        wp_send_json_success(array(
            'message' => 'Password reset link has been sent to your email address. Please check your inbox.'
        ));
    }
}
add_action('wp_ajax_nopriv_custom_forgot_password', 'custom_forgot_password_ajax_handler');
add_action('wp_ajax_custom_forgot_password', 'custom_forgot_password_ajax_handler');


/**
 * Customize password reset email - redirect to custom reset page
 */
function custom_retrieve_password_message($message, $key, $user_login, $user_data) {
    // Use custom reset password page instead of wp-login.php
    $reset_url = home_url('/reset-password/') . "?key=$key&login=" . rawurlencode($user_login);
    
    $message = sprintf(__('Someone has requested a password reset for the following account:')) . "\r\n\r\n";
    $message .= sprintf(__('Site Name: %s'), get_bloginfo('name')) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
    $message .= $reset_url . "\r\n\r\n";
    $message .= __('This link will expire in 24 hours.') . "\r\n\r\n";
    $message .= __('If you need assistance, please contact:') . "\r\n";
    $message .= __('Email: CustomerService@ContinuingEdCourses.Net') . "\r\n";
    $message .= __('Phone: 858-484-4304') . "\r\n";
    
    return $message;
}
add_filter('retrieve_password_message', 'custom_retrieve_password_message', 10, 4);


/**
 * Custom Reset Password Form Shortcode
 * Usage: [custom_reset_password_form]
 */
function custom_reset_password_form_shortcode($atts) {
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
    
    // Get reset key and login from URL
    $reset_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $user_login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';
    
    // Verify reset key
    $user = check_password_reset_key($reset_key, $user_login);
    $is_valid_key = !is_wp_error($user);
    
    ob_start();
    ?>
    
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="contact-form-card">
                        <h3><i class="bi bi-shield-lock-fill me-2"></i>Reset Your Password</h3>
                        
                        <?php if (!$reset_key || !$user_login): ?>
                            <!-- Missing parameters -->
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Invalid password reset link. Please request a new password reset.
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo esc_url(home_url('/forgot-password/')); ?>" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Request Password Reset
                                </a>
                            </div>
                        
                        <?php elseif (!$is_valid_key): ?>
                            <!-- Invalid or expired key -->
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                This password reset link has expired or is invalid. Please request a new one.
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo esc_url(home_url('/forgot-password/')); ?>" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Request New Reset Link
                                </a>
                            </div>
                        
                        <?php else: ?>
                            <!-- Valid reset key - show form -->
                            <div class="info-box mb-4">
                                <p><i class="bi bi-info-circle-fill me-2"></i>
                                    Enter your new password below. Make sure it's strong and secure.
                                </p>
                            </div>

                            <!-- Alert Messages -->
                            <div id="resetPasswordAlert"></div>

                            <form id="customResetPasswordForm">
                                
                                <input type="hidden" name="reset_key" value="<?php echo esc_attr($reset_key); ?>">
                                <input type="hidden" name="user_login" value="<?php echo esc_attr($user_login); ?>">
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password <span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password" 
                                               name="new_password" 
                                               autocomplete="new-password"
                                               placeholder="Enter your new password"
                                               required>                                       
                                    </div>                                 
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           autocomplete="new-password"
                                           placeholder="Re-enter your new password"
                                           required>
                                    <div class="invalid-feedback">
                                        Passwords do not match.
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mb-3">
                                    <button type="submit" class="btn btn-primary" id="resetPasswordSubmitBtn">
                                        <i class="bi bi-check-circle-fill me-2"></i>Reset Password
                                    </button>
                                </div>

                                <div class="text-center">
                                    <p class="mb-4 mt-4">
                                        <a href="<?php echo get_custom_page_url_by_template('page-login.php'); ?>" class="text-decoration-none">
                                            <i class="bi bi-arrow-left me-1"></i>Back to Login
                                        </a>
                                    </p>
                                    <p class="text-muted small mt-3">
                                        If you are having trouble, email 
                                        <a href="mailto:CustomerService@ContinuingEdCourses.Net">CustomerService@ContinuingEdCourses.Net</a> 
                                        or call 858-484-4304.
                                    </p>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Additional styles for reset password form */
        #customResetPasswordForm .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        #customResetPasswordForm a {
            color: var(--secondary-color);
            transition: color 0.3s;
        }
        
        #customResetPasswordForm a:hover {
            color: var(--primary-color);
        }

        /* Toggle password button */
        #togglePassword {
            border-color: #ced4da;
        }

        #togglePassword:hover {
            background-color: #e9ecef;
        }

        /* Password strength indicator */
        #passwordStrength.weak {
            color: #dc3545 !important;
        }

        #passwordStrength.medium {
            color: #ffc107 !important;
        }

        #passwordStrength.strong {
            color: #198754 !important;
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

        .input-group .form-control.is-valid,
        .input-group .form-control.is-invalid {
            background-position: right calc(2.5em + 0.1875rem) center;
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

        /* Loading state */
        #resetPasswordSubmitBtn.loading {
            pointer-events: none;
            opacity: 0.65;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // Toggle password visibility
        $('#togglePassword').on('click', function() {
            var passwordInput = $('#new_password');
            var icon = $('#toggleIcon');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });
        
   
        
        // Form submission
        $('#customResetPasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous messages
            $('#resetPasswordAlert').html('');
            $('.form-control').removeClass('is-invalid is-valid');
            $('.invalid-feedback').removeClass('d-block');
            
            var newPassword = $('#new_password').val();
            var confirmPassword = $('#confirm_password').val();
            var resetKey = $('input[name="reset_key"]').val();
            var userLogin = $('input[name="user_login"]').val();
            var $submitBtn = $('#resetPasswordSubmitBtn');
            
            var isValid = true;
            
            
            // Validate password match
            if (newPassword !== confirmPassword) {
                $('#confirm_password').addClass('is-invalid');
                $('#confirm_password').siblings('.invalid-feedback').addClass('d-block');
                isValid = false;
            } else if (confirmPassword.length > 0) {
                $('#confirm_password').addClass('is-valid');
            }
            
            if (!isValid) {
                return false;
            }
            
            // Add loading state
            $submitBtn.addClass('loading').prop('disabled', true);
            $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Resetting Password...');
            
            // AJAX request
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'custom_reset_password',
                    new_password: newPassword,
                    reset_key: resetKey,
                    user_login: userLogin,
                    nonce: '<?php echo wp_create_nonce('reset-password-nonce'); ?>'
                },
                success: function(response) {
                    // Remove loading state
                    $submitBtn.removeClass('loading').prop('disabled', false);
                    $submitBtn.html('<i class="bi bi-check-circle-fill me-2"></i>Reset Password');
                    
                    if (response.success) {
                        // Show success message
                        $('#resetPasswordAlert').html(
                            '<div class="alert alert-success">' +
                                '<i class="bi bi-check-circle-fill me-2"></i>' +
                                response.data.message +
                            '</div>'
                        );
                        
                        // Hide form
                        $('#customResetPasswordForm .mb-3').hide();
                        $submitBtn.hide();
                        
                        // Redirect to login page after 2 seconds
                        setTimeout(function() {
                            window.location.href = '<?php echo esc_url(home_url('/login-page/')); ?>';
                        }, 2000);
                        
                    } else {
                        // Show error message
                        $('#resetPasswordAlert').html(
                            '<div class="alert alert-danger">' +
                                '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                                response.data.message +
                            '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    // Remove loading state
                    $submitBtn.removeClass('loading').prop('disabled', false);
                    $submitBtn.html('<i class="bi bi-check-circle-fill me-2"></i>Reset Password');
                    
                    // Show error message
                    $('#resetPasswordAlert').html(
                        '<div class="alert alert-danger">' +
                            '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                            'An error occurred. Please try again later.' +
                        '</div>'
                    );
                }
            });
            
            return false;
        });
        
        // Remove validation on input
        $('#new_password, #confirm_password').on('input', function() {
            $(this).removeClass('is-invalid is-valid');
            $(this).siblings('.invalid-feedback').removeClass('d-block');
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_reset_password_form', 'custom_reset_password_form_shortcode');


/**
 * AJAX Handler for Reset Password
 */
function custom_reset_password_ajax_handler() {
    // Verify nonce
    check_ajax_referer('reset-password-nonce', 'nonce');
    
    // Get form data
    $new_password = $_POST['new_password'];
    $reset_key = sanitize_text_field($_POST['reset_key']);
    $user_login = sanitize_text_field($_POST['user_login']);
    
    // Validate inputs
    if (empty($new_password) || empty($reset_key) || empty($user_login)) {
        wp_send_json_error(array(
            'message' => 'All fields are required.'
        ));
    }
    
    
    
    // Verify reset key
    $user = check_password_reset_key($reset_key, $user_login);
    
    if (is_wp_error($user)) {
        wp_send_json_error(array(
            'message' => 'This password reset link has expired or is invalid.'
        ));
    }
    
    // Reset the password
    reset_password($user, $new_password);
    
    // Send success response
    wp_send_json_success(array(
        'message' => 'Your password has been reset successfully! Redirecting to login page...'
    ));
}
add_action('wp_ajax_nopriv_custom_reset_password', 'custom_reset_password_ajax_handler');
add_action('wp_ajax_custom_reset_password', 'custom_reset_password_ajax_handler');
?>
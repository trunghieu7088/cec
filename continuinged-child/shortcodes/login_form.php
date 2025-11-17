<?php
/**
 * Custom Login Form Shortcode
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
    
    // Get redirect URL (current page by default)
    $redirect = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : get_permalink();
    
    // Check for login errors
    $login_error = '';
    if (isset($_GET['login']) && $_GET['login'] == 'failed') {
        $login_error = '<div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Invalid username or password. Please try again.
                        </div>';
    }
    if (isset($_GET['login']) && $_GET['login'] == 'empty') {
        $login_error = '<div class="alert alert-warning">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            Please enter your username and password.
                        </div>';
    }
    
    ob_start();
    ?>
    
    <section class="contact-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="contact-form-card">
                        <h3><i class="bi bi-person-circle me-2"></i>Customer Account</h3>
                        
                        <div class="info-box mb-4">
                            <p><i class="bi bi-info-circle-fill me-2"></i>
                                Note: you don't need to create an account until after you have completed a course. 
                                <a href="<?php echo esc_url(home_url('/courses')); ?>">Click here</a> to begin taking a course.
                            </p>
                        </div>

                        <?php echo $login_error; ?>

                        <form id="customLoginForm" method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
                            
                            <div class="mb-3">
                                <label for="user_login" class="form-label">Username <span class="required">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="user_login" 
                                       name="log" 
                                       autocomplete="username"
                                       placeholder="Enter your username">
                               
                            </div>

                            <div class="mb-3">
                                <label for="user_pass" class="form-label">Password <span class="required">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="user_pass" 
                                       name="pwd" 
                                       autocomplete="current-password"
                                       placeholder="Enter your password">                             
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="rememberme" 
                                       name="rememberme" 
                                       value="forever">
                                <label class="form-check-label" for="rememberme">
                                    Remember Me
                                </label>
                            </div>

                            <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect); ?>">
                            <input type="hidden" name="testcookie" value="1">

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-2">
                                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-decoration-none">
                                        <i class="bi bi-key-fill me-1"></i>I have forgotten my username or password, help me retrieve it
                                    </a>
                                </p>
                                <p class="text-muted small">
                                    For additional help email 
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
    </style>

 

    <?php
    return ob_get_clean();
}
add_shortcode('custom_login_form', 'custom_login_form_shortcode');


/**
 * Custom login redirect to handle errors
 */
function custom_login_failed() {
    $login_page = home_url('/login-page/'); // Change this to your login page URL
    wp_redirect($login_page . '?login=failed');
    exit;
}
add_action('wp_login_failed', 'custom_login_failed');

function custom_verify_username_password($user, $username, $password) {
    // Only check if username or password is empty on login attempt
    if (isset($_POST['log']) && isset($_POST['pwd'])) {
        if (empty($username) || empty($password)) {
            $login_page = home_url('/login-page/'); // Change this to your login page URL
            wp_redirect($login_page . '?login=empty');
            exit;
        }
    }
    return $user;
}
add_filter('authenticate', 'custom_verify_username_password', 30, 3);

/**
 * Redirect after successful login (optional)
 */
function custom_login_redirect($redirect_to, $request, $user) {
    $login_page_url = get_login_page_url(); //url of the page using page login.php template
    if ($login_page_url  && strpos($redirect_to, $login_page_url) !== false) {
        return home_url('/dashboard/'); // Redirect đến trang account
    }
    return site_url(); // Change this to your desired page
}
add_filter('login_redirect', 'custom_login_redirect', 99, 3);
?>
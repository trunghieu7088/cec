<?php

/**
 * Plugin Name: Survey System
 * Description: Custom survey management system
 * Version: 1.0
 */

class WP_Survey_System
{

    private $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'survey_responses';

        // Hooks
        // add_action('init', array($this, 'register_survey_post_type'));
        add_action('wp_ajax_submit_survey', array($this, 'handle_survey_submission'));
        add_action('wp_ajax_nopriv_submit_survey', array($this, 'handle_survey_submission'));

        // Admin
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_survey_settings')); // ← THÊM DÒNG NÀY
        add_action('admin_enqueue_scripts', array($this, 'survey_enqueue_admin_scripts'));

        add_action('wp_ajax_get_survey_data', array($this, 'ajax_get_survey_data'));
        add_action('wp_ajax_delete_survey', array($this, 'ajax_delete_survey'));
        add_action('wp_ajax_export_surveys', array($this, 'ajax_export_surveys'));
    }

    /**
     * Tạo bảng khi activate plugin
     */
    public static function activate()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'survey_responses';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `survey_type` varchar(50) DEFAULT 'general',
          `survey_date` datetime NOT NULL,
          `user_id` bigint(20) unsigned DEFAULT NULL,
          `user_name` varchar(100) DEFAULT NULL,
          `user_email` varchar(100) DEFAULT NULL,
          `user_phone` varchar(20) DEFAULT NULL,
          `course_id` bigint(20) unsigned DEFAULT NULL,
          `survey_data` longtext NOT NULL,
          `ip_address` varchar(45) DEFAULT NULL,
          `user_agent` varchar(255) DEFAULT NULL,
          `referrer` varchar(255) DEFAULT NULL,
          `status` varchar(20) DEFAULT 'submitted',
          `notify_new_courses` tinyint(1) DEFAULT 0,
          `created_at` datetime NOT NULL,
          `updated_at` datetime DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `survey_date` (`survey_date`),
          KEY `user_id` (`user_id`),
          KEY `user_email` (`user_email`),
          KEY `survey_type` (`survey_type`),
          KEY `status` (`status`)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Add version option
        add_option('wp_survey_system_version', '1.0');
    }

    /**
     * Xử lý submit survey
     */
    public function handle_survey_submission()
    {
        global $wpdb;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verify nonce
        if (!isset($_POST['_survery_nonce_field']) || !wp_verify_nonce($_POST['_survery_nonce_field'], 'general_survey_nonce')) {
            wp_send_json_error('Invalid security token');
            return;
        }

        // THÊM VALIDATION CHO REQUIRED FIELDS
        if (empty($_POST['r160']) || empty($_POST['r170']) || empty($_POST['r190'])) {
            wp_send_json_error('Please fill in all required contact information (Name, Email, Phone)');
            return;
        }

        // VALIDATE EMAIL FORMAT
        if (!is_email($_POST['r170'])) {
            wp_send_json_error('Please enter a valid email address');
            return;
        }

        // VALIDATE CAPTCHA - THÊM ĐOẠN NÀY
        if (empty($_POST['HumanVerify'])) {
            wp_send_json_error('Please enter the verification code');
            return;
        }

        // CHECK CAPTCHA AGAINST SESSION
        if (
            !isset($_SESSION['survey_captcha']) ||
            strtoupper(trim($_POST['HumanVerify'])) !== $_SESSION['survey_captcha']
        ) {
            wp_send_json_error('Invalid verification code. Please try again.');
            return;
        }

        // CLEAR CAPTCHA FROM SESSION AFTER USE
        unset($_SESSION['survey_captcha']);
        // END CAPTCHA VALIDATION

        // CHECK IF AT LEAST ONE QUESTION IS ANSWERED - THÊM ĐOẠN NÀY
        $has_answer = false;
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'r') === 0 && !in_array($key, ['r160', 'r170', 'r180', 'r190']) && !empty($value)) {
                $has_answer = true;
                break;
            }
        }

        if (!$has_answer) {
            wp_send_json_error('Please answer at least one survey question');
            return;
        }
        // END



        // Sanitize và collect data
        $survey_data = $this->sanitize_survey_data($_POST);

        // Prepare insert data
        $insert_data = array(
            'survey_type' => sanitize_text_field($_POST['survey_type'] ?? 'general'),
            'survey_date' => current_time('mysql'),
            'user_id' => get_current_user_id() ?: null,
            'user_name' => sanitize_text_field($_POST['r160'] ?? ''),
            'user_email' => sanitize_email($_POST['r170'] ?? ''),
            'user_phone' => sanitize_text_field($_POST['r190'] ?? ''),
            'course_id' => isset($_POST['course_id']) ? absint($_POST['course_id']) : 0,
            'survey_data' => wp_json_encode($survey_data),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'referrer' => substr($_SERVER['HTTP_REFERER'] ?? '', 0, 255),
            'status' => 'submitted',
            'notify_new_courses' => isset($_POST['r180']) ? 1 : 0,
            'created_at' => current_time('mysql')
        );

        // Insert vào database
        $result = $wpdb->insert($this->table_name, $insert_data);

        if ($result) {
            $response_id = $wpdb->insert_id;

            // Log vào file (tương tự old system)
            $this->log_survey_submission($response_id, $survey_data);

            // Send email notification nếu cần
            if (!empty($insert_data['user_email'])) {
                $this->send_thank_you_email($insert_data['user_email'], $insert_data['user_name']);
            }

            wp_send_json_success(array(
                'message' => 'Thank you for your feedback!',
                'discount_code' =>  get_option('survey_default_discount_code', 'T3226'),
                'response_id' => $response_id
            ));
        } else {
            wp_send_json_error('Failed to save survey');
        }
    }

    /**
     * Sanitize survey data
     */
    private function sanitize_survey_data($post_data)
    {
        $questions = array();
        $calculated = array();

        // Các field cần loại bỏ
        $exclude_fields = array('action', 'nonce', 'HumanVerify', 'SubmitSurvey', 'survey_type', 'course_id');

        // Collect tất cả questions
        foreach ($post_data as $key => $value) {
            if (in_array($key, $exclude_fields)) continue;

            // Sanitize based on field type
            if (strpos($key, 'r') === 0) {
                if (is_array($value)) {
                    $questions[$key] = array_map('sanitize_text_field', $value);
                } else {
                    $questions[$key] = sanitize_textarea_field($value);
                }
            }
        }

        // Calculate additional info
        $calculated['profession_types'] = $this->get_selected_professions($questions);
        $calculated['topics_interested'] = $this->get_selected_topics($questions);
        $calculated['course_lengths_preferred'] = $this->get_preferred_lengths($questions);
        $calculated['total_questions_answered'] = count(array_filter($questions));

        return array(
            'questions' => $questions,
            'calculated' => $calculated,
            'metadata' => array(
                'browser' => $this->parse_user_agent(),
                'submission_timestamp' => time()
            )
        );
    }

    /**
     * Get selected professions
     */
    private function get_selected_professions($questions)
    {
        $professions = array();
        $map = array(
            'r70' => 'Psychologist',
            'r71' => 'Social Worker',
            'r72' => 'Marriage and Family Therapist',
            'r74' => 'Counselor'
        );

        foreach ($map as $key => $label) {
            if (!empty($questions[$key])) {
                $professions[] = $label;
            }
        }

        if (!empty($questions['r73']) && !empty($questions['r73text'])) {
            $professions[] = $questions['r73text'];
        }

        return $professions;
    }

    /**
     * Get selected topics
     */
    private function get_selected_topics($questions)
    {
        $topics = array();
        $topic_map = array(
            'r81'  => 'ADHD',
            'r82'  => 'Addiction',
            'r83'  => 'Aging and Long Term Care',
            'r84'  => 'Asperger\'s',
            'r85'  => 'Behavioral Assessment',
            'r86'  => 'Biofeedback',
            'r87'  => 'Brief Psychotherapy',
            'r88'  => 'Couples Therapy',
            'r89'  => 'Crisis Intervention',
            'r90'  => 'Death and Dying',
            'r91'  => 'Depression',
            'r92'  => 'Diagnosis/DSM-IV',
            'r93'  => 'Difficult Clients',
            'r94'  => 'Divorce',
            'r95'  => 'Domestic Violence',
            'r96'  => 'Drug Abuse',
            'r97'  => 'Eating Disorders',
            'r98'  => 'Ethics',
            'r99'  => 'Family Therapy',
            'r100' => 'Forensic Psychology',
            'r101' => 'Gay/Lesbian Issues',
            'r102' => 'Group Therapy',
            'r103' => 'Health Psychology',
            'r104' => 'Hypnosis',
            'r105' => 'Neuropsychology',
            'r106' => 'Organic Mental Disorders',
            'r107' => 'Pain Management',
            'r108' => 'Parenting Skills',
            'r109' => 'Personality Disorders',
            'r110' => 'Play Therapy',
            'r111' => 'Post-Traumatic Stress Disorder',
            'r112' => 'Professional Burnout',
            'r113' => 'Psychopharmacology',
            'r114' => 'Schizophrenia',
            'r115' => 'Social Skills Training',
            'r116' => 'Supervision',
            'r117' => 'Other (please list below)'
        );

        foreach ($topic_map as $key => $label) {
            if (!empty($questions[$key])) {
                $topics[] = $label;
            }
        }

        return $topics;
    }

    /**
     * Get preferred course lengths
     */
    private function get_preferred_lengths($questions)
    {
        $lengths = array();
        $length_map = array(
            'r41' => '1 hour',
            'r42' => '2 hours',
            'r43' => '3 hours',
            'r44' => '4 hours',
            'r45' => '5+ hours'
        );

        foreach ($length_map as $key => $label) {
            if (!empty($questions[$key])) {
                $lengths[] = $label;
            }
        }

        return $lengths;
    }

    /**
     * Get client IP
     */
    private function get_client_ip()
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }
        return sanitize_text_field($ip);
    }

    /**
     * Parse user agent
     */
    private function parse_user_agent()
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // Simple parsing - có thể dùng library chuyên dụng
        if (strpos($ua, 'Chrome') !== false) return 'Chrome';
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'Safari') !== false) return 'Safari';
        if (strpos($ua, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    /**
     * Log survey submission
     */
    private function log_survey_submission($response_id, $survey_data)
    {
        $upload_dir = wp_upload_dir();
        $log_file = $upload_dir['basedir'] . '/survey-logs/survey-' . date('Y-m') . '.log';

        // Create directory if not exists
        wp_mkdir_p(dirname($log_file));

        $log_entry = sprintf(
            "[%s] Response ID: %d | IP: %s | Data: %s\n",
            current_time('mysql'),
            $response_id,
            $this->get_client_ip(),
            wp_json_encode($survey_data)
        );

        error_log($log_entry, 3, $log_file);
    }

    /**
     * Send thank you email
     */
    private function send_thank_you_email($email, $name)
    {
        $subject = 'Thank you for completing our survey';
        $discount_code = get_option('survey_default_discount_code', 'T3226');
        $message = sprintf(
            "Dear %s,\n\n" .
                "Thank you for taking the time to complete our survey.\n\n" .
                "As a token of our appreciation, please use discount code: %s for 10%% off your next course.\n\n" .
                "Best regards,\n" .
                "The Team",
            $name ?: 'Valued Customer',
            $discount_code
        );

        wp_mail($email, $subject, $message);
    }

    /**
     * Generate random discount code
     */
    private function generate_discount_code()
    {
        // Generate format: T + 4 random digits (e.g., T3226)
        return 'T' . rand(1000, 9999);
    }

    /**
     * Admin menu
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'Survey Responses',
            'Surveys',
            'manage_options',
            'survey-responses',
            array($this, 'survey_admin_page'),
            'dashicons-feedback',
            30
        );

        // Submenu - Settings
        add_submenu_page(
            'survey-responses',
            'Survey Settings',
            'Settings',
            'manage_options',
            'survey-settings',
            array($this, 'survey_settings_page')
        );
    }

    /**
     * Admin page
     */
    public function survey_admin_page()
    {
        include  'survey-manage.php';
    }

    public function survey_enqueue_admin_scripts($hook)
    {
        if ($hook !== 'toplevel_page_survey-responses' && $hook !== 'surveys_page_survey-settings') {
            return;
        }

        wp_enqueue_style('fontawesome-admin', get_stylesheet_directory_uri() . '/assets/fontawesome6/css/all.min.css');

        wp_enqueue_style('datatable-style', get_stylesheet_directory_uri() . '/assets/datatables/datatables.min.css');
        wp_enqueue_script('datatables-js-script', get_stylesheet_directory_uri() . '/assets/datatables/datatables.min.js', array('jquery'), null, true);

        // Pass ajaxurl to script
        wp_localize_script('datatables', 'survey_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    /**
     * AJAX: Get survey data for DataTables
     */
    public function ajax_get_survey_data()
    {
        global $wpdb;

        check_ajax_referer('survey_data_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        // DataTables parameters
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 25;
        $search_value = isset($_POST['search']['value']) ? sanitize_text_field($_POST['search']['value']) : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1;
        $order_dir = isset($_POST['order'][0]['dir']) ? sanitize_text_field($_POST['order'][0]['dir']) : 'desc';
        $survey_type = isset($_POST['survey_type']) ? sanitize_text_field($_POST['survey_type']) : '';

        // Column mapping
        $columns = array('', 'id', 'survey_type', 'survey_date', 'user_name', 'user_email', 'user_phone', '');
        $order_by = isset($columns[$order_column]) && $columns[$order_column] ? $columns[$order_column] : 'id';

        // Base query
        $where = "1=1";

        // Survey type filter
        if ($survey_type) {
            $where .= $wpdb->prepare(" AND survey_type = %s", $survey_type);
        }

        // Search filter
        if ($search_value) {
            $where .= $wpdb->prepare(
                " AND (user_name LIKE %s OR user_email LIKE %s OR user_phone LIKE %s OR survey_data LIKE %s)",
                '%' . $wpdb->esc_like($search_value) . '%',
                '%' . $wpdb->esc_like($search_value) . '%',
                '%' . $wpdb->esc_like($search_value) . '%',
                '%' . $wpdb->esc_like($search_value) . '%'
            );
        }

        // Total records
        $total_query = "SELECT COUNT(*) FROM {$this->table_name}";
        $total_records = $wpdb->get_var($total_query);

        // Filtered records
        $filtered_query = "SELECT COUNT(*) FROM {$this->table_name} WHERE {$where}";
        $filtered_records = $wpdb->get_var($filtered_query);

        // Get data
        $data_query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE {$where} ORDER BY {$order_by} {$order_dir} LIMIT %d OFFSET %d",
            $length,
            $start
        );

        $data = $wpdb->get_results($data_query, ARRAY_A);

        // Response
        $response = array(
            'draw' => $draw,
            'recordsTotal' => intval($total_records),
            'recordsFiltered' => intval($filtered_records),
            'data' => $data
        );

        wp_send_json($response);
    }

    /**
     * AJAX: Delete survey
     */
    public function ajax_delete_survey()
    {
        global $wpdb;

        check_ajax_referer('delete_survey_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $survey_id = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;

        if (!$survey_id) {
            wp_send_json_error('Invalid survey ID');
        }

        $result = $wpdb->delete(
            $this->table_name,
            array('id' => $survey_id),
            array('%d')
        );

        if ($result) {
            wp_send_json_success('Survey deleted');
        } else {
            wp_send_json_error('Failed to delete survey');
        }
    }

    /**
     * THÊM MỚI: Register settings
     */
    public function register_survey_settings()
    {
        register_setting('survey_settings_group', 'survey_default_discount_code', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'T3226'
        ));

        add_settings_section(
            'survey_discount_section',
            '',
            array($this, 'discount_section_callback'),
            'survey-settings'
        );

        add_settings_field(
            'survey_default_discount_code',
            'Default Discount Code',
            array($this, 'discount_code_field_callback'),
            'survey-settings',
            'survey_discount_section'
        );
    }

    /**
     * THÊM MỚI: Section callback
     */
    public function discount_section_callback()
    {
        echo '<p>Set the default discount code that users will receive after completing the survey.</p>';
    }

    /**
     * THÊM MỚI: Field callback
     */
    public function discount_code_field_callback()
    {    
        $value = get_option('survey_default_discount_code', 'T3226');
        echo '<input type="text" name="survey_default_discount_code" value="' . esc_attr($value) . '" class="regular-text" placeholder="e.g., T3226" />';
        echo '<p class="description">Enter the discount code that will be shown to users after survey completion.</p>';
    }

    /**
     * THÊM MỚI: Settings page
     */
    public function survey_settings_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <?php if (isset($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>Settings saved successfully!</strong></p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('survey_settings_group');
                do_settings_sections('survey-settings');
                submit_button('Save Settings');
                ?>
            </form>

            <div class="card" style="max-width: 600px; margin-top: 20px;">
                <h2>Current Discount Code</h2>
                <p style="font-size: 24px; font-weight: bold; color: #2271b1;">
                    <?php echo esc_html(get_option('survey_default_discount_code', 'T3226')); ?>
                </p>
                <p class="description">This code will be displayed to users after they complete the survey.</p>
            </div>
        </div>
<?php
    }
}

// Activation hook
//register_activation_hook(__FILE__, array('WP_Survey_System', 'activate'));

// Initialize
new WP_Survey_System();

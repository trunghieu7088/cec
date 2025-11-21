<?php
/**
 * LifterLMS Course Email Notification System with Queue
 * 
 * Tự động gửi email đến students theo batch để tránh quá tải
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LLMS_Course_Email_Notification {

    /**
     * The single instance of the class.
     */
    protected static $instance = null;

    /**
     * Số lượng email gửi mỗi batch
     */
    const BATCH_SIZE = 50; // Gửi 50 emails mỗi lần

    /**
     * Khoảng thời gian giữa các batch (giây)
     */
    const BATCH_INTERVAL = 300; // 5 phút

    /**
     * Get instance
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        // Hook vào khi course được publish

         // Đăng ký custom cron interval
        add_filter( 'cron_schedules', array( $this, 'add_cron_interval' ) );
        
        add_action( 'transition_post_status', array( $this, 'send_new_course_notification' ), 10, 3 );
        
        // Thêm settings page vào admin menu
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        
        // Đăng ký settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        
        // Hook xử lý queue
        add_action( 'llms_process_email_queue', array( $this, 'process_email_queue' ) );
        
        // Hook hiển thị thông báo queue status
        add_action( 'admin_notices', array( $this, 'show_queue_status_notice' ) );
        

    }

    /**
     * Tạo queue khi có khóa học mới publish
     */

    public function send_new_course_notification( $new_status, $old_status, $post ) {
        // Kiểm tra xem có phải là course mới publish không
        if ( 'course' !== $post->post_type ) {
            return;
        }        

        // Chỉ gửi khi course mới được publish (không phải update)
        if ( 'publish' !== $new_status || 'publish' === $old_status ) {
            return;
        }

        if ( get_option( 'llms_course_notification_enabled', '1' ) !== '1' ) {
            return;
        }

        // Lấy thông tin khóa học
        if ( ! function_exists( 'my_lifterlms_courses' ) ) {
            return;
        }
        
        $course_data = my_lifterlms_courses()->get_single_course_data( $post->ID );
        
        if ( ! $course_data ) {
            return;
        }

        // Lấy danh sách recipients
        $recipients = $this->get_email_recipients();

        if ( empty( $recipients ) ) {
            return;
        }

        // Tạo queue trong database
        $this->create_email_queue( $post->ID, $recipients, $course_data );

            // ✅ THÊM: Schedule cron ngay sau khi tạo queue
            if ( ! wp_next_scheduled( 'llms_process_email_queue' ) ) {
                wp_schedule_event( time(), 'llms_email_interval', 'llms_process_email_queue' );
            }
        
    }

    /**
     * Tạo email queue trong database
     */
    protected function create_email_queue( $course_id, $recipients, $course_data ) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'llms_email_queue';
        
        // Tạo table nếu chưa có
        $this->create_queue_table();
        
        // Xóa queue cũ của course này (nếu có)
        $wpdb->delete( $table_name, array( 'course_id' => $course_id ) );
        
        // Thêm từng recipient vào queue
        $queue_id = time() . '_' . $course_id;
        $total_recipients = count( $recipients );
        
        foreach ( $recipients as $user ) {
            $wpdb->insert(
                $table_name,
                array(
                    'queue_id'     => $queue_id,
                    'course_id'    => $course_id,
                    'user_id'      => $user->ID,
                    'user_email'   => $user->user_email,
                    'course_data'  => json_encode( $course_data ),
                    'status'       => 'pending',
                    'created_at'   => current_time( 'mysql' )
                ),
                array( '%s', '%d', '%d', '%s', '%s', '%s', '%s' )
            );
        }
        
        // Lưu thông tin queue
        update_post_meta( $course_id, '_llms_email_queue', array(
            'queue_id'        => $queue_id,
            'total'           => $total_recipients,
            'sent'            => 0,
            'failed'          => 0,
            'status'          => 'processing',
            'created_at'      => current_time( 'mysql' )
        ) );
        
        // Set transient để hiển thị notice
        set_transient( 'llms_queue_created', array(
            'course_id' => $course_id,
            'total'     => $total_recipients
        ), 300 );
    }

    /**
     * Xử lý email queue (chạy bởi WP Cron)
     */
    public function process_email_queue() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'llms_email_queue';
        
        // Lấy batch emails chưa gửi
        $pending_emails = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} 
                WHERE status = 'pending' 
                ORDER BY id ASC 
                LIMIT %d",
                self::BATCH_SIZE
            )
        );
        
        if ( empty( $pending_emails ) ) {
            // Không còn email nào cần gửi, clear cron
            wp_clear_scheduled_hook( 'llms_process_email_queue' );
            return;
        }
        
        // Gửi từng email trong batch
        foreach ( $pending_emails as $queue_item ) {
            $user = get_user_by( 'id', $queue_item->user_id );
            
            if ( ! $user ) {
                // User không tồn tại, đánh dấu failed
                $wpdb->update(
                    $table_name,
                    array( 
                        'status'     => 'failed',
                        'error_msg'  => 'User not found',
                        'sent_at'    => current_time( 'mysql' )
                    ),
                    array( 'id' => $queue_item->id ),
                    array( '%s', '%s', '%s' ),
                    array( '%d' )
                );
                
                $this->update_queue_stats( $queue_item->course_id, 'failed' );
                continue;
            }
            
            $course_data = json_decode( $queue_item->course_data, true );
            
            // Gửi email
            $sent = $this->send_email( $user, $course_data );
            
            if ( $sent ) {
                // Cập nhật status thành sent
                $wpdb->update(
                    $table_name,
                    array( 
                        'status'  => 'sent',
                        'sent_at' => current_time( 'mysql' )
                    ),
                    array( 'id' => $queue_item->id ),
                    array( '%s', '%s' ),
                    array( '%d' )
                );
                
                $this->update_queue_stats( $queue_item->course_id, 'sent' );
            } else {
                // Đánh dấu failed
                $wpdb->update(
                    $table_name,
                    array( 
                        'status'     => 'failed',
                        'error_msg'  => 'Failed to send email',
                        'sent_at'    => current_time( 'mysql' )
                    ),
                    array( 'id' => $queue_item->id ),
                    array( '%s', '%s', '%s' ),
                    array( '%d' )
                );
                
                $this->update_queue_stats( $queue_item->course_id, 'failed' );
            }
            
            // Delay nhỏ giữa các email để tránh spam trigger
            usleep( 100000 ); // 0.1 giây
        }
    }

    /**
     * Cập nhật thống kê queue
     */
    protected function update_queue_stats( $course_id, $type ) {
        $queue_meta = get_post_meta( $course_id, '_llms_email_queue', true );
        
        if ( $queue_meta ) {
            if ( $type === 'sent' ) {
                $queue_meta['sent']++;
            } elseif ( $type === 'failed' ) {
                $queue_meta['failed']++;
            }
            
            // Kiểm tra xem đã gửi xong chưa
            if ( ( $queue_meta['sent'] + $queue_meta['failed'] ) >= $queue_meta['total'] ) {
                $queue_meta['status'] = 'completed';
                $queue_meta['completed_at'] = current_time( 'mysql' );
            }
            
            update_post_meta( $course_id, '_llms_email_queue', $queue_meta );
        }
    }

    /**
     * Tạo bảng queue trong database
     */
    protected function create_queue_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'llms_email_queue';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            queue_id varchar(100) NOT NULL,
            course_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            user_email varchar(255) NOT NULL,
            course_data longtext NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            error_msg text,
            created_at datetime NOT NULL,
            sent_at datetime,
            PRIMARY KEY  (id),
            KEY queue_id (queue_id),
            KEY course_id (course_id),
            KEY status (status)
        ) {$charset_collate};";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Hiển thị thông báo queue status
     */
    public function show_queue_status_notice() {
        $queue_created = get_transient( 'llms_queue_created' );
        
        if ( $queue_created ) {
            $course_id = $queue_created['course_id'];
            $total = $queue_created['total'];
            
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>Email Queue Created:</strong> ' . $total . ' emails đã được thêm vào hàng đợi cho khóa học ID #' . $course_id . '. Hệ thống sẽ tự động gửi ' . self::BATCH_SIZE . ' emails mỗi ' . ( self::BATCH_INTERVAL / 60 ) . ' phút.</p>';
            echo '</div>';
            
            delete_transient( 'llms_queue_created' );
        }
    }

    /**
     * Lấy danh sách users cần nhận email
     */
    protected function get_email_recipients() {
        $args = array(
            'role'       => 'student',
            'meta_query' => array(
                array(
                    'key'     => 'email_me',
                    'value'   => '1',
                    'compare' => '='
                )
            ),
            'fields' => 'all'
        );

        $user_query = new WP_User_Query( $args );
        return $user_query->get_results();
    }

    /**
     * Gửi email đến một user
     */
    protected function send_email( $user, $course_data ) {
        $to = $user->user_email;
        
        // Lấy tên tác giả/instructors
        $instructors_names = array();
        if ( ! empty( $course_data['instructors'] ) ) {
            foreach ( $course_data['instructors'] as $instructor ) {
                $instructors_names[] = $instructor['display_name'];
            }
        }
        $instructors_text = ! empty( $instructors_names ) ? implode( ', ', $instructors_names ) : 'Chưa có giảng viên';

        // Subject
        $subject = sprintf( 
            '[%s] New Course: %s', 
            get_bloginfo( 'name' ), 
            $course_data['post_title'] 
        );

        // Message
        $message = $this->get_email_template( $user, $course_data, $instructors_text );

        // Headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
        );

        // Gửi email
        return wp_mail( $to, $subject, $message, $headers );
    }

    /**
     * Lấy template email
     */
    protected function get_email_template( $user, $course_data, $instructors_text ) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0073aa; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; }
        .course-title { font-size: 24px; color: #0073aa; margin-bottom: 10px; }
        .course-info { margin: 20px 0; padding: 15px; background-color: white; border-left: 4px solid #0073aa; }
        .button { display: inline-block; padding: 12px 30px; background-color: #0073aa; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Course!</h1>
        </div>
        
        <div class="content">
            <p>Dear ' . esc_html( $user->display_name ) . ',</p>
            
            <p>We are so excited to announce the new course!</p>
            
            <div class="course-info">
                <div class="course-title">' . esc_html( $course_data['post_title'] ) . '</div>
                
                <p><strong>Instructor:</strong> ' . esc_html( $instructors_text ) . '</p>';
                
        if ( ! empty( $course_data['course_categories'] ) ) {
            $cat_names = array();
            foreach ( $course_data['course_categories'] as $cat ) {
                $cat_names[] = $cat['name'];
            }
            $html .= '<p><strong>Category:</strong> ' . esc_html( implode( ', ', $cat_names ) ) . '</p>';
        }
        
        $html .= '</div>
            
            <a style="color:#ffffff;" href="' . esc_url( $course_data['course_link'] ) . '" class="button">View Course</a>
            
            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                If you do not want to receive these notification emails anymore, please update the settings in your account.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; ' . date( 'Y' ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Thêm settings page vào admin menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'lifterlms',
            'Notification for new course',
            'Notification Email',
            'manage_options',
            'llms-course-notifications',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        // Save settings nếu form được submit
        if ( isset( $_POST['llms_save_notification_settings'] ) ) {
            check_admin_referer( 'llms_notification_settings_nonce' );
            
            $enabled = isset( $_POST['llms_course_notification_enabled'] ) ? '1' : '0';
            update_option( 'llms_course_notification_enabled', $enabled );
            
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
        }

        // Lấy giá trị hiện tại
        $enabled = get_option( 'llms_course_notification_enabled', '1' );
        
        // Lấy thống kê queue
        global $wpdb;
        $table_name = $wpdb->prefix . 'llms_email_queue';
        $queue_stats = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM {$table_name}"
        );
        ?>
        
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            
            <!-- Queue Statistics -->
            <?php if ( $queue_stats && $queue_stats->total > 0 ): ?>
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>📊 Email Queue Statistics</h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Total</th>
                            <th>Pending</th>
                            <th>Sent</th>
                            <th>Failed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong><?php echo number_format( $queue_stats->total ); ?></strong></td>
                            <td style="color: #f0ad4e;"><strong><?php echo number_format( $queue_stats->pending ); ?></strong></td>
                            <td style="color: #5cb85c;"><strong><?php echo number_format( $queue_stats->sent ); ?></strong></td>
                            <td style="color: #d9534f;"><strong><?php echo number_format( $queue_stats->failed ); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <p style="margin-top: 10px; color: #666;">
                    <em>System sends <?php echo self::BATCH_SIZE; ?> emails every <?php echo self::BATCH_INTERVAL / 60; ?> minutes.</em>
                </p>
            </div>
            <?php endif; ?>
            
            <!-- Settings Form -->
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>⚙️ Email Notification Settings</h2>
                
                <form method="post" action="">
                    <?php wp_nonce_field( 'llms_notification_settings_nonce' ); ?>
                    
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="llms_course_notification_enabled">
                                        Enable New Course Notifications
                                    </label>
                                </th>
                                <td>
                                    <label class="llms-toggle-switch">
                                        <input 
                                            type="checkbox" 
                                            id="llms_course_notification_enabled" 
                                            name="llms_course_notification_enabled" 
                                            value="1" 
                                            <?php checked( $enabled, '1' ); ?>
                                        />
                                        <span class="llms-slider"></span>
                                    </label>
                                    <p class="description">
                                        When enabled, students with "email_me" meta set to 1 will receive email notifications when a new course is published.
                                        <br><strong>Batch Size:</strong> <?php echo self::BATCH_SIZE; ?> emails per batch
                                        <br><strong>Interval:</strong> <?php echo self::BATCH_INTERVAL / 60; ?> minutes between batches
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <?php submit_button( 'Save Settings', 'primary', 'llms_save_notification_settings' ); ?>
                </form>
            </div>
            
            <style>
                .llms-toggle-switch {
                    position: relative;
                    display: inline-block;
                    width: 60px;
                    height: 34px;
                }
                
                .llms-toggle-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                
                .llms-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: .4s;
                    border-radius: 34px;
                }
                
                .llms-slider:before {
                    position: absolute;
                    content: "";
                    height: 26px;
                    width: 26px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }
                
                input:checked + .llms-slider {
                    background-color: #2271b1;
                }
                
                input:checked + .llms-slider:before {
                    transform: translateX(26px);
                }
            </style>
        </div>
        
        <?php
    }

    /**
     * Đăng ký settings
     */
    public function register_settings() {
        register_setting( 'llms_course_notification_settings', 'llms_course_notification_enabled' );
        
    }

    /**
     * Thêm custom cron interval
     */
    public function add_cron_interval( $schedules ) {
        $schedules['llms_email_interval'] = array(
            'interval' => self::BATCH_INTERVAL,
            'display'  => sprintf( __( 'Every %d minutes' ), self::BATCH_INTERVAL / 60 )
        );
        return $schedules;
    }
}

// Initialize the class
function llms_course_email_notification() {
    return LLMS_Course_Email_Notification::get_instance();
}

// Hook vào sau khi WordPress và plugins loaded
add_action( 'init', 'llms_course_email_notification', 999 );
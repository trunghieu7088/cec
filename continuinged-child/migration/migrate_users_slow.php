<?php
if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class CEC_Migrate_Users_Command {

        /**
         * Migrate users from legacy table to WordPress users.
         *
         * ## EXAMPLES
         *
         *     wp cec_migrate_users
         *
         */
        public function __invoke( $args, $assoc_args ) {
            global $wpdb;

            // --- CẤU HÌNH TÊN BẢNG CŨ Ở ĐÂY ---
            // Hãy thay 'users' bằng tên thực tế của bảng cũ trong database của bạn (ví dụ: 'tbl_users', 'old_users'...)
            $old_table_name = 'users'; 

            WP_CLI::log( "Bắt đầu lấy dữ liệu từ bảng: {$old_table_name}..." );

            // Kiểm tra bảng có tồn tại không
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$old_table_name'" ) != $old_table_name ) {
                WP_CLI::error( "Bảng '$old_table_name' không tồn tại trong database." );
                return;
            }

            // Lấy toàn bộ dữ liệu từ bảng cũ
            $old_users = $wpdb->get_results( "SELECT * FROM `$old_table_name`" );
            $total_count = count( $old_users );

            if ( $total_count === 0 ) {
                WP_CLI::error( "Không có dữ liệu nào trong bảng '$old_table_name'." );
                return;
            }

            WP_CLI::log( "Tìm thấy {$total_count} users cũ. Bắt đầu migrate..." );

            // Tạo thanh tiến trình
            $progress = \WP_CLI\Utils\make_progress_bar( 'Migrating users', $total_count );

            $success_count = 0;
            $skip_count = 0;
            $error_count = 0;

            foreach ( $old_users as $row ) {
                // Mapping dữ liệu cơ bản
                $user_login     = $row->Username;
                $user_pass      = $row->Password; // Sẽ được wp_insert_user tự động mã hóa (hash)
                $user_email     = $row->Email;
                $display_name   = $row->FullName;
                $registered     = $row->DateRegistered;
                
                // Chuẩn bị dữ liệu để insert
                // Lưu ý: Password cũ nếu là plain text thì wp_insert_user sẽ hash. 
                // Nếu password cũ đã là MD5/SHA1 thì user sẽ không login được trừ khi reset pass, 
                // vì WP dùng phpass. Giả sử password cũ là text thô (raw).
                
                // Tách FullName thành First/Last name để lưu cho đẹp (tùy chọn, nhưng tốt cho LifterLMS billing)
                $name_parts = explode(' ', $display_name, 2);
                $first_name = isset($name_parts[0]) ? $name_parts[0] : '';
                $last_name  = isset($name_parts[1]) ? $name_parts[1] : '';

                // Kiểm tra user đã tồn tại chưa
                if ( username_exists( $user_login ) || email_exists( $user_email ) ) {
                    // WP_CLI::warning( "User {$user_login} hoặc {$user_email} đã tồn tại. Bỏ qua." );
                    $skip_count++;
                    $progress->tick();
                    continue;
                }

                $userdata = array(
                    'user_login'      => $user_login,
                    'user_pass'       => $user_pass, 
                    'user_email'      => $user_email,
                    'display_name'    => $display_name,
                    'first_name'      => $first_name,
                    'last_name'       => $last_name,
                    'user_registered' => $registered,
                    'role'            => 'student', // Role theo yêu cầu
                );

                // Insert User
                $user_id = wp_insert_user( $userdata );

                if ( ! is_wp_error( $user_id ) ) {
                    // --- MIGRATE META DATA ---

                    // 1. Stable User ID (Quan trọng để đối chứng)
                    update_user_meta( $user_id, 'stable_user_id_cec', $row->UserId );

                    // 2. EmailMe -> email_me
                    update_user_meta( $user_id, 'email_me', $row->EmailMe );

                    // 3. LicenseNumber -> license
                    update_user_meta( $user_id, 'license', $row->LicenseNumber );

                    // 4. LicenseState -> license_state
                    update_user_meta( $user_id, 'license_state', $row->LicenseState );

                    // 5. PhoneNumber -> llms_phone (LifterLMS standard)
                    update_user_meta( $user_id, 'llms_phone', $row->PhoneNumber );

                    // 6. Address Mapping (LifterLMS Billing fields)
                    // Address -> llms_billing_address_1
                    update_user_meta( $user_id, 'llms_billing_address_1', $row->Address );

                    // City -> llms_billing_city
                    update_user_meta( $user_id, 'llms_billing_city', $row->City );

                    // State -> llms_billing_state
                    update_user_meta( $user_id, 'llms_billing_state', $row->State );

                    // Zip -> llms_billing_zip
                    update_user_meta( $user_id, 'llms_billing_zip', $row->Zip );
                    
                    // Bổ sung: Map thêm billing names để checkout LifterLMS mượt hơn
                    update_user_meta( $user_id, 'llms_billing_first_name', $first_name );
                    update_user_meta( $user_id, 'llms_billing_last_name', $last_name );
                    update_user_meta( $user_id, 'llms_billing_email', $user_email );

                    $success_count++;

                } else {
                    WP_CLI::warning( "Lỗi khi tạo user {$user_login}: " . $user_id->get_error_message() );
                    $error_count++;
                }

                $progress->tick();
            }

            $progress->finish();
            WP_CLI::success( "Hoàn tất migrate!" );
            WP_CLI::line( "Thành công: $success_count" );
            WP_CLI::line( "Đã tồn tại (Bỏ qua): $skip_count" );
            WP_CLI::line( "Lỗi: $error_count" );
        }
    }

    WP_CLI::add_command( 'cec_migrate_users', 'CEC_Migrate_Users_Command' );
}
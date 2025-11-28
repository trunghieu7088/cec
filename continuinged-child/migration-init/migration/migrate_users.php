<?php
if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class CEC_Migrate_Users_Command {

        public function __invoke( $args, $assoc_args ) {
            global $wpdb;

            // --- CẤU HÌNH ---
            $old_table_name = 'users'; 
            $batch_size = 1000; // Xử lý mỗi lần 1000 users để nhẹ RAM
            
            // 1. TỐI ƯU: Tắt update cache để tăng tốc
            wp_suspend_cache_addition( true );
            
            WP_CLI::log( "Đang tải danh sách User hiện tại để đối chiếu (tránh query lặp)..." );
            
            // 2. TỐI ƯU: Lấy tất cả username/email hiện có vào mảng để check siêu nhanh
            // Format: ['username' => true, 'email' => true]
            $existing_logins = $wpdb->get_col( "SELECT user_login FROM {$wpdb->users}" );
            $existing_emails = $wpdb->get_col( "SELECT user_email FROM {$wpdb->users}" );
            
            // Chuyển sang mảng hash để lookup O(1)
            $existing_logins = array_flip( $existing_logins );
            $existing_emails = array_flip( $existing_emails );

            // Đếm tổng
            $total_count = $wpdb->get_var( "SELECT COUNT(*) FROM `$old_table_name`" );
            if ( ! $total_count ) {
                WP_CLI::error( "Bảng '$old_table_name' trống hoặc không tồn tại." );
                return;
            }

            WP_CLI::log( "Tìm thấy {$total_count} users cũ. Bắt đầu migrate..." );
            $progress = \WP_CLI\Utils\make_progress_bar( 'Migrating users', $total_count );

            $offset = 0;
            $success_count = 0;
            $skip_count = 0;
            $error_count = 0;

            // Loop theo batch (Chunking) để không bị tràn RAM
            while ( $offset < $total_count ) {
                $old_users = $wpdb->get_results( "SELECT * FROM `$old_table_name` LIMIT $batch_size OFFSET $offset" );
                
                // Buffer để chứa dữ liệu meta chèn một thể
                $meta_inserts = []; 
                
                foreach ( $old_users as $row ) {
                    $user_login     = $row->Username;
                    $user_email     = $row->Email;

                    // 3. TỐI ƯU: Check tồn tại bằng mảng RAM (Siêu nhanh)
                    if ( isset( $existing_logins[$user_login] ) || isset( $existing_emails[$user_email] ) ) {
                        $skip_count++;
                        $progress->tick();
                        continue;
                    }

                    // Chuẩn bị data
                    $display_name   = $row->FullName;
                    $registered     = $row->DateRegistered;
                    $user_pass      = $row->Password; 

                    $name_parts = explode(' ', $display_name, 2);
                    $first_name = isset($name_parts[0]) ? $name_parts[0] : '';
                    $last_name  = isset($name_parts[1]) ? $name_parts[1] : '';

                    $userdata = array(
                        'user_login'      => $user_login,
                        'user_pass'       => $user_pass, 
                        'user_email'      => $user_email,
                        'display_name'    => $display_name,
                        'first_name'      => $first_name,
                        'last_name'       => $last_name,
                        'user_registered' => $registered,
                        'role'            => 'student', 
                    );

                    // Insert User (Vẫn dùng hàm WP để đảm bảo hash password và tạo ID chuẩn)
                    // Đây là đoạn chậm nhất không thể bỏ qua, nhưng đã loại bỏ được update_meta con
                    $user_id = wp_insert_user( $userdata );

                    if ( ! is_wp_error( $user_id ) ) {
                        $success_count++;

                        // Cập nhật lại mảng check tồn tại để tránh trùng trong cùng 1 file import
                        $existing_logins[$user_login] = true;
                        $existing_emails[$user_email] = true;

                        // 4. TỐI ƯU: Gom Meta Data vào buffer (Không gọi update_user_meta)
                        // Hàm helper bên dưới sẽ giúp escape dữ liệu
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'stable_user_id_cec', $row->UserId);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'email_me', $row->EmailMe);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'license', $row->LicenseNumber);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'license_state', $row->LicenseState);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_phone', $row->PhoneNumber);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_address_1', $row->Address);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_city', $row->City);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_state', $row->State);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_zip', $row->Zip);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_first_name', $first_name);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_last_name', $last_name);
                        $this->add_to_meta_buffer($meta_inserts, $user_id, 'llms_billing_email', $user_email);

                    } else {
                        // WP_CLI::warning( "Lỗi: " . $user_id->get_error_message() );
                        $error_count++;
                    }
                    
                    $progress->tick();
                } // End foreach batch

                // 5. TỐI ƯU: Thực hiện INSERT Meta một lần cho cả batch
                if ( ! empty( $meta_inserts ) ) {
                    $this->bulk_insert_meta( $meta_inserts );
                }

                // Clean RAM
                $wpdb->flush();
                unset( $old_users, $meta_inserts );
                
                $offset += $batch_size;
            }

            $progress->finish();
            wp_suspend_cache_addition( false ); // Bật lại cache

            WP_CLI::success( "Hoàn tất migrate!" );
            WP_CLI::line( "Thành công: $success_count" );
            WP_CLI::line( "Đã tồn tại (Bỏ qua): $skip_count" );
            WP_CLI::line( "Lỗi: $error_count" );
        }

        /**
         * Helper để gom meta data
         */
        private function add_to_meta_buffer( &$buffer, $user_id, $key, $value ) {
            global $wpdb;
            if ( empty( $value ) && $value !== '0' ) return; // Bỏ qua dữ liệu rỗng
            
            // Escape kỹ lưỡng vì chúng ta sẽ chạy Raw SQL
            $key = esc_sql( $key );
            $value = esc_sql( $value );
            
            $buffer[] = "($user_id, '$key', '$value')";
        }

        /**
         * Chạy 1 câu query INSERT cực lớn
         */
        private function bulk_insert_meta( $values_array ) {
            global $wpdb;
            if ( empty( $values_array ) ) return;

            // Chia nhỏ query nếu quá lớn (ví dụ max packet size của MySQL)
            // Chia mỗi lần insert khoảng 2000 dòng meta (khoảng 200 user)
            $chunks = array_chunk( $values_array, 2000 );

            foreach ( $chunks as $chunk ) {
                $sql = "INSERT INTO {$wpdb->usermeta} (user_id, meta_key, meta_value) VALUES " . implode( ',', $chunk );
                $wpdb->query( $sql );
            }
        }
    }

    WP_CLI::add_command( 'cec_migrate_users', 'CEC_Migrate_Users_Command' );
}
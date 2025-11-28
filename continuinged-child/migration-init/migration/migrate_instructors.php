<?php
/**
 * Tool Import Authors to WP Users (Role: Instructor)
 */

// 1. Tạo Menu trong Admin Dashboard
add_action('admin_menu', 'cec_register_import_menu');
function cec_register_import_menu() {
    add_submenu_page(
            'tools.php',                  // Parent menu: Tools (Công cụ)
            'Migrate Instructors',         // Tiêu đề trang
            'Migrate Instructors',         // Tên hiển thị trong menu
            'manage_options',             // Quyền yêu cầu
            'cec-import-instructors',     // Slug của trang
            'cec_render_import_page'      // Hàm render nội dung trang
        );
}

// 2. Hiển thị giao diện và Xử lý Logic
function cec_render_import_page() {
    global $wpdb;
    $table_name = 'authors'; // Tên bảng nguồn dữ liệu của bạn
    $message = '';

    // Kiểm tra nếu người dùng bấm nút Import
    if (isset($_POST['cec_run_import']) && check_admin_referer('cec_import_action', 'cec_import_nonce')) {
        
        // Lấy dữ liệu từ bảng authors
        // Lưu ý: Nếu bảng authors có prefix (vd: wp_authors) thì thêm $wpdb->prefix vào
        $authors = $wpdb->get_results("SELECT * FROM $table_name");

        if (empty($authors)) {
            $message = '<div class="notice notice-error"><p>Không tìm thấy bảng "authors" hoặc bảng trống.</p></div>';
        } else {
            $count_success = 0;
            $count_exist = 0;
            $errors = [];

            foreach ($authors as $author) {
                // --- XỬ LÝ TÊN VÀ HỌC VỊ ---
                // Tách chuỗi dựa trên dấu phẩy đầu tiên
                // Ví dụ: "David Cosio, Ph.D., ABPP" -> ["David Cosio", " Ph.D., ABPP"]
                $name_parts = explode(',', $author->name, 2);
                
                $real_name = trim($name_parts[0]); // Tên thật: David Cosio
                $degrees   = isset($name_parts[1]) ? trim($name_parts[1]) : ''; // Học vị: Ph.D., ABPP

                // --- TẠO USERNAME ---
                // Chuyển về chữ thường, bỏ ký tự đặc biệt, thay khoảng trắng bằng _
                $clean_name_for_user = strtolower($real_name);
                // Xóa các ký tự không phải chữ và số (giữ lại khoảng trắng để replace sau)
                $clean_name_for_user = preg_replace('/[^a-z0-9\s]/', '', $clean_name_for_user);
                $clean_name_for_user = str_replace(' ', '_', $clean_name_for_user);
                // Xử lý nhiều dấu _ liền nhau nếu có
                $clean_name_for_user = preg_replace('/_+/', '_', $clean_name_for_user);
                
                $username = $clean_name_for_user . '_instructor';
                $email    = $username . '@cecinstructor.com';

                //author stable ID
                $author_stable_id= $author->author_stable_id ? $author->author_stable_id : '';

                // --- TÁCH FIRST NAME / LAST NAME ---
                $full_name_arr = explode(' ', $real_name);
                $last_name  = array_pop($full_name_arr); // Lấy từ cuối làm Last Name
                $first_name = implode(' ', $full_name_arr); // Các từ còn lại là First Name
                
                // Nếu tên chỉ có 1 từ (ít gặp nhưng đề phòng)
                if (empty($first_name)) {
                    $first_name = $last_name;
                    $last_name = '';
                }

                // --- KIỂM TRA USER TỒN TẠI ---
                if (username_exists($username) || email_exists($email)) {
                    $count_exist++;
                    // Nếu muốn cập nhật user cũ thì viết code update ở đây. 
                    // Hiện tại tôi sẽ bỏ qua để tránh trùng lặp.
                    continue; 
                }

                // --- TẠO USER MỚI ---
                $user_data = array(
                    'user_login'    => $username,
                    'user_email'    => $email,
                    'user_pass'     => wp_generate_password(), // Tạo pass ngẫu nhiên
                    'first_name'    => $first_name,
                    'last_name'     => $last_name,
                    'display_name'  => $real_name,
                    'role'          => 'instructor', // Role yêu cầu
                );

                $user_id = wp_insert_user($user_data);

                if (!is_wp_error($user_id)) {
                    // --- CẬP NHẬT USER META ---
                    
                    // 1. Website
                    if (!empty($author->website)) {
                        update_user_meta($user_id, 'llms_instructor_website', $author->website);
                    }

                    // 2. Photo Path (Cover Image)
                    if (!empty($author->photo_path)) {
                        update_user_meta($user_id, 'llms_instructor_cover_img', $author->photo_path);
                    }

                    // 3. Học vị (Degrees/Certs)
                    if (!empty($degrees)) {
                        update_user_meta($user_id, 'llms_degrees_certs', $degrees);
                    }

                    // 4. Bio (Description HTML)
                    if (!empty($author->description_html)) {
                        update_user_meta($user_id, 'llms_instructor_bio', $author->description_html);
                    }

                    //update author stable Id để migrate bảng khác cần author id
                    update_user_meta($user_id,'author_stable_id',$author_stable_id);
                                        
                    // Lưu ID cũ để đối chiếu nếu cần
                    update_user_meta($user_id, '_original_author_id', $author->id);

                    $count_success++;
                } else {
                    $errors[] = "Lỗi tạo user ($username): " . $user_id->get_error_message();
                }
            }

            $message = '<div class="notice notice-success"><p>Đã xử lý xong!</p>';
            $message .= "<ul>";
            $message .= "<li>Thêm mới thành công: <strong>$count_success</strong></li>";
            $message .= "<li>Đã tồn tại (bỏ qua): <strong>$count_exist</strong></li>";
            $message .= "</ul></div>";

            if (!empty($errors)) {
                $message .= '<div class="notice notice-warning"><p>Chi tiết lỗi:</p><ul>';
                foreach($errors as $err) {
                    $message .= "<li>$err</li>";
                }
                $message .= '</ul></div>';
            }
        }
    }

    // Render HTML Form
    ?>
    <div class="wrap">
        <h1>Import Instructors từ bảng DB 'authors'</h1>
        
        <?php echo $message; ?>

        <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
            <h3>Hướng dẫn:</h3>
            <p>Tool này sẽ lấy dữ liệu từ bảng <code>authors</code> trong database hiện tại và tạo user WordPress.</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong>Role:</strong> instructor</li>
                <li><strong>Username:</strong> edward_abramson_instructor (ví dụ)</li>
                <li><strong>Email:</strong> username@cecinstructor.com</li>
                <li><strong>Meta:</strong> Website, Bio, Photo, Degrees được map tự động.</li>
            </ul>
            <p style="color: red;">Lưu ý: Nên backup database trước khi chạy để đảm bảo an toàn.</p>
            
            <form method="post" action="">
                <?php wp_nonce_field('cec_import_action', 'cec_import_nonce'); ?>
                <p class="submit">
                    <input type="submit" name="cec_run_import" id="submit" class="button button-primary" value="Bắt đầu Import Users">
                </p>
            </form>
        </div>
    </div>
    <?php
}
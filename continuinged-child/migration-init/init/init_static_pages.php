<?php
// Thêm menu mới vào admin
add_action('admin_menu', 'custom_static_pages_menu');

function custom_static_pages_menu() {


    add_submenu_page(
            'tools.php',                  // Parent menu: Tools (Công cụ)
            'Create Static Pages & Template',         // Tiêu đề trang
            'Create Static Pages & Template',         // Tên hiển thị trong menu
            'manage_options',             // Quyền yêu cầu
            'init-static-pages',     // Slug của trang
            'custom_static_pages_page',      // Hàm render nội dung trang
            25
        );
}

function custom_static_pages_page() {

    if (!current_user_can('manage_options')) {
        wp_die('you dont have permission to access this site.');
    }


    if (isset($_POST['init_static_pages']) && wp_verify_nonce($_POST['init_pages_nonce'], 'init_pages_action')) {
        $created_count = create_custom_static_pages();
        echo '<div class="updated"><p>Created the ' . $created_count . ' static pages!</p></div>';
    }
    ?>

    <div class="wrap">
        <h1>Init Static Pages</h1>
        <form method="post">
            <?php wp_nonce_field('init_pages_action', 'init_pages_nonce'); ?>
            
            <p><strong>Please notice that:</strong> the system will check the slug page, if it already exists, it will not be created</p>
            <p>
                <input type="submit" name="init_static_pages" class="button button-primary" value="Init Pages">
            </p>
        </form>
    </div>

    <?php
}

function create_custom_static_pages() {
    // Danh sách các page cần tạo: title | slug | shortcode | template_file_name
    // template_file_name là tên file trong thư mục templates-page, ví dụ: page-quiz.php
    $pages = array(
        // Các page cũ (giữ nguyên, không có template đặc biệt)
        array('Links',          'links',        '[custom_static_links]',      null),
        array('Links to Us',    'links-to-us',  '[custom_static_linktous]',   null),
        array('Policies',       'policies',     '[custom_static_policies]',   null),
        array('Rewards',        'rewards',      '[custom_static_rewards]',    null),
        array('Approvals',      'approvals',    '[custom_static_approvals]',  null),

        // ================== CÁC PAGE MỚI YÊU CẦU THÊM ==================
        array('Quiz',                   'quiz',                 '',                     'page-quiz.php'),                    // Quiz page
        array('Contact Us & Help',       'contact-us-help',      '',                     'page-contact-us-help.php'),         // Contact us & help
        array('Home',                    'home',                 '',                     'page-home-custom.php'),             // Home page - full width custom
        array('Login',                   'login',                '',                     'page-login.php'),                   // Login page
        array('Purchase Certificate',    'purchase-certificate', '',                     'page-purchase-certificate.php'),    // Purchase certificate
        array('Author List',             'author-list',          '',                     'page-author-list.php'),             // Author list
        array('Customer Account',         'customer-account',              '',             'page-account.php'),                 // Account customer
        array('Forgot Password',         'forgot-password',      '',                     'page-forgot-password.php'),         // Forgot password
        array('Quiz Test',               'quiz-test',            '',                     'page-quiz-test.php'),               // Bonus nếu bạn có
        array('Reset Password',          'reset-password',       '',                     'page-reset-password.php'),          // Reset password (nếu cần)        
        array('Course List',            'course-list',       '',                     'page-course-listing.php'),              // Course Listing Page
    );

    $created = 0;

    foreach ($pages as $page) {
        $title       = $page[0];
        $slug        = $page[1];
        $content     = $page[2];                    // shortcode hoặc nội dung
        $template    = isset($page[3]) ? $page[3] : null;  // tên file template (có thể null)

        // Kiểm tra xem page đã tồn tại chưa (theo slug)
        if (!get_page_by_path($slug)) {
            $page_data = array(
                'post_title'     => $title,
                'post_name'      => $slug,
                'post_content'   => $content,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => get_current_user_id(),
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            );

            $page_id = wp_insert_post($page_data);

            if ($page_id && !is_wp_error($page_id)) {
                $created++;

                // Nếu có template thì gán Page Template
                if ($template) {
                    update_post_meta($page_id, '_wp_page_template','template-pages/'.$template);
                }

                // Đặc biệt: set trang Home làm trang chủ (nếu slug là 'home')
                if ($slug === 'home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $page_id);
                }
            }
        } else {
            // Nếu page đã tồn tại, vẫn cố gắng cập nhật template (phòng trường hợp quên gán trước đó)
            $existing_page = get_page_by_path($slug);
            if ($existing_page && $template) {
                update_post_meta($existing_page->ID, '_wp_page_template','template-pages/'. $template);
            }
            // Cũng cập nhật làm trang chủ nếu là home (an toàn khi chạy lại)
            if ($slug === 'home') {
                update_option('show_on_front', 'page');
                update_option('page_on_front', $existing_page->ID);
            }
        }
    }

    return $created;
}
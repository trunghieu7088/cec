<?php
// 1. Thêm menu con vào Tools
add_action('admin_menu', 'add_create_header_menu_tool');

function add_create_header_menu_tool() {
    add_submenu_page(
        'tools.php',                          // Parent slug
        'Create Header Menu',                 // Page title
        'Create Header Menu',                 // Menu title
        'manage_options',                     // Capability
        'create-header-menu',                 // Menu slug
        'render_create_header_menu_page'      // Callback function
    );
}

// 2. Nội dung trang tool
function render_create_header_menu_page() {
    // Kiểm tra quyền
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Xử lý khi submit form
    if (isset($_POST['create_header_menu_nonce']) && wp_verify_nonce($_POST['create_header_menu_nonce'], 'create_header_menu_action')) {
        create_main_header_menu();
        echo '<div class="updated"><p><strong>Header menu "main-header-menu" has been created successfully !</strong></p></div>';
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Create Header Menu'); ?></h1>
        <p>Click the below buttons to create header menu witht the items: Home, Courses, Approvals, Contact Us & Help.</p>
        
        <form method="post">
            <?php wp_nonce_field('create_header_menu_action', 'create_header_menu_nonce'); ?>
            <p>
                <input type="submit" class="button button-primary" value="Create Header menu" onclick="return confirm('Are you sure want to create header menu ?');">
            </p>
        </form>
    </div>
    <?php
}

// 3. Hàm chính tạo menu
function create_main_header_menu() {
    $menu_name     = 'main-header-menu';
    $menu_location = 'primary'; // Thay đổi nếu theme bạn dùng tên location khác

    // Kiểm tra menu đã tồn tại chưa
    $menu_exists = wp_get_nav_menu_object($menu_name);

    // Nếu chưa tồn tại thì tạo mới
    if (!$menu_exists) {
        $menu_id = wp_create_nav_menu($menu_name);

        // Danh sách các page cần tạo/thêm
        $pages = array(
            'Home'          => home_url('/'),
            'Courses'       => '',
            'Approvals'     => '',
            'Contact Us & Help' => '',
        );

        foreach ($pages as $title => $url) {
            // Kiểm tra page đã tồn tại chưa (theo title)
            $page = get_page_by_title_safe($title);

            if (!$page) {
                // Tạo page mới
                $page_data = array(
                    'post_title'   => $title,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => sanitize_title($title),
                    'post_content' => '',
                );
                $page_id = wp_insert_post($page_data);

                // Nếu là Home thì set làm trang chủ (static front page)
                if ($title === 'Home') {
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $page_id);
                }
            } else {
                $page_id = $page->ID;
            }

            // Thêm vào menu
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title'   => $title,
                'menu-item-url'     => $url ? $url : get_permalink($page_id),
                'menu-item-status'  => 'publish',
                'menu-item-type'    => 'post_type',
                'menu-item-object'  => 'page',
                'menu-item-object-id' => $page_id,
            ));
        }

        // Gán menu vào location "primary"
        $locations = get_theme_mod('nav_menu_locations');
        $locations[$menu_location] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

function get_page_by_title_safe($page_title) {
    $query = new WP_Query(array(
        'post_type'              => 'page',
        'title'                  => $page_title,
        'posts_per_page'         => 1,
        'post_status'            => 'publish', // chỉ lấy trang đã publish
        'no_found_rows'          => true,
        'ignore_sticky_posts'    => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
    ));

    return !empty($query->posts) ? $query->posts[0] : false;
}
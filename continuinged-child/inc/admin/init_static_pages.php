<?php
// Thêm menu mới vào admin
add_action('admin_menu', 'custom_static_pages_menu');

function custom_static_pages_menu() {
    add_menu_page(
        'Init Static Pages',        // Page title
        'Init Static Pages',        // Menu title
        'manage_options',           // Capability (chỉ admin)
        'init-static-pages',        // Menu slug
        'custom_static_pages_page', // Callback function
        'dashicons-admin-page',     // Icon
        100                         // Position
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
    // Danh sách các page cần tạo: title | slug | shortcode
    $pages = array(
        array('Links',          'links',        'custom_static_links'),
        array('Links to Us',    'links-to-us',  'custom_static_linktous'),
        array('Policies',       'policies',     'custom_static_policies'),
        array('Rewards',        'rewards',      'custom_static_rewards'),
        array('Approvals',      'approvals',    'custom_static_approvals'),
    );

    $created = 0;

    foreach ($pages as $page) {
        $title     = $page[0];
        $slug      = $page[1];
        $shortcode = '[' . $page[2] . ']';

        // Kiểm tra xem page đã tồn tại chưa (theo slug)
        if (!get_page_by_path($slug)) {
            $page_data = array(
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_content' => $shortcode,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
                'comment_status' => 'closed',
                'ping_status'   => 'closed',
            );

            $page_id = wp_insert_post($page_data);

            if ($page_id && !is_wp_error($page_id)) {
                $created++;
            }
        }
    }

    return $created;
}
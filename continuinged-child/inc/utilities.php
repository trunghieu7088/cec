<?php
function get_llms_states() {    
    $states = apply_filters( 'lifterlms_states', include( LLMS_PLUGIN_DIR . 'languages/states.php' ) );
    return $states;
}

function get_llms_states_by_country( $country_code = 'US' ) {
    $all_states = get_llms_states();
    return isset( $all_states[ $country_code ] ) ? $all_states[ $country_code ] : array();
}


function get_custom_page_url_by_template( $template_filename, $return_type = 'url' ) {
    // Chuẩn hóa đường dẫn template để chắc chắn đúng format WordPress lưu
    $template_path = 'template-pages/' . ltrim( $template_filename, '/' );
    
    // Cache kết quả để tăng tốc (vì hàm này hay được gọi nhiều lần)
    $cache_key = 'page_by_template_' . md5( $template_path . $return_type );
    $cached    = wp_cache_get( $cache_key, 'custom_pages' );
    
    if ( false !== $cached ) {
        return $cached;
    }

    $args = array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'key'   => '_wp_page_template',
                'value' => $template_path,
            ),
        ),
        'fields'         => 'ids', // Chỉ lấy ID để tối ưu
    );

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        wp_cache_set( $cache_key, false, 'custom_pages', HOUR_IN_SECONDS );
        return false;
    }

    $page_id = $query->posts[0];

    $result = ( $return_type === 'slug' )
        ? get_post_field( 'post_name', $page_id )
        : get_permalink( $page_id );

    // Cache kết quả 1 tiếng (hoặc lâu hơn nếu muốn)
    wp_cache_set( $cache_key, $result, 'custom_pages', HOUR_IN_SECONDS );

    return $result;
}

function cec_get_latest_certificate_id($order_by = 'ID') {
    $args = [
        'post_type'      => 'llms_certificate',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => ($order_by === 'date') ? 'date' : 'ID',
        'order'          => 'DESC',
        'fields'         => 'ids', // Chỉ trả về ID để tối ưu
    ];

    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        return $query->posts[0];
    }

    return false; // Không tìm thấy
}


function get_currency_of_llms() {
    $currency_instance= array('code'=>'USD','sign'=>'$');
    $currency_code = get_lifterlms_currency();
    if($currency_code)
    {
        $currency_instance['code']=$currency_code;
        $currency_instance['sign']= get_lifterlms_currency_symbol( $currency_code);         
    }
 
    return $currency_instance;
}


/**
 * Lấy tên của menu được gán cho một vị trí theme location cụ thể.
 *
 * @param string $location_slug Slug của vị trí menu (ví dụ: 'primary', 'footer').
 * @return string Tên menu hoặc chuỗi rỗng nếu không tìm thấy.
 */
function get_primary_menu_name_cec( $location_slug = 'primary' ) {
    // 1. Lấy tất cả các vị trí menu đã đăng ký
    $locations = get_nav_menu_locations();

    // 2. Kiểm tra xem vị trí yêu cầu có được gán menu nào không
    if ( isset( $locations[ $location_slug ] ) ) {
        
        // 3. Lấy ID của menu
        $menu_id = $locations[ $location_slug ];
        
        // 4. Lấy đối tượng menu
        $menu_object = wp_get_nav_menu_object( $menu_id );
        
        // 5. Kiểm tra và trả về tên menu
        if ( $menu_object && ! is_wp_error( $menu_object ) ) {
            return $menu_object->name;
        }
    }

    // Trả về chuỗi rỗng nếu không có menu nào được gán hoặc tìm thấy
    return '';
}

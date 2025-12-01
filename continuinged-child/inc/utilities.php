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
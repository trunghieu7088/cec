<?php
function check_valid_coupon_code_to_apply_plan($course_id,$coupon_code)
{
    $validation=false;
    $product = new LLMS_Product( $course_id );
        $plans = $product->get_access_plans();
        if (empty($plans)) {
            wp_send_json_error(array(
                'message' => 'No access plan found for this course.'
            ));
        }
        
        $plan_id = $plans[0]->get('id'); // Get first plan ID
        if($plan_id)
        {
            $coupon_info= get_llms_coupon_by_title_exact( $coupon_code );
            if($coupon_info)
            {
                $coupon = new LLMS_Coupon( $coupon_info->ID );
                if($coupon->is_valid( $plan_id ))
                {
                    $validation=true;
                }
            }
        }
    return $validation;
}


/**
 * Lấy thông tin coupon LLMS chính xác theo post_title (không cache, không filter thừa)
 *
 * @param string $coupon_title Tiêu đề coupon cần tìm (exact match)
 * @return WP_Post|bool         Trả về object WP_Post nếu tìm thấy, false nếu không
 */
function get_llms_coupon_by_title_exact( $coupon_title ) {
    global $wpdb;

    if ( empty( $coupon_title ) ) {
        return false;
    }

    // Ép kiểu string và trim để đảm bảo chính xác
    $coupon_title = trim( (string) $coupon_title );

    // Truy vấn trực tiếp vào database, bỏ qua cache + filter
    $post_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} 
         WHERE post_title = %s 
           AND post_type = 'llms_coupon' 
           AND post_status ='publish'
         LIMIT 1",
        $coupon_title
    ) );

    if ( ! $post_id ) {
        return false;
    }

    // Lấy post object mà KHÔNG dùng cache và loại bỏ filter không cần thiết
    // Tạm thời remove các filter phổ biến có thể can thiệp
    add_filter( 'posts_search', '__return_false', 999 ); // bỏ search filter nếu có
    remove_all_filters( 'posts_where' );
    remove_all_filters( 'posts_join' );
    remove_all_filters( 'posts_orderby' );

    $post = get_post( $post_id, OBJECT, 'raw' ); // 'raw' lấy trực tiếp từ DB, không qua filter

    // Khôi phục lại filter để không ảnh hưởng tới các query khác
    remove_filter( 'posts_search', '__return_false', 999 );

    // Nếu vẫn muốn chắc chắn hơn nữa, có thể clear cache của object cụ thể
    //clean_post_cache( $post_id );

    return $post ? $post : false;
}


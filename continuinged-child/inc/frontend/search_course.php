<?php

function get_all_courses_for_search() {
    // Thời gian bắt đầu để đo performance
    $start_time = microtime(true);
    
    // Đặt thời gian expire siêu dài vì dữ liệu hiếm khi thay đổi
    $transient_key = 'all_courses_search_data';
    $data = get_transient( $transient_key );
    
    // DEBUG: Log xem transient có data không
    error_log('Transient data: ' . ($data === false ? 'FALSE - will rebuild' : 'EXISTS'));

    if ( false === $data ) {
        error_log('Building courses cache...');
        
        $courses = get_posts([
            'post_type'      => 'course',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids', // chỉ lấy ID cho nhẹ
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
        
        error_log('Found ' . count($courses) . ' courses');

        $data = [];
        foreach ( $courses as $post_id ) {
            // Lấy categories của course
            $categories = get_the_terms( $post_id, 'course_cat' );
            $cat_names = [];
            $cat_slugs = [];
            
            if ( $categories && ! is_wp_error( $categories ) ) {
                foreach ( $categories as $cat ) {
                    $cat_names[] = $cat->name;
                    $cat_slugs[] = $cat->slug;
                }
            }
            
            $data[] = [
                'id'            => $post_id,
                'title'         => html_entity_decode( get_the_title( $post_id ) ),
                'url'           => get_permalink( $post_id ),
                'categories'    => $cat_names, // Array tên categories
                'cat_slugs'     => $cat_slugs, // Array slug categories (backup)
                'categories_str' => implode(', ', $cat_names), // String để hiển thị
                // thêm field nào cần search: giá, giảng viên...
                // 'price' => get_field('price', $post_id),
            ];
        }
        
        error_log('Built array with ' . count($data) . ' items');

        // Cache 6 tháng (cũng được), hoặc 1 năm luôn cũng chẳng sao
        $set_result = set_transient( $transient_key, $data, 6 * MONTH_IN_SECONDS );
        error_log('Set transient result: ' . ($set_result ? 'SUCCESS' : 'FAILED'));
    }
    
    $execution_time = round((microtime(true) - $start_time) * 1000, 2);
    error_log('Total execution time: ' . $execution_time . 'ms');
    error_log('Sending ' . count($data) . ' courses to client');
    
    // ← QUAN TRỌNG: Gửi đúng format
    wp_send_json_success([
        'courses' => $data,
        'total' => count($data),
        'execution_time' => $execution_time . 'ms',
        'from_cache' => $data !== false
    ]);
}
add_action('wp_ajax_load_courses_search', 'get_all_courses_for_search');
add_action('wp_ajax_nopriv_load_courses_search', 'get_all_courses_for_search');

/* clear cache de test */
/*
add_action('init',function(){
    delete_transient('all_courses_search_data');

})*/
/* end */
?>
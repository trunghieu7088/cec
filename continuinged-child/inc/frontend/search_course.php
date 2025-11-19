<?php

/**
 * AJAX handler for course search
 */
add_action('wp_ajax_search_courses', 'search_courses_callback');
add_action('wp_ajax_nopriv_search_courses', 'search_courses_callback');

function search_courses_callback() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'search_courses_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    if (empty($search_term)) {
        wp_send_json_error('Empty search term');
        return;
    }
    
    // Query courses
    $args = array(
        'post_type' => 'course',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        's' => $search_term,
        'orderby' => 'relevance',
    );
    
    $query = new WP_Query($args);
    $results = array();
    //$course_instance=my_lifterlms_courses();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // Get excerpt
            $excerpt = get_the_excerpt();
            if (empty($excerpt)) {
                $excerpt = wp_trim_words(get_the_content(), 20, '...');
            }
            //$course=$course_instance->get_single_course_data(get_the_ID());
            $main_content=get_post_meta(get_the_ID(),'_course_main_content',true);
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => wp_trim_words(wp_strip_all_tags($main_content,50,'...')),
                'url' => get_permalink(),
            );
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success($results);
}



/**
 * Thêm rewrite rule cho search
 */
function add_search_rewrite_rules() {
    // Lấy slug của search results page
    $search_page_slug = get_custom_page_url_by_template( 'page-search-results.php', 'slug' );
    
    if ( $search_page_slug ) {
        add_rewrite_rule(
            '^' . $search_page_slug . '/search/([^/]*)/?',
            'index.php?pagename=' . $search_page_slug . '&search_term=$matches[1]',
            'top'
        );
    }
}
add_action( 'init', 'add_search_rewrite_rules' );

/**
 * Đăng ký query var
 */
function register_search_term_query_var( $vars ) {
    $vars[] = 'search_term';
    return $vars;
}
add_filter( 'query_vars', 'register_search_term_query_var' );
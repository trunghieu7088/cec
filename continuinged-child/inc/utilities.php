<?php
//get author list page url
function get_author_list_page_url() {
    $args = array(
        'post_type'  => 'page',
        'meta_query' => array(
            array(
                'key'   => '_wp_page_template',
                'value' => 'template-pages/page-author-list.php', 
            ),
        ),
        'posts_per_page' => 1, 
        'post_status'    => 'publish', 
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $page_url = get_permalink(); 
            wp_reset_postdata();
            return $page_url;
        }
    }

    return false; 
}

//get quiz page url
function get_quiz_page_url() {
    $args = array(
        'post_type'  => 'page',
        'meta_query' => array(
            array(
                'key'   => '_wp_page_template',
                'value' => 'template-pages/page-quiz-test.php', 
            ),
        ),
        'posts_per_page' => 1, 
        'post_status'    => 'publish', 
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $page_url = get_permalink(); 
            wp_reset_postdata();
            return $page_url;
        }
    }

    return false; 
}

//get purchase certificate page url
function get_purchase_certificate_page_url() {
    $args = array(
        'post_type'  => 'page',
        'meta_query' => array(
            array(
                'key'   => '_wp_page_template',
                'value' => 'template-pages/page-purchase-certificate.php', 
            ),
        ),
        'posts_per_page' => 1, 
        'post_status'    => 'publish', 
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $page_url = get_permalink(); 
            wp_reset_postdata();
            return $page_url;
        }
    }

    return false; 
}

//get login page url
function get_login_page_url($return_type = 'url') {
    $args = array(
        'post_type'      => 'page',
        'meta_query'     => array(
            array(
                'key'   => '_wp_page_template',
                'value' => 'template-pages/page-login.php',
            ),
        ),
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0];
        wp_reset_postdata();

        if ($return_type === 'slug') {
            return get_post_field('post_name', $post);
        }
        return get_permalink($post);
    }

    return false;
}

function get_llms_states() {
    // Sử dụng filter để lấy mảng states (tương tự cách LifterLMS load)
    $states = apply_filters( 'lifterlms_states', include( LLMS_PLUGIN_DIR . 'languages/states.php' ) );
    return $states;
}

function get_llms_states_by_country( $country_code = 'US' ) {
    $all_states = get_llms_states();
    return isset( $all_states[ $country_code ] ) ? $all_states[ $country_code ] : array();
}


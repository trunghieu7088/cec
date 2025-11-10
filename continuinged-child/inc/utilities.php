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
<?php
/**
 * Continuinged Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Continuinged Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );


// Include custom PHP files & asset files
require_once get_stylesheet_directory() . '/import_assets.php';
require_once get_stylesheet_directory() . '/inc/frontend/frontend_logic.php';
require_once get_stylesheet_directory() . '/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/admin/settings.php';
require_once get_stylesheet_directory() . '/inc/llms/custom-llms.php';
require_once get_stylesheet_directory() . '/inc/utilities.php';
require_once get_stylesheet_directory() . '/inc/admin/init_db.php';
require_once get_stylesheet_directory() . '/inc/admin/init_ce.php';
require_once get_stylesheet_directory() . '/inc/admin/init_static_pages.php';


function astra_child_setup() {
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'astra-child' ),
        )
    );
}
add_action( 'after_setup_theme', 'astra_child_setup' );

//rewrite rule for quiz page
function add_course_slug_query_var($vars) {
    $vars[] = 'course_slug';
    return $vars;
}
add_filter('query_vars', 'add_course_slug_query_var');

function add_course_slug_rewrite_rule() {    
    add_rewrite_rule(
        '^quiz-page/([^/]+)/?$',
        'index.php?pagename=quiz-page&course_slug=$matches[1]',
        'top'
    );
}
add_action('init', 'add_course_slug_rewrite_rule');


function flush_rewrite_rules_on_theme_activation() {
    add_course_slug_rewrite_rule();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_rewrite_rules_on_theme_activation');



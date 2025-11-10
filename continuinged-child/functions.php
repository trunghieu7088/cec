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

// Enqueue styles and scripts for frontend
function astra_child_enqueue_assets() {
    // Enqueue parent theme style
   // wp_enqueue_style( 'continuinged-parent-style', get_template_directory_uri() . '/style.css' );

   // Bootstrap CSS
    wp_enqueue_style( 'bootstrap-base', get_stylesheet_directory_uri() . '/assets/css/bootstrap/bootstrap5.min.css', array(), '5.3.2' );

     // Bootstrap Icons
    wp_enqueue_style( 'bootstrap-icons', get_stylesheet_directory_uri() . '/assets/css/bootstrap/bootstrap-icons.min.css', array(), '1.11.1' );

    // Bootstrap JS
    wp_enqueue_script( 'bootstrap-js-base', get_stylesheet_directory_uri() . '/assets/js/bootstrap/bootstrap.bundle.min.js', array('jquery'), '5.3.2', true );
    
    //css for quiz test page
    if (is_page_template('template-pages/page-quiz-test.php')) {
       
        wp_enqueue_style(
            'quiz-test-style',
            get_stylesheet_directory_uri() . '/assets/css/quiz-page.css',
            array(), 
            '1.0.0', 
            'all'
        );
        wp_enqueue_script( 'cec-quiz-test-js', get_stylesheet_directory_uri() . '/assets/js/quiz-test.js', array( 'jquery' ), '1.0.0', true );
    }

    // contact us assets
    if (is_page_template('template-pages/page-contact-us-help.php')) {
        wp_enqueue_script( 'cec-contact-us-js', get_stylesheet_directory_uri() . '/assets/js/contact-us.js', array( 'jquery' ), '1.0.0', true );        
        
        wp_localize_script('cec-contact-us-js', 'contactAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('contact_form_nonce')
        ));
    }
    //end contact

    // Enqueue child frontend CSS
    wp_enqueue_style( 'continuinged-child-frontend', get_stylesheet_directory_uri() . '/assets/css/frontend.css', array(), '1.0.0' );

    // css from admin
    $primary_color = get_theme_mod('primary_color', '#2c5f7c');
    $secondary_color = get_theme_mod('secondary_color', '#4a90af');
    $accent_color = get_theme_mod('accent_color', '#f8b739');
    $dark_bg = get_theme_mod('dark_bg', '#1a3a4d');
    $light_bg = get_theme_mod('light_bg', '#f8f9fa');
    $text_dark = get_theme_mod('text_dark', '#333');
    $text_light = get_theme_mod('text_light', '#666');

    $custom_css = "
        :root {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
            --accent-color: {$accent_color};
            --dark-bg: {$dark_bg};
            --light-bg: {$light_bg};
            --text-dark: {$text_dark};
            --text-light: {$text_light};
        }
    ";

    wp_add_inline_style('continuinged-child-frontend', $custom_css);
    //end css from admin

    // Enqueue child frontend JS
    wp_enqueue_script( 'continuinged-child-frontend-js', get_stylesheet_directory_uri() . '/assets/js/frontend.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_assets',99 );

// Enqueue admin styles and scripts
function astra_child_enqueue_admin_assets() {
    // Enqueue child admin CSS
    wp_enqueue_style( 'astra-child-admin', get_stylesheet_directory_uri() . '/assets/css/admin.css', array(), '1.0.0' );

    // Enqueue child admin JS
    wp_enqueue_script( 'astra-child-admin-js', get_stylesheet_directory_uri() . '/assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'astra_child_enqueue_admin_assets' );

// Include custom PHP files from inc/

require_once get_stylesheet_directory() . '/inc/frontend/shortcodes.php';
require_once get_stylesheet_directory() . '/inc/admin/settings.php';
require_once get_stylesheet_directory() . '/inc/llms/custom-llms.php';
require_once get_stylesheet_directory() . '/inc/utilities.php';

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

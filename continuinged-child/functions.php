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
    
    // Enqueue child frontend CSS
    wp_enqueue_style( 'continuinged-child-frontend', get_stylesheet_directory_uri() . '/assets/css/frontend.css', array(), '1.0.0' );

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

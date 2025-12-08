<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
<?php astra_head_top(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">

<?php wp_head(); ?>
<?php astra_head_bottom(); ?>
</head>

<body <?php body_class(); ?>>
<?php astra_body_top(); ?>
<?php wp_body_open(); ?>

<div id="page" class="hfeed site top-div-page">
	<a class="skip-link screen-reader-text" href="#content"><?php echo esc_html__( 'Skip to content', 'astra-child' ); ?></a>

	<?php astra_header_before(); ?>

	  <!-- Top Bar Header -->
    <div class="top-bar-header">
		<div class="top-bar-header-component container d-flex align-items-center justify-content-between">
			<div class="main-logo-wrapper">
			<img src="<?php echo get_stylesheet_directory_uri().'/assets/images/true-site-logo.png'; ?>" class="cec-main-logo" alt="Main Logo">
			</div>
        	<p>Courses for Mental Health Professionals</p>
		</div>		
    </div>


	<!-- Custom Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark top-navbar">
		<div class="container">
			<!--<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php 
				$custom_logo_id = get_theme_mod( 'custom_logo' );		
				if($custom_logo_id)		
				{
					$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
					if($logo_url): ?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
					<?php endif;
				}
				bloginfo( 'name' ); 
				?>
			</a> -->
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-center" id="navbarNav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav',
						'fallback_cb'    => '__return_false',
						'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'depth'          => 2,
						'walker'         => new class extends Walker_Nav_Menu {
							function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
								$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
								$classes = empty( $item->classes ) ? array() : (array) $item->classes;
								$classes[] = 'nav-item';
								$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
								$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
								
								$output .= $indent . '<li' . $class_names . '>';
								
								$atts = array();
								$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
								$atts['target'] = ! empty( $item->target ) ? $item->target : '';
								$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
								$atts['href']   = ! empty( $item->url ) ? $item->url : '';
								$atts['class']  = 'nav-link';
								
								$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
								
								$attributes = '';
								foreach ( $atts as $attr => $value ) {
									if ( ! empty( $value ) ) {
										$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
										$attributes .= ' ' . $attr . '="' . $value . '"';
									}
								}
								
								$item_output = $args->before;
								$item_output .= '<a' . $attributes . '>';
								$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
								$item_output .= '</a>';
								$item_output .= $args->after;
								
								$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
							}
						},
					)
				);
				?>
				<!-- search icon -->
				<li class="nav-item search-icon-wrapper">
					<span class="search-icon" data-bs-toggle="modal" data-bs-target="#searchModal">
						<i class="bi bi-search"></i>
					</span>
				</li>
			</div>
		</div>
	</nav>

	<?php //astra_header_after(); ?>

	<?php //astra_content_before(); ?>
	<?php get_template_part( 'templates/search', 'modal' ); ?>
	<div class="main-content">
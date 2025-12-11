<?php
/**
 * Template Name: Full Width Custom Template
 * Template Post Type: page
 * Description: A responsive, full-width template utilizing the standard WordPress Loop and core theme functions for maximum compatibility and maintainability.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<?php echo do_shortcode('[custom_hero_section]'); ?>
<?php echo do_shortcode('[rewards_section]'); ?>
<?php echo do_shortcode('[accreditation_section]'); ?>+

<?php
$plan = llms_get_post( 69 );
if ( $plan && method_exists( $plan, 'validate' ) ) {
    $validation = $plan->validate();
    if ( is_wp_error( $validation ) ) {
        error_log( 'Plan validation errors: ' . print_r( $validation->get_error_messages(), true ) );
    }
}

// Hoặc kiểm tra tất cả meta
$all_meta = get_post_meta( $plan_id );
print_r( $all_meta, true );
error_log( 'Plan meta: ' . print_r( $all_meta, true ) );
?>
<?php
get_footer();
?>
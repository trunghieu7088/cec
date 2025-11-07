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
<?php echo do_shortcode('[ce_testimonial_section]'); ?>
<?php echo do_shortcode('[llms_course_list_custom limit="-1"]'); ?>


<?php
get_footer();
?>
<?php
/**
 * Template Name: North Carolina Course Listing Page
 * Template Post Type: page
 * Description: A responsive, full-width template utilizing the standard WordPress Loop and core theme functions for maximum compatibility and maintainability.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<?php echo do_shortcode('[llms_course_list_custom course_type="ncourse" limit="-1"]'); ?>

<?php
get_footer();
?>
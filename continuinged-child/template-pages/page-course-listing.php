<?php
/**
 * Template Name: Course Listing Page
 * Template Post Type: page
 * Description: A responsive, full-width template utilizing the standard WordPress Loop and core theme functions for maximum compatibility and maintainability.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<?php echo do_shortcode('[llms_course_list_custom limit="-1"]'); ?>
<div class="container mt-4">
	<p>For the North Carolina Board of Licensed Clinical Mental Health Counselors Jurisprudence Exams, click here: <a style="color: rgb(51, 102, 102);" href="<?php echo get_custom_page_url_by_template('page-ncourse-listing.php'); ?>"> NC Jurisprudence Exams</a></p>
</div>
<?php
get_footer();
?>


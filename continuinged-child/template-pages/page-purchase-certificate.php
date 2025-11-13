<?php
/**
 * Template Name: Purchase Certificate Page
 * Template Post Type: page
 * Description: Purchase certificate page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<?php echo do_shortcode('[purchase_certificate]'); ?>

<?php
get_footer();
?>
<?php
/**
 * Template Name: Forgot Password Page
 * Template Post Type: page
 * Description: Forgot password page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<?php echo do_shortcode('[custom_forgot_password_form]'); ?>

<?php
get_footer();
?>
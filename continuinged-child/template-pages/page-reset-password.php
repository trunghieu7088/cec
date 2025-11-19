<?php
/**
 * Template Name: Reset Password Page
 * Template Post Type: page
 * Description: Reset password page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<?php echo do_shortcode('[custom_reset_password_form]'); ?>

<?php
get_footer();
?>
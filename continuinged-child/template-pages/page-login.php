<?php
/**
 * Template Name: Custom Login Page
 * Template Post Type: page
 * Description: Login Page for user.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<?php echo do_shortcode('[custom_login_form]'); ?>

<?php
get_footer();
?>
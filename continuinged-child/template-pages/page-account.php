<?php
/**
 * Template Name: Page Customer Account
 * Template Post Type: page
 * Description: Login Page for user.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>
<?php echo do_shortcode('[customer_account]'); ?>

<?php
get_footer();
?>




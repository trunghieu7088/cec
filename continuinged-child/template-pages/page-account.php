<?php
/**
 * Template Name: Page Customer Account
 * Template Post Type: page
 * Description: Login Page for user.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if(!is_user_logged_in()){
	wp_redirect( get_custom_page_url_by_template('page-login.php') );
	exit;
}
get_header();
?>
<?php echo do_shortcode('[customer_account]'); ?>

<?php
get_footer();
?>




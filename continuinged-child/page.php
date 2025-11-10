<?php
/**
 * Template for displaying a static page
 *
 * @package YourTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
get_header();
?>
<?php
the_content();
?>
<?php
get_footer();
?>
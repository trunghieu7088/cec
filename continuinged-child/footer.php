<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

			<?php //astra_content_bottom(); ?>
		</div><!-- .ast-container -->
	</div><!-- #content -->

	<?php //astra_content_after(); ?>

	<?php //astra_footer_before(); ?>
	
	

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<p><?php echo esc_html( get_theme_mod( 'footer_site_name', get_bloginfo( 'name' ) ) ); ?></p>			
		</div>
	</footer>

	<!-- Scroll to Top Button -->
	<!-- <div class="scroll-top" id="scrollTop">
		<i class="bi bi-arrow-up"></i>
	</div> -->

	<?php //astra_footer_after(); ?>

</div><!-- #page -->

<?php astra_body_bottom(); ?>
<?php wp_footer(); ?>

</body>
</html>
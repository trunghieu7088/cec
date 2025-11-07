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

	<!-- Accreditation Section -->
	<section id="accreditation" class="accreditation-section">
		<div class="container">
			<h2 class="text-center mb-4" style="color: white;">
				<?php echo esc_html( get_theme_mod( 'accreditation_title', 'Our Accreditations' ) ); ?>
			</h2>
			<div class="accreditation-grid">
				<!-- Card 1 -->
				<div class="accreditation-card">
					<?php if ( get_theme_mod( 'accred_card1_image' ) ) : ?>
						<img src="<?php echo esc_url( get_theme_mod( 'accred_card1_image' ) ); ?>" alt="<?php echo esc_attr( get_theme_mod( 'accred_card1_title', 'ASWB Approved' ) ); ?>" class="accred-image">
					<?php else : ?>
						<i class="bi bi-award-fill"></i>
					<?php endif; ?>
					<h5><?php echo esc_html( get_theme_mod( 'accred_card1_title', 'ASWB Approved' ) ); ?></h5>
					<p><?php echo esc_html( get_theme_mod( 'accred_card1_desc', 'ContinuingEdCourses.Net dba SocialWorkCoursesOnline.com, provider #1107, is approved as an ACE provider to offer social work continuing education by the Association of Social Work Boards (ASWB) Approved Continuing Education (ACE) program.' ) ); ?></p>
					<?php if ( get_theme_mod( 'accred_card1_small' ) ) : ?>
						<small><?php echo esc_html( get_theme_mod( 'accred_card1_small', 'Approval period: 3/9/2005-3/9/2027' ) ); ?></small>
					<?php endif; ?>
				</div>
				
				<!-- Card 2 -->
				<div class="accreditation-card">
					<?php if ( get_theme_mod( 'accred_card2_image' ) ) : ?>
						<img src="<?php echo esc_url( get_theme_mod( 'accred_card2_image' ) ); ?>" alt="<?php echo esc_attr( get_theme_mod( 'accred_card2_title', 'NBCC Approved' ) ); ?>" class="accred-image">
					<?php else : ?>
						<i class="bi bi-patch-check-fill"></i>
					<?php endif; ?>
					<h5><?php echo esc_html( get_theme_mod( 'accred_card2_title', 'NBCC Approved' ) ); ?></h5>
					<p><?php echo esc_html( get_theme_mod( 'accred_card2_desc', 'Approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified.' ) ); ?></p>
					<?php if ( get_theme_mod( 'accred_card2_small' ) ) : ?>
						<small><?php echo esc_html( get_theme_mod( 'accred_card2_small' ) ); ?></small>
					<?php endif; ?>
				</div>
				
				<!-- Card 3 -->
				<div class="accreditation-card">
					<?php if ( get_theme_mod( 'accred_card3_image' ) ) : ?>
						<img src="<?php echo esc_url( get_theme_mod( 'accred_card3_image' ) ); ?>" alt="<?php echo esc_attr( get_theme_mod( 'accred_card3_title', 'NYSED-SW Recognized' ) ); ?>" class="accred-image">
					<?php else : ?>
						<i class="bi bi-shield-fill-check"></i>
					<?php endif; ?>
					<h5><?php echo esc_html( get_theme_mod( 'accred_card3_title', 'NYSED-SW Recognized' ) ); ?></h5>
					<p><?php echo esc_html( get_theme_mod( 'accred_card3_desc', 'Recognized by the New York State Education Department\'s State Board for Social Work as an approved provider of continuing education for licensed social workers #SW-0561.' ) ); ?></p>
					<?php if ( get_theme_mod( 'accred_card3_small' ) ) : ?>
						<small><?php echo esc_html( get_theme_mod( 'accred_card3_small' ) ); ?></small>
					<?php endif; ?>
				</div>
				
				<!-- Card 4 -->
				<div class="accreditation-card">
					<?php if ( get_theme_mod( 'accred_card4_image' ) ) : ?>
						<img src="<?php echo esc_url( get_theme_mod( 'accred_card4_image' ) ); ?>" alt="<?php echo esc_attr( get_theme_mod( 'accred_card4_title', 'NYSED-MHC Recognized' ) ); ?>" class="accred-image">
					<?php else : ?>
						<i class="bi bi-check-circle-fill"></i>
					<?php endif; ?>
					<h5><?php echo esc_html( get_theme_mod( 'accred_card4_title', 'NYSED-MHC Recognized' ) ); ?></h5>
					<p><?php echo esc_html( get_theme_mod( 'accred_card4_desc', 'Recognized by the New York State Education Department\'s State Board for Mental Health Practitioners as an approved provider of continuing education for licensed mental health counselors #MHC-0229.' ) ); ?></p>
					<?php if ( get_theme_mod( 'accred_card4_small' ) ) : ?>
						<small><?php echo esc_html( get_theme_mod( 'accred_card4_small' ) ); ?></small>
					<?php endif; ?>
				</div>
			</div>
			<p class="text-center mt-4" style="color: rgba(255,255,255,0.8);">
				<em><?php echo esc_html( get_theme_mod( 'accreditation_disclaimer', 'Although we may provide guidance, it is your responsibility to verify your continuing education requirements with your licensing board.' ) ); ?></em>
			</p>
		</div>
	</section>

	<!-- Contact Section -->
	<section id="contact" class="py-5">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8 text-center">
					<h2 class="section-title">
						<?php echo esc_html( get_theme_mod( 'contact_title', 'Contact Us' ) ); ?>
					</h2>
					<p class="lead mt-4">
						<?php echo esc_html( get_theme_mod( 'contact_description', 'Feel free to contact us if you have any questions.' ) ); ?>
					</p>
					<div class="mt-4">
						<p><i class="bi bi-telephone-fill text-primary"></i> <strong>Phone:</strong> <?php echo esc_html( get_theme_mod( 'contact_phone', '858-842-4100' ) ); ?></p>
						<p><i class="bi bi-envelope-fill text-primary"></i> <strong>Email:</strong> <a href="mailto:<?php echo esc_attr( get_theme_mod( 'contact_email', 'Contact@SocialWorkCoursesOnline.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'contact_email', 'Contact@SocialWorkCoursesOnline.com' ) ); ?></a></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<p>&copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( get_theme_mod( 'footer_site_name', get_bloginfo( 'name' ) ) ); ?>. <?php echo esc_html( get_theme_mod( 'footer_copyright_text', 'All rights reserved.' ) ); ?></p>
			<p><?php echo esc_html( get_theme_mod( 'footer_regulatory_text', 'Regulatory boards are the final authority on courses accepted for continuing education credit.' ) ); ?></p>
		</div>
	</footer>

	<!-- Scroll to Top Button -->
	<div class="scroll-top" id="scrollTop">
		<i class="bi bi-arrow-up"></i>
	</div>

	<?php astra_footer_after(); ?>

</div><!-- #page -->

<?php astra_body_bottom(); ?>
<?php wp_footer(); ?>

</body>
</html>
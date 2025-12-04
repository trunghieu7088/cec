<?php
// [contact_section] Shortcode
function contact_section_shortcode( $atts ) {
    // Cho phép tùy chỉnh ID nếu cần (ví dụ dùng nhiều lần trên 1 trang)
    $atts = shortcode_atts( array(
        'id' => 'contact',
    ), $atts, 'contact_section' );

    ob_start();
    ?>
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
    <?php
    return ob_get_clean();
}
add_shortcode( 'contact_section', 'contact_section_shortcode' );
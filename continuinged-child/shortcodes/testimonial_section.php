<?php 
// Register the testimonial shortcode
function testimonial_section_shortcode() {
    // Get customizer settings
    $testimonial_1_text = get_theme_mod('testimonial_1_text', 'One of the best online courses I have ever taken. Very well done.');
    $testimonial_1_author = get_theme_mod('testimonial_1_author', 'Mark Basinger, LCSW, Muncy, PA');
    $testimonial_2_text = get_theme_mod('testimonial_2_text', 'I like that I can review the material before I decide what course to take. I see that as very generous and user friendly, so thank you.');
    $testimonial_2_author = get_theme_mod('testimonial_2_author', 'Dawne M. Grove, MC, LMHC, Everson, WA');
    $cta_main = get_theme_mod('testimonial_cta_main', 'View our courses for free - no pre-registration required.');
    $cta_sub = get_theme_mod('testimonial_cta_sub', 'You pay only after you pass the test.');

    // Output the testimonial section
    ob_start();
    ?>
    <section class="testimonial-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-text"><?php echo esc_html($testimonial_1_text); ?></div>
                        <div class="testimonial-author">- <?php echo esc_html($testimonial_1_author); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="testimonial-text"><?php echo esc_html($testimonial_2_text); ?></div>
                        <div class="testimonial-author">- <?php echo esc_html($testimonial_2_author); ?></div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="lead"><strong><?php echo esc_html($cta_main); ?></strong></p>
                <p><?php echo esc_html($cta_sub); ?></p>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('ce_testimonial_section', 'testimonial_section_shortcode');

<?php
// Register Hero Section Shortcode
function hero_section_shortcode($atts) {
    // Get Customizer settings
    $badge_text = get_theme_mod('hero_badge_text', 'Nationally Accredited Provider');
    $heading = get_theme_mod('hero_heading', 'Continuing Education Courses Online');
    $lead_text = get_theme_mod('hero_lead_text', 'The highest quality, most up-to-date continuing education courses for licensed social workers, professional counselors, and marriage and family therapists.');
    $step1_title = get_theme_mod('hero_step1_title', 'Take the Course Online');
    $step1_text = get_theme_mod('hero_step1_text', 'No books to buy. No videos to suffer through!');
    $step2_title = get_theme_mod('hero_step2_title', 'Take the Test Online');
    $step2_text = get_theme_mod('hero_step2_text', 'Complete the assessment at your own pace');
    $step3_title = get_theme_mod('hero_step3_title', 'Print Your Certificate');
    $step3_text = get_theme_mod('hero_step3_text', 'Pay only after you pass the test');

    // Build the HTML output
    $output = '
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">' . esc_html($badge_text) . '</div>
                <h1>' . esc_html($heading) . '</h1>
                <p class="lead">' . esc_html($lead_text) . '</p>
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>' . esc_html($step1_title) . '</h4>
                        <p>' . esc_html($step1_text) . '</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>' . esc_html($step2_title) . '</h4>
                        <p>' . esc_html($step2_text) . '</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>' . esc_html($step3_title) . '</h4>
                        <p>' . esc_html($step3_text) . '</p>
                    </div>
                </div>
            </div>
        </div>
    </section>';

    return $output;
}
add_shortcode('custom_hero_section', 'hero_section_shortcode');

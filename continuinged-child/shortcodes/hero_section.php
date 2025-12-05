<?php
// Register Hero Section Shortcode
function hero_section_shortcode($atts) {
    // Get Customizer settings
    $main_title = get_theme_mod('hero_heading', 'Continuing Ed Courses on the Internet');
    
    // Testimonial settings
    $testimonial_text = get_theme_mod('hero_testimonial_text', '"Comprehensive, well written, concise."');
    $testimonial_author = get_theme_mod('hero_testimonial_author', '— Linda Thole, M.Ed., LPC, NCC, Sunset Beach, NC');
    
    // Intro paragraph
    $intro_text = get_theme_mod('hero_lead_text', 'We provide high quality, up-to-date continuing education courses online. As a <a href="accreditation.php">nationally accredited</a> provider, our courses are developed by <a href="authors.php">distinguished authors</a> who are experts in their respective fields. The course material is anchored in established theory and is highly relevant for today\'s mental health professional. These continuing education courses are designed specifically for licensed psychologists, social workers, professional counselors, and marriage and family therapists.');
    
    // Steps section
    $steps_title = get_theme_mod('hero_step_title', 'How It Works');
    $step1_text = get_theme_mod('hero_step1_title', 'Take the course online. (No books to buy. No videos to suffer through!)');
    $step2_text = get_theme_mod('hero_step2_title', 'Take the test online.');
    $step3_text = get_theme_mod('hero_step3_title', 'Pay then print your certificate online.');
    
    // CTA button
    $cta_text = get_theme_mod('hero_cta_text', 'Select a Course');    
    
    // Note box
    $note_text = get_theme_mod('hero_note_text', '<strong>Free to Browse:</strong> You may view our courses for free - no pre-registration is required. (You pay only after you pass the test.)');
    $course_page_url=get_custom_page_url_by_template('page-course-listing.php');
    // Build the HTML output
    $output = '
    <section id="home" class="hero">
        <div class="container">
            <div class="header-section">
                <h1 class="main-title">' . esc_html($main_title) . '</h1>
                
                <div class="testimonial-box">
                    <div class="testimonial-text">' . esc_html($testimonial_text) . '</div>
                    <div class="testimonial-author">' . esc_html($testimonial_author) . '</div>
                </div>
            </div>
            
            <div class="content-card">
                <div class="intro-text">
                    <p>' . wp_kses_post($intro_text) . '</p>
                </div>
                
                <div class="steps-section">
                    <h2 class="section-title">' . esc_html($steps_title) . '</h2>
                    
                    <div class="step-item">
                        <span class="step-number">1</span>
                        <span class="step-text">' . esc_html($step1_text) . '</span>
                    </div>
                    <div class="step-item">
                        <span class="step-number">2</span>
                        <span class="step-text">' . esc_html($step2_text) . '</span>
                    </div>
                    <div class="step-item">
                        <span class="step-number">3</span>
                        <span class="step-text">' . esc_html($step3_text) . '</span>
                    </div>
                </div>
                
                <div class="text-center my-4">
                    <a href="' . $course_page_url. '" class="btn btn-primary btn-lg px-5 py-3" style="background-color: var(--primary-blue); border-color: var(--primary-blue); font-size: 1.2rem; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 15px rgba(51, 102, 102, 0.3); transition: all 0.3s ease;">
                        ' . esc_html($cta_text) . '
                    </a>
                </div>
                
                <div class="note-box">
                    ' . wp_kses_post($note_text) . '
                </div>
            </div>
        </div>
    </section>';

    return $output;
}
add_shortcode('custom_hero_section', 'hero_section_shortcode');
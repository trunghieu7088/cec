<?php
// Add customizer settings for the testimonial section
function testimonial_section_customizer($wp_customize) {
    // Add a section for testimonials
    $wp_customize->add_section('testimonial_section', array(
        'title' => __('Testimonial Section', 'your-theme-textdomain'),
        'priority' => 30,
    ));

    // Testimonial 1 Text
    $wp_customize->add_setting('testimonial_1_text', array(
        'default' => 'One of the best online courses I have ever taken. Very well done.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('testimonial_1_text', array(
        'label' => __('Testimonial 1 Text', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'textarea',
    ));

    // Testimonial 1 Author
    $wp_customize->add_setting('testimonial_1_author', array(
        'default' => 'Mark Basinger, LCSW, Muncy, PA',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonial_1_author', array(
        'label' => __('Testimonial 1 Author', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'text',
    ));

    // Testimonial 2 Text
    $wp_customize->add_setting('testimonial_2_text', array(
        'default' => 'I like that I can review the material before I decide what course to take. I see that as very generous and user friendly, so thank you.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('testimonial_2_text', array(
        'label' => __('Testimonial 2 Text', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'textarea',
    ));

    // Testimonial 2 Author
    $wp_customize->add_setting('testimonial_2_author', array(
        'default' => 'Dawne M. Grove, MC, LMHC, Everson, WA',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonial_2_author', array(
        'label' => __('Testimonial 2 Author', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'text',
    ));

    // CTA Main Text
    $wp_customize->add_setting('testimonial_cta_main', array(
        'default' => 'View our courses for free - no pre-registration required.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonial_cta_main', array(
        'label' => __('Call to Action Main Text', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'text',
    ));

    // CTA Sub Text
    $wp_customize->add_setting('testimonial_cta_sub', array(
        'default' => 'You pay only after you pass the test.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('testimonial_cta_sub', array(
        'label' => __('Call to Action Sub Text', 'your-theme-textdomain'),
        'section' => 'testimonial_section',
        'type' => 'text',
    ));
}
add_action('customize_register', 'testimonial_section_customizer');
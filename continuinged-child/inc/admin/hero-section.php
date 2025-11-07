<?php
// Add Customizer Settings for Hero Section
function hero_section_customizer($wp_customize) {
    // Add a new section for Hero Section
    $wp_customize->add_section('hero_section_settings', array(
        'title' => __('Hero Section', 'your-theme-textdomain'),
        'priority' => 30,
    ));

    // Badge Text
    $wp_customize->add_setting('hero_badge_text', array(
        'default' => 'Nationally Accredited Provider',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_badge_text', array(
        'label' => __('Badge Text', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Heading
    $wp_customize->add_setting('hero_heading', array(
        'default' => 'Continuing Education Courses Online',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_heading', array(
        'label' => __('Heading', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Lead Text
    $wp_customize->add_setting('hero_lead_text', array(
        'default' => 'The highest quality, most up-to-date continuing education courses for licensed social workers, professional counselors, and marriage and family therapists.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('hero_lead_text', array(
        'label' => __('Lead Text', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'textarea',
    ));

    // Step 1 Title
    $wp_customize->add_setting('hero_step1_title', array(
        'default' => 'Take the Course Online',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step1_title', array(
        'label' => __('Step 1 Title', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Step 1 Text
    $wp_customize->add_setting('hero_step1_text', array(
        'default' => 'No books to buy. No videos to suffer through!',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step1_text', array(
        'label' => __('Step 1 Text', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Step 2 Title
    $wp_customize->add_setting('hero_step2_title', array(
        'default' => 'Take the Test Online',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step2_title', array(
        'label' => __('Step 2 Title', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Step 2 Text
    $wp_customize->add_setting('hero_step2_text', array(
        'default' => 'Complete the assessment at your own pace',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step2_text', array(
        'label' => __('Step 2 Text', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Step 3 Title
    $wp_customize->add_setting('hero_step3_title', array(
        'default' => 'Print Your Certificate',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step3_title', array(
        'label' => __('Step 3 Title', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));

    // Step 3 Text
    $wp_customize->add_setting('hero_step3_text', array(
        'default' => 'Pay only after you pass the test',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('hero_step3_text', array(
        'label' => __('Step 3 Text', 'your-theme-textdomain'),
        'section' => 'hero_section_settings',
        'type' => 'text',
    ));
}
add_action('customize_register', 'hero_section_customizer');

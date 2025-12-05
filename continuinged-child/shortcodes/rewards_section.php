<?php
// Add Rewards Section settings to Customizer
function rewards_section_customizer($wp_customize) {
    // Add Rewards Section
    $wp_customize->add_section('rewards_section', array(
        'title'    => __('Rewards Section Settings', 'your-theme'),
        'priority' => 35,
    ));

    // Rewards Section Title
    $wp_customize->add_setting('rewards_title', array(
        'default'           => 'CERewards™ – A Discount Program for Loyal Customers',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_title', array(
        'label'    => __('Section Title', 'your-theme'),
        'section'  => 'rewards_section',
        'type'     => 'text',
    ));

    // Rewards Description Paragraph 1
    $wp_customize->add_setting('rewards_description_1', array(
        'default'           => 'With CERewards, you automatically receive discounts of 5%, 10%, 15%, and more on your courses, based on the number that you have already completed. The discounts are cumulative and automatic, and we include courses that you have completed in years past.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_description_1', array(
        'label'    => __('Description Paragraph 1', 'your-theme'),
        'section'  => 'rewards_section',
        'type'     => 'textarea',
    ));

    // Rewards Description Paragraph 2 - Before Bold Text
    $wp_customize->add_setting('rewards_description_2_before', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_description_2_before', array(
        'label'       => __('Description 2 - Text Before Bold (Optional)', 'your-theme'),
        'section'     => 'rewards_section',
        'type'        => 'text',
        'description' => __('Text that appears before the bold text in paragraph 2', 'your-theme'),
    ));

    // Rewards Description Paragraph 2 - Bold Text
    $wp_customize->add_setting('rewards_description_2_bold', array(
        'default'           => 'Take more courses, earn greater discounts. It\'s automatic.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_description_2_bold', array(
        'label'       => __('Description 2 - Bold Text', 'your-theme'),
        'section'     => 'rewards_section',
        'type'        => 'text',
        'description' => __('This text will be displayed in bold', 'your-theme'),
    ));

    // Rewards Description Paragraph 2 - After Bold Text
    $wp_customize->add_setting('rewards_description_2_after', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_description_2_after', array(
        'label'       => __('Description 2 - Text After Bold (Optional)', 'your-theme'),
        'section'     => 'rewards_section',
        'type'        => 'text',
        'description' => __('Text that appears after the bold text in paragraph 2', 'your-theme'),
    ));

    // Learn More Link Text
    $wp_customize->add_setting('rewards_link_text', array(
        'default'           => 'Learn more now.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('rewards_link_text', array(
        'label'    => __('Learn More Link Text', 'your-theme'),
        'section'  => 'rewards_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'rewards_section_customizer');

// Register Rewards Section Shortcode
function rewards_section_shortcode($atts) {
    // Get Customizer settings
    $title = get_theme_mod('rewards_title', 'CERewards™ – A Discount Program for Loyal Customers');
    $description_1 = get_theme_mod('rewards_description_1', 'With CERewards, you automatically receive discounts of 5%, 10%, 15%, and more on your courses, based on the number that you have already completed. The discounts are cumulative and automatic, and we include courses that you have completed in years past.');
    $description_2_before = get_theme_mod('rewards_description_2_before', '');
    $description_2_bold = get_theme_mod('rewards_description_2_bold', 'Take more courses, earn greater discounts. It\'s automatic.');
    $description_2_after = get_theme_mod('rewards_description_2_after', '');
    $link_text = get_theme_mod('rewards_link_text', 'Learn more now.');
    $rewards_page_url = site_url('rewards');
    // Build paragraph 2
    $paragraph_2 = '';
    if (!empty($description_2_before)) {
        $paragraph_2 .= esc_html($description_2_before) . ' ';
    }
    $paragraph_2 .= '<strong>' . esc_html($description_2_bold) . '</strong>';
    if (!empty($description_2_after)) {
        $paragraph_2 .= ' ' . esc_html($description_2_after);
    }
    $paragraph_2 .= ' <a href="'.$rewards_page_url.'">' . esc_html($link_text) . '</a>';

    // Build the HTML output
    $output = '
    <div class="container mt-2">
    <div class="rewards-section">
        <h2>' . esc_html($title) . '</h2>
        <p>' . esc_html($description_1) . '</p>
        <p>' . $paragraph_2 . '</p>
    </div>
    </div>';

    return $output;
}
add_shortcode('rewards_section', 'rewards_section_shortcode');

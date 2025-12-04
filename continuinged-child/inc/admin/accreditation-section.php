<?php
function accreditation_section_customizer($wp_customize) {
    // Add Accreditation Section
    $wp_customize->add_section('accreditation_section', array(
        'title'    => __('Accreditation Section Settings', 'your-theme'),
        'priority' => 36,
    ));

    // Section Title
    $wp_customize->add_setting('accreditation_section_title', array(
        'default'           => 'Accreditation & Approvals',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_section_title', array(
        'label'    => __('Section Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // APA - Title
    $wp_customize->add_setting('accreditation_apa_title', array(
        'default'           => 'American Psychological Association (APA):',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_apa_title', array(
        'label'    => __('APA - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // APA - Description
    $wp_customize->add_setting('accreditation_apa_description', array(
        'default'           => 'ContinuingEdCourses.Net is approved by the American Psychological Association to sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_apa_description', array(
        'label'    => __('APA - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // ASWB - Title
    $wp_customize->add_setting('accreditation_aswb_title', array(
        'default'           => 'Association of Social Work Boards (ASWB):',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_aswb_title', array(
        'label'    => __('ASWB - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // ASWB - Description
    $wp_customize->add_setting('accreditation_aswb_description', array(
        'default'           => 'ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the Association of Social Work Boards (ASWB) Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 3/9/2005-3/9/2027.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_aswb_description', array(
        'label'    => __('ASWB - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // NBCC - Title
    $wp_customize->add_setting('accreditation_nbcc_title', array(
        'default'           => 'National Board for Certified Counselors (NBCC):',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_nbcc_title', array(
        'label'    => __('NBCC - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // NBCC - Description
    $wp_customize->add_setting('accreditation_nbcc_description', array(
        'default'           => 'ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_nbcc_description', array(
        'label'    => __('NBCC - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // NY Psychology - Title
    $wp_customize->add_setting('accreditation_ny_psy_title', array(
        'default'           => 'New York State - Psychology:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_psy_title', array(
        'label'    => __('NY Psychology - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // NY Psychology - Description
    $wp_customize->add_setting('accreditation_ny_psy_description', array(
        'default'           => 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Psychology (NYSED-PSY) as an approved provider of continuing education for licensed psychologists #PSY-0048.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_psy_description', array(
        'label'    => __('NY Psychology - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // NY Social Work - Title
    $wp_customize->add_setting('accreditation_ny_sw_title', array(
        'default'           => 'New York State - Social Work:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_sw_title', array(
        'label'    => __('NY Social Work - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // NY Social Work - Description
    $wp_customize->add_setting('accreditation_ny_sw_description', array(
        'default'           => 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Social Work (NYSED-SW) as an approved provider of continuing education for licensed social workers #SW-0561.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_sw_description', array(
        'label'    => __('NY Social Work - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // NY Mental Health - Title
    $wp_customize->add_setting('accreditation_ny_mhc_title', array(
        'default'           => 'New York State - Mental Health Counselors:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_mhc_title', array(
        'label'    => __('NY Mental Health - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // NY Mental Health - Description
    $wp_customize->add_setting('accreditation_ny_mhc_description', array(
        'default'           => 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Mental Health Practitioners (NYSED-MHC) as an approved provider of continuing education for licensed mental health counselors #MHC-0229.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_ny_mhc_description', array(
        'label'    => __('NY Mental Health - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'textarea',
    ));

    // Conflict Notice - Title
    $wp_customize->add_setting('accreditation_conflict_title', array(
        'default'           => 'Transparency:',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_conflict_title', array(
        'label'    => __('Conflict Notice - Title', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));

    // Conflict Notice - Description
    $wp_customize->add_setting('accreditation_conflict_description', array(
        'default'           => 'No conflicts of interest have been reported by the authors.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('accreditation_conflict_description', array(
        'label'    => __('Conflict Notice - Description', 'your-theme'),
        'section'  => 'accreditation_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'accreditation_section_customizer');

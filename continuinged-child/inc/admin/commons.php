<?php
//ACCREDITATION SECTION 
//FOOTER
//CONTACT US
function astra_child_footer_customizer( $wp_customize ) {
    
    // ========================================
    // ACCREDITATION SECTION
    // ========================================
    
    $wp_customize->add_section( 'footer_accreditation_section', array(
        'title'    => __( 'Footer Accreditation Section', 'astra-child' ),
        'priority' => 120,
        'panel'    => '', // Leave empty or create a panel if needed
    ) );
    
    // Accreditation Title
    /*
    $wp_customize->add_setting( 'accreditation_title', array(
        'default'           => 'Our Accreditations',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'accreditation_title', array(
        'label'       => __( 'Accreditation Section Title', 'astra-child' ),
        'section'     => 'footer_accreditation_section',
        'type'        => 'text',
    ) );
    
    // Accreditation Disclaimer Text
    $wp_customize->add_setting( 'accreditation_disclaimer', array(
        'default'           => 'Although we may provide guidance, it is your responsibility to verify your continuing education requirements with your licensing board.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'accreditation_disclaimer', array(
        'label'       => __( 'Disclaimer Text', 'astra-child' ),
        'section'     => 'footer_accreditation_section',
        'type'        => 'textarea',
    ) );
    
    // Card 1 - ASWB
        $wp_customize->add_setting( 'accred_card1_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'accred_card1_image', array(
        'label'       => __( 'Card 1 - Image', 'astra-child' ),
        'section'     => 'footer_accreditation_section',
        'settings'    => 'accred_card1_image',
    ) ) );
    
    $wp_customize->add_setting( 'accred_card1_title', array(
        'default'           => 'ASWB Approved',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card1_title', array(
        'label'   => __( 'Card 1 - Title', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    $wp_customize->add_setting( 'accred_card1_desc', array(
        'default'           => 'ContinuingEdCourses.Net dba SocialWorkCoursesOnline.com, provider #1107, is approved as an ACE provider to offer social work continuing education by the Association of Social Work Boards (ASWB) Approved Continuing Education (ACE) program.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'accred_card1_desc', array(
        'label'   => __( 'Card 1 - Description', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'textarea',
    ) );
    
    $wp_customize->add_setting( 'accred_card1_small', array(
        'default'           => 'Approval period: 3/9/2005-3/9/2027',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card1_small', array(
        'label'   => __( 'Card 1 - Small Text', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    // Card 2 - NBCC
    $wp_customize->add_setting( 'accred_card2_image', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'accred_card2_image', array(
        'label'       => __( 'Card 2 - Image', 'astra-child' ),
        'section'     => 'footer_accreditation_section',
        'settings'    => 'accred_card2_image',
    ) ) );
        
    $wp_customize->add_setting( 'accred_card2_title', array(
        'default'           => 'NBCC Approved',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card2_title', array(
        'label'   => __( 'Card 2 - Title', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    $wp_customize->add_setting( 'accred_card2_desc', array(
        'default'           => 'Approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'accred_card2_desc', array(
        'label'   => __( 'Card 2 - Description', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'textarea',
    ) );
    
    $wp_customize->add_setting( 'accred_card2_small', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card2_small', array(
        'label'   => __( 'Card 2 - Small Text', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    // Card 3 - NYSED-SW
   $wp_customize->add_setting( 'accred_card3_image', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
) );
$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'accred_card3_image', array(
    'label'       => __( 'Card 3 - Image', 'astra-child' ),
    'section'     => 'footer_accreditation_section',
    'settings'    => 'accred_card3_image',
) ) );
    
    $wp_customize->add_setting( 'accred_card3_title', array(
        'default'           => 'NYSED-SW Recognized',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card3_title', array(
        'label'   => __( 'Card 3 - Title', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    $wp_customize->add_setting( 'accred_card3_desc', array(
        'default'           => 'Recognized by the New York State Education Department\'s State Board for Social Work as an approved provider of continuing education for licensed social workers #SW-0561.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'accred_card3_desc', array(
        'label'   => __( 'Card 3 - Description', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'textarea',
    ) );
    
    $wp_customize->add_setting( 'accred_card3_small', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card3_small', array(
        'label'   => __( 'Card 3 - Small Text', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    // Card 4 - NYSED-MHC
    $wp_customize->add_setting( 'accred_card4_image', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'accred_card4_image', array(
        'label'       => __( 'Card 4 - Image', 'astra-child' ),
        'section'     => 'footer_accreditation_section',
        'settings'    => 'accred_card4_image',
    ) ) );

    $wp_customize->add_setting( 'accred_card4_title', array(
        'default'           => 'NYSED-MHC Recognized',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card4_title', array(
        'label'   => __( 'Card 4 - Title', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) );
    
    $wp_customize->add_setting( 'accred_card4_desc', array(
        'default'           => 'Recognized by the New York State Education Department\'s State Board for Mental Health Practitioners as an approved provider of continuing education for licensed mental health counselors #MHC-0229.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'accred_card4_desc', array(
        'label'   => __( 'Card 4 - Description', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'textarea',
    ) );
    
    $wp_customize->add_setting( 'accred_card4_small', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'accred_card4_small', array(
        'label'   => __( 'Card 4 - Small Text', 'astra-child' ),
        'section' => 'footer_accreditation_section',
        'type'    => 'text',
    ) ); */
    
    // ========================================
    // CONTACT SECTION
    // ========================================
    
    $wp_customize->add_section( 'footer_contact_section', array(
        'title'    => __( 'Contact Us & Help', 'astra-child' ),
        'priority' => 121,
    ) );
    
    // Contact Title
    $wp_customize->add_setting( 'contact_title', array(
        'default'           => 'Contact Information',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'contact_title', array(
        'label'   => __( 'Contact Section Title', 'astra-child' ),
        'section' => 'footer_contact_section',
        'type'    => 'text',
    ) );

     // Contact address
    $wp_customize->add_setting( 'cec_contact_address', array(
        'default'           => '12842 Francine Ct. Poway, CA 92064',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cec_contact_address', array(
        'label'   => __( 'Address', 'astra-child' ),
        'section' => 'footer_contact_section',
        'type'    => 'textarea',
    ) );
    
    // Contact Description
    $wp_customize->add_setting( 'contact_description', array(
        'default'           => 'Feel free to contact us if you have any questions.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'contact_description', array(
        'label'   => __( 'Contact Description', 'astra-child' ),
        'section' => 'footer_contact_section',
        'type'    => 'textarea',
    ) );
    
    // Phone Number
    $wp_customize->add_setting( 'cec_contact_phone', array(
        'default'           => '858-842-4100',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'cec_contact_phone', array(
        'label'   => __( 'Phone Number', 'astra-child' ),
        'section' => 'footer_contact_section',
        'type'    => 'text',
    ) );
    
    // Email
    $wp_customize->add_setting( 'contact_email', array(
        'default'           => 'Contact@SocialWorkCoursesOnline.com',
        'sanitize_callback' => 'sanitize_email',
    ) );
    $wp_customize->add_control( 'contact_email', array(
        'label'   => __( 'Email Address', 'astra-child' ),
        'section' => 'footer_contact_section',
        'type'    => 'email',
    ) );

        // Site Name for Copyright

         $wp_customize->add_section( 'footer_copyright_section', array(
        'title'    => __( 'Copyright Footer Text', 'astra-child' ),
        'priority' => 121,
    ) );
    

    $wp_customize->add_setting( 'footer_site_name', array(
        'default'           => '© Copyright 2004-2025 by ContinuingEdCourses.Net, Inc. All rights reserved.',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'footer_site_name', array(
        'label'   => __( 'Copyright Text', 'astra-child' ),
        'section' => 'footer_copyright_section',
        'type'    => 'text',
    ) );
    



}
add_action( 'customize_register', 'astra_child_footer_customizer' );
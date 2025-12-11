<?php
function accreditation_section_shortcode($atts) {
    // Get Customizer settings
    $section_title = get_theme_mod('accreditation_section_title', 'Accreditation & Approvals');
    
    // APA
    $apa_title = get_theme_mod('accreditation_apa_title', 'American Psychological Association (APA):');
    $apa_description = get_theme_mod('accreditation_apa_description', 'ContinuingEdCourses.Net is approved by the American Psychological Association to sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.');
    
    // ASWB
    $aswb_title = get_theme_mod('accreditation_aswb_title', 'Association of Social Work Boards (ASWB):');
    $aswb_description = get_theme_mod('accreditation_aswb_description', 'ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the Association of Social Work Boards (ASWB) Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 3/9/2005-3/9/2027.');
    
    // NBCC
    $nbcc_title = get_theme_mod('accreditation_nbcc_title', 'National Board for Certified Counselors (NBCC):');
    $nbcc_description = get_theme_mod('accreditation_nbcc_description', 'ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.');
    
    // NY Psychology
    $ny_psy_title = get_theme_mod('accreditation_ny_psy_title', 'New York State - Psychology:');
    $ny_psy_description = get_theme_mod('accreditation_ny_psy_description', 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Psychology (NYSED-PSY) as an approved provider of continuing education for licensed psychologists #PSY-0048.');
    
    // NY Social Work
    $ny_sw_title = get_theme_mod('accreditation_ny_sw_title', 'New York State - Social Work:');
    $ny_sw_description = get_theme_mod('accreditation_ny_sw_description', 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Social Work (NYSED-SW) as an approved provider of continuing education for licensed social workers #SW-0561.');
    
    // NY Mental Health
    $ny_mhc_title = get_theme_mod('accreditation_ny_mhc_title', 'New York State - Mental Health Counselors:');
    $ny_mhc_description = get_theme_mod('accreditation_ny_mhc_description', 'ContinuingEdCourses.Net is recognized by the New York State Education Department\'s State Board for Mental Health Practitioners (NYSED-MHC) as an approved provider of continuing education for licensed mental health counselors #MHC-0229.');
    
    // Conflict Notice
    $conflict_title = get_theme_mod('accreditation_conflict_title', 'Transparency:');
    $conflict_description = get_theme_mod('accreditation_conflict_description', 'No conflicts of interest have been reported by the authors.');
    $prefix_path=get_stylesheet_directory_uri().'/assets/accreditaion-img/';
    

    // Build the HTML output
    $output = '
    <style>    
.accreditation-logos-flex {
    display: flex;
    flex-wrap: wrap; 
    justify-content: center; 
    gap: 20px; 
    margin-bottom: 30px;
}
.accreditation-logos-flex img
{
    width:150px;
    height:150px;
    border-radius:50%;
}
    </style>
    <div class="container mt-4">
    <div class="content-card">
        <h2 class="section-title">' . esc_html($section_title) . '</h2>
        <div class="accreditation-logos-flex">
             <img src="'. $prefix_path.'apa-new.jpeg'.'" alt="" class="accreditation-logo-img">
             <img src="'. $prefix_path.'aswb-new.png'.'" alt="" class="accreditation-logo-img">
             <img src="'. $prefix_path.'NBCC.png'.'" alt="" class="accreditation-logo-img">
             <img src="'. $prefix_path.'nysed-logo.png'.'" alt="" class="accreditation-logo-img">
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($apa_title) . '</strong> ' . esc_html($apa_description) . '</p>
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($aswb_title) . '</strong> ' . esc_html($aswb_description) . '</p>
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($nbcc_title) . '</strong> ' . esc_html($nbcc_description) . '</p>
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($ny_psy_title) . '</strong> ' . esc_html($ny_psy_description) . '</p>
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($ny_sw_title) . '</strong> ' . esc_html($ny_sw_description) . '</p>
        </div>
        <div class="accreditation-item">
            <p><strong>' . esc_html($ny_mhc_title) . '</strong> ' . esc_html($ny_mhc_description) . '</p>
        </div>
        <div class="conflict-notice">
            <strong>' . esc_html($conflict_title) . '</strong> ' . esc_html($conflict_description) . '
        </div>
    </div>
    </div>';

    return $output;
}
add_shortcode('accreditation_section', 'accreditation_section_shortcode');
<?php
use Dompdf\Dompdf;
use Dompdf\Options;

add_action('wp_ajax_generate_certificate_pdf', 'generate_certificate_pdf_handler');
add_action('wp_ajax_nopriv_generate_certificate_pdf', 'generate_certificate_pdf_handler');

function generate_certificate_pdf_handler() {
    // Verify nonce for security
    check_ajax_referer('certificate_pdf_nonce', 'nonce');
    
    if (!isset($_POST['certificate_id'])) {
        wp_send_json_error(['message' => 'Certificate ID is required']);
    }
    
    $certificate_id = intval($_POST['certificate_id']);
    $pre_certificate = get_post($certificate_id);
    
    if (!$pre_certificate || $pre_certificate->post_type != 'llms_my_certificate') {
        wp_send_json_error(['message' => 'Invalid certificate']);
    }
    
    try {
        // Generate PDF
        $pdf_path = generate_certificate_pdf($certificate_id);
        
        if ($pdf_path) {
            // Return success with file path
            wp_send_json_success([
                'message' => 'PDF generated successfully',
                'download_url' => $pdf_path
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to generate PDF']);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

/**
 * Generate Certificate PDF
 */
function generate_certificate_pdf($certificate_id) {
    require_once get_stylesheet_directory() . '/lib/dompdf/vendor/autoload.php';
    
    $certificate = new LLMS_User_Certificate($certificate_id);
    $user_id = $certificate->post->post_author;
    $student = llms_get_student($user_id);
    $student_name = $student ? $student->get_name() : 'Student Name';
    
    $course_id = $certificate->get('related');
    if ($course_id) {
        $course_manager = my_lifterlms_courses();
        $course_data = $course_manager->get_single_course_data($course_id);
    }
    
    $license_number = get_user_meta($user_id, 'license_number', true);
    
    // Get certificate HTML
    $html = get_certificate_pdf_html($certificate, $student_name, $license_number, $course_id, $course_data);
    
    // Create PDF
    //use Dompdf\Dompdf;
    //use Dompdf\Options;
    
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('chroot', get_stylesheet_directory());
    
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Save to temporary directory
    $upload_dir = wp_upload_dir();
    $pdf_dir = $upload_dir['basedir'] . '/certificates';
    
    // Create directory if not exists
    if (!file_exists($pdf_dir)) {
        wp_mkdir_p($pdf_dir);
    }
    
    $filename = 'certificate_' . $certificate_id . '_' . sanitize_title($student_name) . '_' . time() . '.pdf';
    $pdf_path = $pdf_dir . '/' . $filename;
    
    // Save PDF
    file_put_contents($pdf_path, $dompdf->output());
    
    // Return URL
    return $upload_dir['baseurl'] . '/certificates/' . $filename;
}

/**
 * Get Certificate PDF HTML Template
 */
function get_certificate_pdf_html($certificate, $student_name, $license_number, $course_id, $course_data) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * {
                margin:0;
                margin-top:5px;                          
                padding: 0px 5px;                
                box-sizing: border-box;
            }
            
            body {
                font-family: DejaVu Sans, sans-serif;
                background: white;
            }
            
            .certificate-container {
                border: 8px double #336666;
                padding: 30px 25px;
                background: #ffffff;
            }
            
            .certificate-header {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .certificate-title {
                font-size: 28px;
                font-weight: bold;
                color: #336666;
                margin-bottom: 10px;
                letter-spacing: 1px;
                border: 3px solid #336666;
                display: inline-block;
                padding: 12px 35px;
                background: white;
            }
            
            .certificate-number {
                font-size: 14px;
                color: #555;
                margin: 12px 0;
                font-weight: 600;
                text-align: center;
            }
            
            .recipient-info {
                text-align: center;
                margin: 18px 0;
            }
            
            .recipient-name {
                font-size: 20px;
                color: #336666;
                font-weight: bold;
                margin: 8px 0;
                text-decoration: underline;
            }
            
            .license-info {
                font-size: 14px;
                color: #555;
                margin: 8px 0;
            }
            
            .completion-text {
                text-align: center;
                font-size: 13px;
                color: #333;
                margin: 15px 0;
                line-height: 1.5;
            }
            
            .course-details {
                background: #f8f9fa;
                border-left: 4px solid #336666;
                padding: 15px;
                margin: 15px 0;
                text-align: center;
            }
            
            .course-title-cert {
                font-size: 16px;
                font-weight: bold;
                color: #336666;
                margin-bottom: 6px;
            }
            
            .course-author {
                font-size: 13px;
                color: #555;
                margin: 6px 0;
            }
            
            .ce-hours {
                font-size: 18px;
                font-weight: bold;
                color: #336666;
                margin-top: 8px;
            }
            
            .accreditation-section {
                margin: 18px 0;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 6px;
                font-size: 10px;
                line-height: 1.4;
                color: #555;
            }
            
            .accreditation-section p {
                margin-bottom: 8px;
            }
            
            .signature-section {
                margin-top: 20px;
                padding-top: 15px;
                border-top: 2px solid #e0e0e0;
            }
            
            .signature-row {
                width: 100%;
            }
            
            .company-info {
                float: left;
                width: 48%;
                font-size: 11px;
                color: #555;
                line-height: 1.5;
            }
            
            .signature-block {
                float: right;
                width: 48%;
                text-align: center;
            }
            
            .signature-image {
                max-width: 180px;
                margin-bottom: 3px;
            }
            
            .signature-line {
                border-top: 2px solid #333;
                margin: 0 auto;
                width: 180px;
            }
            
            .signature-name {
                font-size: 12px;
                color: #333;
                margin-top: 3px;
                font-weight: 600;
            }
            
            .clearfix::after {
                content: "";
                display: table;
                clear: both;
            }
        </style>
    </head>
    <body>
        <div class="certificate-container">
            <div class="certificate-header">
                <div class="certificate-title">Certificate of Completion</div>
            </div>

            <div class="certificate-number">
                Certificate #<?php echo $certificate->get('id'); ?> - Date of completion: <?php echo $certificate->get_earned_date(); ?>
            </div>

            <div class="recipient-info">
                <div class="recipient-name"><?php echo esc_html($student_name); ?></div>
                <div class="license-info">ID License: <?php echo esc_html($license_number); ?></div>
            </div>

            <div class="completion-text">
                has successfully completed a self-study, reading-based online continuing education course<br>
                provided by <strong>ContinuingEdCourses.Net</strong>
            </div>

            <div class="course-details">
                <div class="course-title-cert">
                    Course #<?php echo $course_id; ?> - <?php echo esc_html($course_data['post_title']); ?>
                </div>
                <div class="course-author">
                    by <?php 
                        $instructor_list = $course_data['instructors'];
                        if ($instructor_list) {
                            $total_instructors = count($instructor_list);
                            $count = 0;
                            foreach ($instructor_list as $course_instructor) {
                                $count++;
                                echo esc_html($course_instructor['display_name'] . ' ' . $course_instructor['llms_degrees_certs']);
                                if ($count < $total_instructors) {
                                    echo ' and ';
                                }
                            }
                        }
                    ?>
                </div>
                <div class="ce-hours"><?php echo esc_html($course_data['llmscehours']); ?> CE Hours</div>
            </div>

            <div class="accreditation-section">
                <p>ContinuingEdCourses.Net is approved by the <strong>American Psychological Association (APA)</strong> to sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.</p>

                <p>ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the <strong>Association of Social Work Boards (ASWB)</strong> Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 3/9/2005-3/9/2027. Social workers completing this course receive 3 continuing education credits.</p>

                <p>ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Psychology (NYSED-PSY)</strong> as an approved provider of continuing education for licensed psychologists #PSY-0048.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Social Work (NYSED-SW)</strong> as an approved provider of continuing education for licensed social workers #SW-0561.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Mental Health Practitioners (NYSED-MHC)</strong> as an approved provider of continuing education for licensed mental health counselors #MHC-0229.</p>
            </div>

            <div class="signature-section clearfix">
                <div class="signature-row clearfix">
                    <div class="company-info">
                        <strong>ContinuingEdCourses.Net, Inc.</strong><br>
                        12842 Francine Ct.<br>
                        Poway, CA 92064<br>
                        858-484-4304
                    </div>

                    <div class="signature-block">
                        <?php
                        $signature_path = get_stylesheet_directory() . '/assets/images/RuthSignature.jpg';                      
                        ?>
                        <img src="<?php echo  $signature_path; ?>" alt="Signature" class="signature-image">
                        <div class="signature-line"></div>
                        <div class="signature-name">
                            Ruth Samad, Ph.D.<br>Program Administrator
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Cleanup old certificate PDFs (optional - run via cron)
 */
function cleanup_old_certificate_pdfs() {
    $upload_dir = wp_upload_dir();
    $pdf_dir = $upload_dir['basedir'] . '/certificates';
    
    if (!file_exists($pdf_dir)) {
        return;
    }
    
    $files = glob($pdf_dir . '/*.pdf');
    $now = time();
    
    foreach ($files as $file) {
        if (is_file($file)) {
            // Delete files older than 24 hours
            if ($now - filemtime($file) >= 24 * 3600) {
                unlink($file);
            }
        }
    }
}

// Schedule cleanup (optional)
if (!wp_next_scheduled('cleanup_certificate_pdfs_hook')) {
    wp_schedule_event(time(), 'daily', 'cleanup_certificate_pdfs_hook');
}
add_action('cleanup_certificate_pdfs_hook', 'cleanup_old_certificate_pdfs');
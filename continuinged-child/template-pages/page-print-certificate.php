<?php
/**
 * Template Name: Page Print Certificate
 * Template Post Type: page
 * Description: Print Certificate Page.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(isset($_GET['certificate_id']))
{
    $pre_certificate=get_post($_GET['certificate_id']);
    if($pre_certificate && $pre_certificate->post_type=='llms_my_certificate')
    {
        $certificate_id=$pre_certificate->ID;
    }
    else
    {
        wp_redirect(site_url('home'));
    }
}
else
{
     wp_redirect(site_url('home'));
}

$certificate = new LLMS_User_Certificate($certificate_id);
//prevent other user from accessing the certificate page
if($certificate->post->post_author != get_current_user_id())
{
     wp_redirect(site_url('home'));
}
$user_id=$certificate->post->post_author;
//var_dump($certificate);
$student = llms_get_student( $user_id ); 
$student_name = '';
if ( $student ) {
    $student_name = $student->get_name();
}
if ( empty( $student_name ) ) {
    $student_name = 'Student Name';
}
//course
$course_id=$certificate->get('related');
if($course_id)
{
    $course_manager = my_lifterlms_courses();
    $course_data = $course_manager->get_single_course_data($course_id);
}
//user info
$license_number=get_user_meta($user_id,'license_number',true);
$ce_hour=get_post_meta($certificate_id,'ce_hours',true);
get_header();
?>
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
     
        .certificate-wrapper {
            max-width: 900px;
            margin: 10px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .certificate-container {
            border: 8px double #336666;
            padding: 30px 25px;
            background: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);
            position: relative;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .certificate-title {
            font-size: 2rem;
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
            font-size: 1rem;
            color: #555;
            margin: 12px 0;
            font-weight: 600;
        }

        .recipient-info {
            text-align: center;
            margin: 18px 0;
        }

        .recipient-name {
            font-size: 1.4rem;
            color: #336666;
            font-weight: bold;
            margin: 8px 0;
            text-decoration: underline;
            text-decoration-color: #336666;
            text-underline-offset: 4px;
        }

        .license-info {
            font-size: 1rem;
            color: #555;
            margin: 8px 0;
        }

        .completion-text {
            text-align: center;
            font-size: 0.95rem;
            color: #333;
            margin: 15px 0;
            line-height: 1.5;
        }

        .course-details {
            background: #f8f9fa;
            border-left: 4px solid #336666;
            padding: 15px;
            margin: 5px 0;
            text-align: center;
            display: block;
        }

        .course-title-cert {
            font-size: 1.1rem;
            font-weight: bold;
            color: #336666;
            margin-bottom: 6px;
        }

        .course-author {
            font-size: 0.95rem;
            color: #555;
            margin: 6px 0;
        }

        .ce-hours {
            font-size: 1.2rem;
            font-weight: bold;
            color: #336666;            
        }

        .accreditation-section {
            margin: 18px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 0.75rem;
            line-height: 1.4;
            color: #555;
        }

        .accreditation-section p {
            margin-bottom: 8px;
        }

        .accreditation-section strong {
            color: #336666;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
        }

        .company-info {
            flex: 1;
            font-size: 0.85rem;
            color: #555;
            line-height: 1.5;
        }

        .signature-block {
            flex: 1;
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
            font-size: 0.9rem;
            color: #333;
            margin-top: 3px;
            font-weight: 600;
        }

        .print-button-container {
            text-align: center;
            margin-top: 25px;
        }

        .print-button {
            background-color: #336666;
            color: white;
            border: none;
            padding: 12px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(51, 102, 102, 0.3);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background-color: #2a5555;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(51, 102, 102, 0.4);
        }

        /* action buttons */

          .action-buttons-container {
        text-align: center;
        margin-top: 25px;
        display: flex;
        gap: 15px;
        justify-content: center;
        align-items: center;
    }

    .action-button {
        background-color: #336666;
        color: white;
        border: none;
        padding: 12px 40px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(51, 102, 102, 0.3);
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        position: relative;
    }

    .action-button:hover {
        background-color: #2a5555;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(51, 102, 102, 0.4);
    }

    .action-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .action-button.download-button {
        background-color: #28a745;
    }

    .action-button.download-button:hover {
        background-color: #218838;
    }

    .loading-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-left: 8px;
        vertical-align: middle;
    }

    .action-button.loading .loading-spinner {
        display: inline-block;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
        /* end action buttons */

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .certificate-wrapper {
                max-width: 100%;
                box-shadow: none;
                padding: 10px;
            }

            .certificate-container {
                padding: 20px 18px;
                page-break-inside: avoid;
                border-width: 6px;
            }

            .certificate-title {
                font-size: 1.6rem;
                padding: 10px 28px;
            }

            .certificate-number {
                font-size: 0.9rem;
                margin: 10px 0;
            }

            .recipient-name {
                font-size: 1.2rem;
            }

            .license-info {
                font-size: 0.9rem;
            }

            .completion-text {
                font-size: 0.85rem;
                margin: 12px 0;
            }

            .course-details {
                padding: 12px;
                margin: 15px 0;
            }

            .course-title-cert {
                font-size: 1rem;
            }

            .course-author {
                font-size: 0.85rem;
            }

            .ce-hours {
                font-size: 1.1rem;
                margin-top: 8px;
            }

            .accreditation-section {
                font-size: 0.65rem;
                padding: 12px;
                margin: 15px 0;
                line-height: 1.3;
            }

            .accreditation-section p {
                margin-bottom: 6px;
            }

            .signature-section {
                margin-top: 15px;
                padding-top: 12px;
            }

            .company-info {
                font-size: 0.75rem;
            }

            .signature-name {
                font-size: 0.8rem;
            }

            .signature-image {
                max-width: 150px;
            }

            .signature-line {
                width: 150px;
            }

            .print-button-container {
                display: none;
            }
        }

          @media print {
            /* 1. Ẩn tất cả mọi thứ trên body */
            body * {
                visibility: hidden;
            }

            /* 2. Reset margin/padding của trang để tận dụng tối đa khổ giấy */
            body, html {
                margin: 0;
                padding: 0;
                background: white;
            }

            /* 3. Chỉ hiển thị phần certificate-wrapper và các con của nó */
            .certificate-wrapper, .certificate-wrapper * {
                visibility: visible;
            }

            /* 4. Định vị wrapper tuyệt đối để nó đè lên header/footer đã bị ẩn và nằm ở đầu trang */
            .certificate-wrapper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100% !important; /* Đảm bảo full chiều rộng */
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                z-index: 9999; /* Đảm bảo nằm trên cùng */
            }

            .certificate-container {
                padding: 20px 18px;
                page-break-inside: avoid;
                border-width: 6px;
                min-height: 98vh; /* Tùy chọn: giúp căn chỉnh đẹp hơn trên trang A4 */
            }

            /* Ẩn nút in khi đang in */
            .print-button-container {
                display: none !important;
            }

            /* Các style điều chỉnh font size khi in (giữ nguyên logic cũ của bạn) */
            .certificate-title {
                font-size: 1.6rem;
                padding: 10px 28px;
            }

            .certificate-number {
                font-size: 0.9rem;
                margin: 10px 0;
            }

            .recipient-name {
                font-size: 1.2rem;
            }

            .license-info {
                font-size: 0.9rem;
            }

            .completion-text {
                font-size: 0.85rem;
                margin: 12px 0;
            }

            .course-details {
                padding: 12px;
                margin: 15px 0;
            }

            .course-title-cert {
                font-size: 1rem;
            }

            .course-author {
                font-size: 0.85rem;
            }

            .ce-hours {
                font-size: 1.1rem;
                margin-top: 8px;
            }

            .accreditation-section {
                font-size: 0.65rem;
                padding: 12px;
                margin: 15px 0;
                line-height: 1.3;
            }

            .accreditation-section p {
                margin-bottom: 6px;
            }

            .signature-section {
                margin-top: 15px;
                padding-top: 12px;
            }

            .company-info {
                font-size: 0.75rem;
            }

            .signature-name {
                font-size: 0.8rem;
            }

            .signature-image {
                max-width: 150px;
            }

            .signature-line {
                width: 150px;
            }
        }

        @page {
            size: A4;
            margin: 0.8cm; 
        }
        /* ẩn page title và url page mặc định khi in */
     * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Cách chắc chắn nhất hiện nay (2025) - hoạt động tốt trên Chrome, Edge, Firefox mới */
    
    header, footer, nav, aside { display: none !important; }
     /* end ẩn page title và url page mặc định khi in */

    </style>
      <div class="certificate-wrapper">
        <div class="certificate-container">
            <div class="certificate-header">
                <div class="certificate-title">Certificate of Completion</div>
            </div>

            <div class="certificate-number">
                Certificate #<?php echo $certificate->get('id'); ?> - Date of completion: <?php echo $certificate->get_earned_date(); ?>
            </div>

            <div class="recipient-info">
                <div class="recipient-name"> <?php echo $student_name; ?> - ID License: <?php echo $license_number; ?></div>
                <div class="license-info">ID License: <?php echo $license_number; ?></div>
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

                        $instructor_list=$course_data['instructors'];
                        if($instructor_list)                            
                        {   
                            $total_instructors=count($instructor_list);
                            $count=0;                            
                            foreach( $instructor_list as $course_instructor)
                            {   
                                $count++;                                                                                
                                echo esc_html($course_instructor['display_name'].' '.$course_instructor['llms_degrees_certs']);                            
                                if ($count < $total_instructors) {
                                    echo ' and '; // Add & if not the last author
                                }                                                    
                            }
                        }
                      
                    ?>
                </div>
                <div class="ce-hours"><?php echo $ce_hour; ?> CE Hours</div>
            </div>

            <div class="accreditation-section">
                <p>ContinuingEdCourses.Net is approved by the <strong>American Psychological Association (APA)</strong> to sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.</p>

                <p>ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the <strong>Association of Social Work Boards (ASWB)</strong> Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 3/9/2005-3/9/2027. Social workers completing this course receive 3 continuing education credits.</p>

                <p>ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Psychology (NYSED-PSY)</strong> as an approved provider of continuing education for licensed psychologists #PSY-0048.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Social Work (NYSED-SW)</strong> as an approved provider of continuing education for licensed social workers #SW-0561.</p>

                <p>ContinuingEdCourses.Net is recognized by the <strong>New York State Education Department's State Board for Mental Health Practitioners (NYSED-MHC)</strong> as an approved provider of continuing education for licensed mental health counselors #MHC-0229.</p>
            </div>

            <div class="signature-section">
                <div class="company-info">
                    <strong>ContinuingEdCourses.Net, Inc.</strong><br>
                    12842 Francine Ct.<br>
                    Poway, CA 92064<br>
                    858-484-4304
                </div>

                <div class="signature-block">
                    <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/RuthSignature.jpg'; ?>" alt="Signature" class="signature-image" onerror="this.style.display='none'">
                    <div class="signature-line"></div>
                    <div class="signature-name">                        
                        Ruth Samad, Ph.D.<br>Program Administrator
                    </div>
                </div>
            </div>
        </div>

        <div class="print-button-container">
            <button class="print-button" onclick="window.print()">Print Certificate</button>
            <button class="action-button download-button" id="downloadPdfBtn" data-certificate-id="<?php echo $certificate_id; ?>">
                Download PDF
                <span class="loading-spinner"></span>
            </button>
        </div>
    </div>
    <script>
jQuery(document).ready(function($) {
    $('#downloadPdfBtn').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        var certificateId = $btn.data('certificate-id');
        
        // Disable button and show loading
        $btn.prop('disabled', true).addClass('loading');
        var originalText = $btn.html();
        $btn.html('Generating PDF... <span class="loading-spinner"></span>');
        
        // AJAX request
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'generate_certificate_pdf',
                certificate_id: certificateId,
                nonce: '<?php echo wp_create_nonce('certificate_pdf_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Create temporary link and trigger download
                    var link = document.createElement('a');
                    link.href = response.data.download_url;
                    link.download = '';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Reset button
                    $btn.html(originalText);
                    $btn.prop('disabled', false).removeClass('loading');
                    
                    // Show success message (optional)
                    alert('PDF downloaded successfully!');
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.html(originalText);
                    $btn.prop('disabled', false).removeClass('loading');
                }
            },
            error: function(xhr, status, error) {
                alert('Ajax error: ' + error);
                $btn.html(originalText);
                $btn.prop('disabled', false).removeClass('loading');
            }
        });
    });
});
</script>
<?php
get_footer();
?>




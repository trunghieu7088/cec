<style>
   .footer {
        display: block;
    }
    
    .certificate-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }

    .certificate-wrapper {
        background: white;
        border: 3px solid #2c3e50;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 50px;
        position: relative;
    }

    .certificate-title {
        text-align: center;
        border: 2px solid #2c3e50;
        padding: 15px 30px;
        display: inline-block;
        margin: 0 auto 30px;
        font-size: 1.8rem;
        font-weight: bold;
        background: white;
    }

    .certificate-header {
        text-align: center;
        margin-bottom: 10px;
    }

    .certificate-number {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .recipient-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 25px;
        color: #2c3e50;
    }

    .completion-text {
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 25px;
        color: #333;
    }

    .course-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 10px 0;
        color: #2c3e50;
    }

    .course-authors {
        font-size: 1rem;
        margin-bottom: 10px;
        color: #555;
    }

    .ce-hours {
        font-size: 1.2rem;
        font-weight: bold;
        margin: 5px 0;
        color: #2c3e50;
    }

    .approvals-section {
        font-size: 0.85rem;
        line-height: 1.8;
        margin: 30px 0;
        padding: 20px;
        background: #f8f9fa;
        border-left: 3px solid #2c3e50;
        color: #333;
    }

    .approvals-section p {
        margin-bottom: 12px;
        text-align: justify;
    }

    .approvals-section .org-name {
        font-weight: bold;
        display: inline;
    }

    .footer-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #dee2e6;
    }

    .company-info {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #555;
    }

    .signature-section {
        text-align: right;
    }

    .signature-image {
        font-family: 'Brush Script MT', cursive;
        font-size: 1.8rem;
        margin-bottom: 5px;
        color: #2c3e50;
    }

    .signature-name {
        font-size: 0.9rem;
        color: #555;
    }

    .print-button-container {
        text-align: center;
        margin: 30px 0;
    }

    .btn-print {
        background-color: #5cb85c;
        border-color: #4cae4c;
        color: white;
        padding: 10px 30px;
        font-size: 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-print:hover {
        background-color: #449d44;
        border-color: #398439;
    }

    @media (max-width: 768px) {
        .certificate-wrapper {
            padding: 30px 20px;
        }

        .certificate-title {
            font-size: 1.4rem;
            padding: 12px 20px;
        }

        .certificate-number {
            font-size: 0.95rem;
        }

        .course-title {
            font-size: 1rem;
        }

        .approvals-section {
            font-size: 0.75rem;
            padding: 15px;
        }

        .footer-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 30px;
        }

        .signature-section {
            text-align: left;
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .certificate-container {
            padding: 10px;
            margin: 20px auto;
        }

        .certificate-wrapper {
            padding: 20px 15px;
        }

        .certificate-title {
            font-size: 1.2rem;
            padding: 10px 15px;
        }

        .approvals-section {
            font-size: 0.7rem;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 0.5cm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .print-button-container {
            display: none !important;
        }

        .certificate-container {
            margin: 0 !important;
            padding: 10px !important;
            max-width: 100% !important;
            width: 100% !important;
        }

        .certificate-wrapper {
            box-shadow: none !important;
            page-break-inside: avoid;
            border: 3px solid #2c3e50 !important;
            padding: 30px !important;
            background: white !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .certificate-header {
            display: block !important;
            visibility: visible !important;
        }

        .certificate-title {
            border: 2px solid #2c3e50 !important;
            background: white !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        .certificate-number,
        .recipient-name,
        .completion-text,
        .course-title,
        .course-authors,
        .ce-hours {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
        }

        .text-center {
            text-align: center !important;
        }

        .approvals-section {
            background: #f8f9fa !important;
            border-left: 3px solid #2c3e50 !important;
            padding: 15px !important;
            margin: 20px 0 !important;
            display: block !important;
            visibility: visible !important;
        }

        .approvals-section p {
            visibility: visible !important;
            display: block !important;
            opacity: 1 !important;
            margin-bottom: 10px !important;
            color: #333 !important;
            font-size: 0.75rem !important;
            line-height: 1.6 !important;
            text-align: justify !important;
        }

        .approvals-section .org-name {
            font-weight: bold !important;
            display: inline !important;
        }

        .footer-section {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: flex-end !important;
            margin-top: 30px !important;
            padding-top: 20px !important;
            border-top: 1px solid #dee2e6 !important;
            visibility: visible !important;
            opacity: 1 !important;
            page-break-inside: avoid !important;
        }

        .company-info {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            font-size: 0.8rem !important;
            line-height: 1.5 !important;
        }

        .company-info strong,
        .company-info br {
            display: inline !important;
            visibility: visible !important;
        }

        .signature-section {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            text-align: right !important;
        }

        .signature-image {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            font-size: 1.5rem !important;
        }

        .signature-name {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
            font-size: 0.8rem !important;
        }
         .signature-image img {
            max-width: 150px !important;
            height: auto !important;
            display: block !important;
            margin: 0 0 10px auto !important;
        }
    }

    #llms-print-certificate {
        display: none;
    }
</style>
<?php 
global $post;
$certificate = new LLMS_User_Certificate( $post->ID );
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
?>

 <div class="certificate-container">
        <div class="print-date"> <span id="printDateTime"></span></div>
        <div class="certificate-wrapper">
            <div class="certificate-header">
                <div class="certificate-title">Certificate of Completion</div>
            </div>

            <div class="text-center">
                <div class="certificate-number">
                    Certificate #<?php echo $certificate->get('id'); ?> - Date of completion: <?php echo $certificate->get_earned_date(); ?>
                </div>

                <div class="recipient-name">
                    <?php echo $student_name; ?> - ID License: <?php echo $license_number; ?>
                </div>

                <div class="completion-text">
                    has successfully completed a self-study, reading-based online continuing education course provided by ContinuingEdCourses.Net
                </div>

                <div class="course-title">
                    Course #<?php echo $course_id; ?> - <?php echo esc_html($course_data['post_title']); ?>
                </div>

                <div class="course-authors">
                    by 
                    <?php 

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

                <div class="ce-hours">
                    3 CE Hours
                </div>
            </div>

            <div class="approvals-section">
                <p>ContinuingEdCourses.Net is approved by the American Psychological Association (APA) as sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.</p>

                <p>ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the Association of Social Work Boards (ASWB) Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 5/8/2025-5/8/2027. Social workers completing this course receive 3.0 continuing education credits.</p>

                <p>ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 3222. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.</p>

                <p>ContinuingEdCourses.Net is recognized by the New York State Education Department's State Board for Psychology (NYSED-PTY) as an approved provider of continuing education for licensed psychologists #PSY-0048.</p>

                <p>ContinuingEdCourses.Net is recognized by the New York State Education Department's State Board for Social Work (NYSED-SW) as an approved provider of continuing education for licensed social workers #SW-0581.</p>

                <p>ContinuingEdCourses.Net is recognized by the New York State Education Department's State Board for Mental Health Practitioners (NYSED-MHC) as an approved provider of continuing education for licensed mental health counselors #MHC-0226.</p>
            </div>

            <div class="footer-section">
                <div class="company-info">
                    <strong>ContinuingEdCourses.Net, Inc.</strong><br>
                    12842 Francine Ct.<br>
                    Poway, CA 92064<br>
                    858-484-4304
                </div>

                <div class="signature-section">
                    <div class="signature-image"><img alt="Signature" style="max-width: 200px; height: auto;" src="<?php echo get_stylesheet_directory_uri().'/assets/images/RuthSignature.jpg'; ?>"></div>
                    <div class="signature-name">Ruth Samad, Ph.D. Program Administrator</div>
                </div>
            </div>
        </div>

        <div class="print-button-container">
            <button class="btn btn-print" onclick="window.print()">Print</button>
        </div>
    </div>
<script>
// Set print date and time
window.addEventListener('beforeprint', function() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    };
    document.getElementById('printDateTime').textContent = now.toLocaleString('en-US', options);
});
</script>
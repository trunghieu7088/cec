<?php

function custom_static_approvals_shortcode() {
    ob_start();
    ?>
    <section class="approvals-section py-5">
    <div class="container">
        <div class="approvals-intro text-center mb-5">
            <h2><i class="bi bi-patch-check-fill text-success me-2"></i>Approvals & Accreditations</h2>
            <p class="lead">We are committed to providing high-quality courses, recognized by leading reputable organizations in the field of continuing education.</p>
        </div>

        <div class="row g-4">
            <!-- Approval Card: APA -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                        <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/APA.png'; ?>" alt="APA Logo" class="img-fluid me-3" style="height: 100px;">
                       
                        <h3 class="reward-title">American Psychological Association (APA)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net is approved by the <b>American Psychological Association (APA)</b> to sponsor continuing education for psychologists. ContinuingEdCourses.Net maintains responsibility for this program and its content.</p>
                    </div>
                </div>
            </div>

            <!-- Approval Card: ASWB -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                      
                            <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/ASWB.png'; ?>" alt="ASWB Logo" class="img-fluid me-3" style="height: 100px;">
                     
                        <h3 class="reward-title">Association of Social Work Boards (ASWB)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net, provider #1107, is approved as an ACE provider to offer social work continuing education by the <b>Association of Social Work Boards (ASWB)</b> Approved Continuing Education (ACE) program. Regulatory boards are the final authority on courses accepted for continuing education credit. ACE provider approval period: 3/9/2005-3/9/2027.</p>
                    </div>
                </div>
            </div>

            <!-- Approval Card: NBCC -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                      
                            <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/NBCC.png'; ?>" alt="NBCC Logo" class="img-fluid me-3" style="height: 100px;">
                
                        <h3 class="reward-title">National Board for Certified Counselors (NBCC)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net has been approved by NBCC as an Approved Continuing Education Provider, ACEP No. 6323. Programs that do not qualify for NBCC credit are clearly identified. ContinuingEdCourses.Net is solely responsible for all aspects of the programs.</p>
                    </div>
                </div>
            </div>

            <!-- Approval Card: NYSED-PSY -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                       
                            <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/NYSED.png'; ?>" alt="NYSED Logo" class="img-fluid me-3" style="height: 100px;">
                    
                        <h3 class="reward-title">NY State Education Department (NYSED-PSY)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net is recognized by the <b>New York State Education Department's State Board for Psychology (NYSED-PSY)</b> as an approved provider of continuing education for licensed psychologists #PSY-0048.</p>
                    </div>
                </div>
            </div>

            <!-- Approval Card: NYSED-SW -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                     
                            <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/NYSED.png'; ?>" alt="NYSED Logo" class="img-fluid me-3" style="height: 100px;">
                       
                        <h3 class="reward-title">NY State Education Department (NYSED-SW)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net is recognized by the <b>New York State Education Department's State Board for Social Work (NYSED-SW)</b> as an approved provider of continuing education for licensed social workers #SW-0561.</p>
                    </div>
                </div>
            </div>

            <!-- Approval Card: NYSED-MHC -->
            <div class="col-lg-6">
                <div class="reward-card h-100">
                    <div class="reward-header">
                      
                            <img src="<?php echo get_stylesheet_directory_uri().'/assets/images/NYSED.png'; ?>" alt="NYSED Logo" class="img-fluid me-3" style="height: 100px;">
                      
                        <h3 class="reward-title">NY State Education Department (NYSED-MHC)</h3>
                    </div>
                    <div class="reward-details">
                        <p>ContinuingEdCourses.Net is recognized by the <b>New York State Education Department's State Board for Mental Health Practitioners (NYSED-MHC)</b> as an approved provider of continuing education for licensed mental health counselors #MHC-0229.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="highlight-box mt-5 text-center">
            <p class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i><b>Note:</b> No conflicts of interest have been reported by the authors. While we may provide guidance, it is your responsibility to verify your continuing education requirements with your licensing board.</p>
        </div>

        <div class="cta-section mt-5">
            <h3><i class="bi bi-question-circle-fill me-2"></i>Questions?</h3>
            <p>Feel free to contact us at <a style="color:white;" href="tel:858-484-4304">858-484-4304</a> or via email at <a style="color:white;" href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>.</p>
        </div>
    </div>
</section>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_static_approvals', 'custom_static_approvals_shortcode');


<?php
/**
 * Template Name: Contact us & help Page
 * Template Post Type: page
 * Description: A responsive, full-width template utilizing the standard WordPress Loop and core theme functions for maximum compatibility and maintainability.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
$contact_us_title=get_theme_mod('contact_title','Contact Information');
$address=get_theme_mod('cec_contact_address',' 12842 Francine Ct. Poway CA 9206422');
$phone=get_theme_mod('cec_contact_phone','858-484-4304');
$email_address=get_theme_mod('contact_email','Contact@ContinuingEdCourses.Net');
?>
<style>
    
    .accordion-button:hover {
       color: var(--primary-blue);
        background-color: #e6fcfc; 
    }
</style>
<div class="container mb-5">
    
    <!-- Page Header -->
    <div class="header-section text-center fade-in">
        <h1 class="main-title">Contact Us</h1>
        <p class="text-muted mt-2 mb-0">We are here to help you with any questions or suggestions.</p>
    </div>

    <div class="row g-4 fade-in">
        
        <!-- Left Column: Contact Info & Content -->
        <!-- Left Column: Contact Info & Content -->
            <div class="col-lg-6">
                <div class="content-card">
                    <h2 class="section-heading"><i class="bi bi-info-circle-fill"></i> <?php echo $contact_us_title; ?></h2>
                    
                    <!-- Address -->
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <strong>ContinuingEdCourses.Net, Inc.</strong><br>
                            <?php  echo $address;?>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <strong>Phone:</strong><br>
                            <?php echo $phone; ?>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <strong>General Inquiries:</strong><br>
                            <a href="mailto:<?php echo $email_address; ?>" class="contact-link"><?php echo $email_address; ?></a>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon"><i class="bi bi-pc-display"></i></div>
                        <div>
                            <strong>Website Problems:</strong><br>
                            <span class="text-muted">Found a bug?</span> <a href="mailto:<?php echo $email_address; ?>" class="contact-link">Email our Webmaster</a>
                        </div>
                    </div>

                    <hr class="my-4" style="opacity: 0.1">

                    <!-- Opportunities Section -->
                    <h2 class="section-heading"><i class="bi bi-lightbulb-fill"></i> Opportunities</h2>
                    
                    <div class="highlight-box">
                        <span class="highlight-title">Prospective Authors</span>
                        <p class="small mb-2">Interested in publishing a course? Please email us with your CV, phone number, and proposal. Courses must be post-doctoral level.</p>
                        <a href="<?php echo get_custom_page_url_by_template('page-proposal-author.php'); ?>" class="small contact-link"><i class="bi bi-file-earmark-arrow-up"></i> Submit Proposal</a> 
                       <!-- &nbsp;|&nbsp;  <a href="CourseProposal.docx" class="small contact-link"><i class="bi bi-download"></i> Download Form</a> -->
                    </div>

                    <div class="highlight-box" style="background-color: #fff9e6; border-color: #ffcc00;">
                        <span class="highlight-title" style="color: #997a00;">Course Suggestions</span>
                        <p class="small mb-1">Have a topic request? Take our survey to provide suggestions.</p>
                        <a href="<?php echo get_custom_page_url_by_template('page-survey.php'); ?>" class="small" style="color: #997a00; font-weight:600;">Take Survey &rarr;</a>
                    </div>

                    <!-- Accordion for Secondary Info (Team & Policies) -->
                    <div class="accordion mt-4" id="infoAccordion">
                        <!-- Team -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTeam">
                                    Our Team
                                </button>
                            </h2>
                            <div id="collapseTeam" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body small">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2"><strong>Gary Samad</strong>, Chief Executive Officer</li>
                                        <li class="mb-2"><strong>Rosalie Easton, Ph.D.</strong>, Advisory Board Chair</li>
                                        <li class="mb-2"><strong>Randy Kasper, LCSW, BCD, Ph.D.</strong>, Advisory Board Member</li>
                                        <li><strong>Ruth Samad, Ph.D.</strong>, Program Administrator</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Policies -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePolicies">
                                    Policies & Procedures
                                </button>
                            </h2>
                            <div id="collapsePolicies" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body small">
                                    <div class="d-flex flex-wrap gap-3">
                                        <a href="<?php echo site_url('policies'); ?>#privacy-policy" class="contact-link">Privacy Policy</a>
                                        <a href="<?php echo site_url('policies'); ?>#refund-policy" class="contact-link">Refund Policy</a>
                                        <a href="<?php echo site_url('policies'); ?>#complaint-procedure" class="contact-link">Complaint Procedure</a>
                                        <a href="<?php echo site_url('policies'); ?>#ada-accommodations" class="contact-link">ADA Accommodations</a>
                                        <a href="<?php echo site_url('policies'); ?>#conflict-of-interest" class="contact-link">Conflict of Interest</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <!-- Other Links -->
                         <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLinks">
                                    Resources
                                </button>
                            </h2>
                            <div id="collapseLinks" class="accordion-collapse collapse" data-bs-parent="#infoAccordion">
                                <div class="accordion-body small">
                                    <a href="<?php echo site_url('links'); ?>" class="contact-link">View useful links and resources</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        <!-- Right Column: Contact Form -->
        <div class="col-lg-6">
            <div class="content-card">
                <h2 class="section-heading"><i class="bi bi-send-fill"></i> Send us a Message</h2>
                <p class="mb-4 text-muted">Please fill out the form below and we will get back to you as soon as possible. Fields marked with <span class="required">*</span> are required.</p>
                
                <form id="contactForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firstName" class="form-label fw-bold small">First Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="firstName" placeholder="John" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label fw-bold small">Last Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold small">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="john@example.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold small">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" placeholder="(555) 123-4567">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold small">Subject <span class="required">*</span></label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="How can we help?" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-bold small">Message <span class="required">*</span></label>
                        <textarea class="form-control" id="message" rows="5" placeholder="Your message here..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-send-fill me-2"></i>Send Message
                    </button>

                    <div id="formMessage" class="mt-3"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
?>
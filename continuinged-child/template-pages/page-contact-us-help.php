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
?>
  <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Contact Information -->
                <div class="col-lg-4 mb-4">
                    <div class="contact-info-card">
                        <h3>Get in Touch</h3>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="contact-details">
                                <h5>Phone</h5>
                                <p><a href="tel:858-842-4100">858-842-4100</a></p>
                                <small class="text-muted">Monday - Friday, 9am - 5pm PST</small>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="contact-details">
                                <h5>Email</h5>
                                <p><a href="mailto:Contact@SocialWorkCoursesOnline.com">Contact@SocialWorkCoursesOnline.com</a></p>
                                <small class="text-muted">We typically respond within 24 hours</small>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="contact-details">
                                <h5>Business Hours</h5>
                                <p>Monday - Friday<br>9:00 AM - 5:00 PM PST</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-question-circle-fill"></i>
                            </div>
                            <div class="contact-details">
                                <h5>Support</h5>
                                <p>Have questions about our courses or need technical support? We're here to help!</p>
                            </div>
                        </div>
                         <div class="contact-item">
                            <div class="contact-icon">
                                <i class="bi bi-question-circle-fill"></i>
                            </div>
                            <div class="contact-details">
                                <h5>Other contact info</h5>
                               <div class="d-flex flex-column">
                                    <span>Gary Samad, Chief Executive Officer.</span>
                                    <span>Rosalie Easton, Ph.D., Advisory Board Chair.</span>
                                    <span>Randy Kasper, LCSW, BCD, Ph.D., Advisory Board Member.</span>
                                    <span>Ruth Samad, Ph.D., Program Administrator.</span>
                               </div>
                                        
                                   
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-form-card">
                        <h3>Send Us a Message</h3>
                        
                        <div class="info-box">
                            <p><i class="bi bi-info-circle-fill me-2"></i>Please fill out the form below and we'll get back to you as soon as possible. Fields marked with <span class="required">*</span> are required.</p>
                        </div>

                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="firstName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="lastName" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="required">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="required">*</span></label>
                                <textarea class="form-control" id="message" rows="6" required></textarea>
                            </div>


                            <div class="d-grid gap-2 d-md-block">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                </button>
                            </div>

                            <div id="formMessage" class="mt-3"></div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="contact-info-card">
                        <h3><i class="bi bi-lightbulb-fill text-warning me-2"></i>Frequently Asked Questions</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h5 class="text-default">What credit cards do you accept?</h5>
                                <p class="text-muted">We accept Mastercard, Visa, American Express, and Discover. We also accept checks or money orders.</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h5 class="text-default">How quickly can I get my certificate of completion?</h5>
                                <p class="text-muted">After passing the test and paying with a credit card, you can print your certificate immediately. For an additional fee we will print your certificate and mail it to you.</p>
                            </div>                          
                            <div class="col-md-6 mb-3">
                                <h5 class="text-default">Are your courses really free?</h5>
                                <p class="text-muted">You may view our courses for free. After you have taken the test and received a score of 75% or better you may purchase the course and print your Certificate of Completion. We will maintain records of your course completion for at least five years.</p>
                            </div>
                             <div class="col-md-6 mb-3">
                                <h5 class="text-default">Can I copy your courses and give them to my friends?</h5>
                                <p class="text-muted">All of our courses are copyrighted. We encourage you to email links to our courses to your colleagues. You may not make copies of our courses for commercial purposes without prior written approval.</p>
                            </div>

                              <div class="col-md-12 mb-3">
                                <h5 class="text-default">I don't want to provide my credit card number over the Internet. What should I do?</h5>
                                <p class="text-muted">Our purchasing process is secure. We use encryption between your computer and our servers to keep your credit card information from prying eyes. We never permanently store your credit card number; after the transaction is complete, we erase your credit card number from our site.</p>
                                <p class="text-muted">If you are still uneasy about submitting your credit card number over the Internet, you may fill out the form on the Purchase Certificate page, print it, and either call us or mail the form with your credit card information or a check. (See the ContactUs page for our mailing address and phone number.) Please be sure to include your name, address, phone number, course Completion Code, credit card number, and expiration date. We will email or call you back once your transaction is complete.</p>
                            </div>
                                                      
                            <div class="col-md-12 mb-3">
                                <h5 class="text-default">What is your privacy policy?</h5>
                                <p class="text-muted">You may view our privacy policy and other policies and procedures on our Policies and Procedures page.</p>
                                 <p class="text-muted">
                                    <ul class="preference-links">
                                        <li><a href="/policies/#privacy-policy">Privacy Policy</a></li>
                                        <li><a href="/policies/#refund-policy">Refund Policy</a></li>
                                        <li><a href="/policies/#complaint-procedure">Complaint Procedure</a></li>
                                        <li><a href="/policies/#conflict-of-interest">Conflict of Interest</a></li>
                                    </ul>
                                </p>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
get_footer();
?>
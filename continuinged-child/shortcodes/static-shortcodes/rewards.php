<?php

function custom_static_rewards_shortcode() {
    ob_start();
    ?>
 <!-- Rewards Section -->
    <section class="rewards-section">
        <div class="container">
            <!-- Introduction -->
            <div class="rewards-intro">
                <h2><i class="bi bi-star-fill text-warning me-2"></i>Welcome to Our Rewards Program</h2>
                <p>At Continuing Education Courses, we believe in rewarding your commitment to professional development. Our rewards program is designed to make your learning journey more affordable and rewarding.</p>
                
                <div class="highlight-box">
                    <p class="mb-0"><i class="bi bi-info-circle-fill me-2"></i><strong>How It Works:</strong> Earn rewards for every course you complete and redeem them for discounts on future courses. The more you learn, the more you save!</p>
                </div>
            </div>

            <!-- Reward Tiers -->
            <div class="row">
                <!-- Bronze Tier -->
                <div class="col-lg-4 col-md-6">
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-icon">
                                <i class="bi bi-award"></i>
                            </div>
                            <h3 class="reward-title">Bronze Level</h3>
                        </div>
                        <div class="reward-value">5% Off</div>
                        <p class="reward-description">Start earning rewards from your very first course!</p>
                        <div class="reward-details">
                            <strong>Benefits:</strong>
                            <ul>
                                <li>5% discount on future courses</li>
                                <li>Access to exclusive newsletters</li>
                                <li>Early notification of new courses</li>
                            </ul>
                            <strong class="text-primary">Requirements:</strong> Complete 1-3 courses
                        </div>
                    </div>
                </div>

                <!-- Silver Tier -->
                <div class="col-lg-4 col-md-6">
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-icon">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <h3 class="reward-title">Silver Level</h3>
                        </div>
                        <div class="reward-value">10% Off</div>
                        <p class="reward-description">Take your learning further and save more!</p>
                        <div class="reward-details">
                            <strong>Benefits:</strong>
                            <ul>
                                <li>10% discount on future courses</li>
                                <li>Priority customer support</li>
                                <li>Free downloadable resources</li>
                                <li>All Bronze benefits</li>
                            </ul>
                            <strong class="text-primary">Requirements:</strong> Complete 4-9 courses
                        </div>
                    </div>
                </div>

                <!-- Gold Tier -->
                <div class="col-lg-4 col-md-6">
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-icon">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <h3 class="reward-title">Gold Level</h3>
                        </div>
                        <div class="reward-value">15% Off</div>
                        <p class="reward-description">Our highest tier for dedicated learners!</p>
                        <div class="reward-details">
                            <strong>Benefits:</strong>
                            <ul>
                                <li>15% discount on future courses</li>
                                <li>Exclusive webinars and events</li>
                                <li>Free certificate expedited shipping</li>
                                <li>Personalized learning recommendations</li>
                                <li>All Silver & Bronze benefits</li>
                            </ul>
                            <strong class="text-primary">Requirements:</strong> Complete 10+ courses
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Rewards -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3 class="reward-title">Referral Bonus</h3>
                        </div>
                        <div class="reward-value">$10 Credit</div>
                        <p class="reward-description">Refer a colleague and you both earn rewards!</p>
                        <div class="reward-details">
                            <ul>
                                <li>Earn $10 credit for each successful referral</li>
                                <li>Your referral gets $10 off their first course</li>
                                <li>No limit on number of referrals</li>
                                <li>Credits never expire</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="reward-card">
                        <div class="reward-header">
                            <div class="reward-icon">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <h3 class="reward-title">Loyalty Bonus</h3>
                        </div>
                        <div class="reward-value">20% Off</div>
                        <p class="reward-description">Annual reward for our most dedicated learners!</p>
                        <div class="reward-details">
                            <ul>
                                <li>Complete 6+ courses in a calendar year</li>
                                <li>Receive 20% off one course of your choice</li>
                                <li>Valid for the following calendar year</li>
                                <li>Stackable with tier discounts</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h3><i class="bi bi-rocket-takeoff-fill me-2"></i>Ready to Start Earning Rewards?</h3>
                <p>Browse our extensive course catalog and begin your journey to professional excellence today!</p>
                <a href="/#courses" class="cta-button">
                    <i class="bi bi-book-fill me-2"></i>Browse Courses
                </a>
            </div>

            <!-- Terms and Conditions -->
            <div class="terms-section">
                <h3><i class="bi bi-file-text-fill me-2"></i>Terms & Conditions</h3>
                <ul>
                    <li>Rewards are automatically applied to your account upon course completion</li>
                    <li>Discounts cannot be combined with other promotional offers unless explicitly stated</li>
                    <li>Tier status is evaluated at the time of course purchase</li>
                    <li>Course completions from all time count toward your tier status</li>
                    <li>Rewards have no cash value and are non-transferable</li>
                    <li>We reserve the right to modify the rewards program with 30 days notice</li>
                    <li>Fraudulent activity will result in account suspension and forfeiture of rewards</li>
                    <li>Refunded courses do not count toward tier status or rewards</li>
                </ul>
                <p class="mt-3"><strong>Questions?</strong> Contact us at <a href="mailto:Contact@SocialWorkCoursesOnline.com">Contact@SocialWorkCoursesOnline.com</a> or call <a href="tel:858-842-4100">858-842-4100</a></p>
            </div>
        </div>
    </section>
        <div class="container">
            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-12">
                    <div class="info-box">
                        <p><i class="bi bi-info-circle-fill me-2"></i>If you don't find what you need here, please <a href="/contact-us-help/">Contact Us</a> to phone or email us.</p>
                    </div>

                    <!-- Privacy Policy -->
                    <div class="content-section">
                        <h2 id="privacy-policy">Privacy Policy</h2>
                        
                        <h3>Your Privacy</h3>
                        <p>At ContinuingEdCourses.Net, we are committed to protecting your privacy. We use the information we collect about you to process orders and to provide a more personalized shopping experience. Please read on for more details about our privacy policy.</p>

                        <h3>What Information Do We Collect? How Do We Use It?</h3>
                        <p>When you order a certificate, we need to know your name, e-mail address, mailing address, credit-card number and expiration date. This allows us to process and fulfill your order. We do not store your credit card number permanently. You may voluntarily sign up to be informed about new courses. For this service we need only an e-mail address, which we use to send the information you requested.</p>
                        
                        <p>We may personalize your shopping experience by using your purchases to shape our recommendations about other courses that might be of interest to you. We also monitor customer traffic patterns and site usage to help us develop the design and layout of the website. We may also use the information we collect to occasionally notify you about important changes to the Web site, new courses, and special offers we think you will find valuable. If you would rather not receive this information, please notify us by sending an e-mail message to <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>.</p>

                        <h3>How Does ContinuingEdCourses.Net Protect Customer Information?</h3>
                        <p>We never store your credit card information permanently. Your other account information including name, mailing address, email address, etc. is stored in our database and is protected from unauthorized access by password.</p>

                        <h3>What About "Cookies"?</h3>
                        <p>"Cookies" are small pieces of information that are stored by your browser on your computer. Our cookies do not contain any personally identifying information. We only use "session" cookies, which are temporary and are not stored on your hard drive, in order to help us track your progress and process your purchases.</p>

                        <h3>Will ContinuingEdCourses.Net Disclose the Information it Collects to Outside Parties?</h3>
                        <p>ContinuingEdCourses.Net does not sell, trade or rent your personal information to others. We may choose to do so in the future with trustworthy third parties, but you can tell us not to by sending an e-mail message to <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>.</p>
                        
                        <p>Also, ContinuingEdCourses.Net may provide aggregate statistics about our customers, sales, traffic patterns, and related site information to reputable third-party vendors, but these statistics will include no personally identifying information.</p>

                        <h3>In Summary</h3>
                        <p>We are committed to protecting your privacy. We use the information we collect on the site to make shopping at ContinuingEdCourses.Net possible and to enhance your overall experience. We do not sell, trade or rent your personal information to others. We may choose to do so in the future with trustworthy third parties, but you can tell us not to by sending an e-mail message to <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>.</p>

                        <h3>Your Consent</h3>
                        <p>By using our Web site, you consent to the collection and use of this information by ContinuingEdCourses.Net. If we decide to change our privacy policy, we will post those changes on this page, so you are always aware of what information we collect, how we use it and under what circumstances we disclose it.</p>

                        <h3>Tell Us What You Think</h3>
                        <p>ContinuingEdCourses.Net welcomes your questions and comments about privacy. Please e-mail us at <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>.</p>
                    </div>

                    <!-- Refund Policy -->
                    <div class="content-section">
                        <h2 id="refund-policy">Refund Policy</h2>
                        <p>Since customers have the opportunity to view the entire course and pass the test before payment, we don't offer refunds.</p>
                        
                        <p>We will, however, make an adjustment to your account if there is a billing error.</p>
                        
                        <p>We will consider requests on a case-by-case basis.</p>
                        
                        <p>If you have any questions about this please contact us at <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a> or 858-484-4304.</p>
                    </div>

                    <!-- Complaint Procedure -->
                    <div class="content-section">
                        <h2 id="complaint-procedure">Complaint Procedure</h2>
                        <p>Participants who have complaints may take one of the following steps:</p>
                        
                        <ul class="outline-list">
                            <li>After completing a course, fill out the Course Evaluation which will be reviewed by the Program Administrator, or</li>
                            <li>Contact the Program Administrator directly at <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a> or 858-484-4304.</li>
                        </ul>
                        
                        <p>In either case, the Program Administrator will determine a course of action to resolve the complaint with the participant. We are committed to making your learning experience a positive one.</p>
                    </div>

                    <!-- ADA Accommodations -->
                    <div class="content-section">
                        <h2 id="ada-accommodations">ADA Accommodations</h2>
                        <p>Since these are online courses, the font size is adjustable for the visually impaired. All materials may be printed out to be read aloud to the participant.</p>
                    </div>

                    <!-- Conflict of Interest -->
                    <div class="content-section">
                        <h2 id="conflict-of-interest">Conflict of Interest</h2>
                        <p>We do not accept courses from authors for whom there are potential conflicts of interest, including commercial support, other authors, content of instruction, or any other relationship that could reasonably be construed as a conflict of interest.</p>
                    </div>
                </div>               
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_static_rewards', 'custom_static_rewards_shortcode');


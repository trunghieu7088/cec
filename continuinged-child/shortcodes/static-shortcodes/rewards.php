<?php

function custom_static_rewards_shortcode() {
    ob_start();
    ?>
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Page Title Section -->
                    <div class="page-title-section">
                        <h1 class="page-title">CERewards<sup style="font-size: 0.6em;">TM</sup></h1>
                    </div>

                    <!-- Introduction Card -->
                    <div class="content-card">
                        <div class="intro-text">
                            <p>The CERewards program provides you with cumulative discounts based on the total number of courses that you have completed. We automatically provide you with discounts of up to 35% based on the total number of hours that you have already completed.</p>
                        </div>
                    </div>

                    <!-- Rewards Table Card -->
                    <div class="content-card">
                        <h2 class="section-title">
                            <i class="bi bi-award-fill"></i> Discount Levels
                        </h2>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle text-center">
                                <thead style="background-color: var(--primary-blue); color: white;">
                                    <tr>
                                        <th colspan="2" style="font-size: 1.2rem; padding: 15px;">
                                            <strong>CERewards<sup style="font-size: 0.7em;">TM</sup></strong>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="padding: 12px;">Total Hours</th>
                                        <th style="padding: 12px;">Discount Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px;">1-10</td>
                                        <td style="padding: 10px;">0%</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">11-20</td>
                                        <td style="padding: 10px;"><strong>5%</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">21-40</td>
                                        <td style="padding: 10px;"><strong>10%</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">41-80</td>
                                        <td style="padding: 10px;"><strong>15%</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">81-150</td>
                                        <td style="padding: 10px;"><strong>20%</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">151-300</td>
                                        <td style="padding: 10px;"><strong>25%</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px;">301-500</td>
                                        <td style="padding: 10px;"><strong>30%</strong></td>
                                    </tr>
                                    <tr style="background-color: #fff9e6;">
                                        <td style="padding: 10px;"><strong>501+</strong></td>
                                        <td style="padding: 10px;"><strong style="color: var(--primary-blue); font-size: 1.2rem;">35%</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="note-box mt-4">
                            <p><strong>How it works:</strong> Your discount is cumulative, based on the number of hours that you have completed and paid for, no matter how many years it has taken. Your current discount will be applied to each new course that you take, until you reach a new discount level. The CERewards discount is in addition to any other promotional discount to which you may be entitled.</p>
                        </div>

                        <div class="highlight-box mt-3">
                            <p><strong>Example:</strong> If you have already completed 50 hours of courses, you will automatically receive a CERewards discount of 15% on your courses. If you also have a promotional discount code for 10% off, then you will receive 10%+15% for a total of 25% off of your next course and all future courses until you reach the next CERewards level.</p>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="content-card">
                        <h2 class="section-title">
                            <i class="bi bi-question-circle-fill"></i> Frequently Asked Questions
                        </h2>

                        <div class="content-text">
                            <h3>How can I view my current CERewards discount level?</h3>
                            <p>Simply sign in to your account by clicking <a href="<?php echo get_custom_page_url_by_template('page-account.php') ?>">My Account</a>.</p>

                            <h3>What if I have a discount code or promotional code from an advertisement?</h3>
                            <p>Your CERewards discount is added to any promotional or discount code that you may have. For instance, if you have a discount code for 10% off, and you have accumulated 20 hours of courses, you will receive an additional CERewards discount of 10%, for a total of 20% off.</p>

                            <h3>Is the discount retroactive?</h3>
                            <p>No. We do not provide CERewards discounts on courses that you have previously completed and paid for. However, we do include all courses that you have ever taken with us when calculating your current discount. For instance, if you have already taken 20 hours of courses with us, we will automatically apply an additional 10% discount to any new courses that you take. After you reach 40 hours, we will automatically apply an additional 15% discount, and so on.</p>

                            <h3>I don't see all of the courses that I have taken over the years?</h3>
                            <p>If you log into your account and see fewer courses than you think you have completed over the years, then you have probably set up more than one account. See the next section:</p>

                            <h3>What if I have more than one account?</h3>
                            <p>We can consolidate all of your accounts and courses under a single account for you. That way, you will receive the highest possible CERewards discount. To request account consolidation simply email us at <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a> or call us at 858-484-4304 and include your full name, phone number, usernames that you remember, and any other information that you think will be helpful. We will consolidate all of your courses under the latest username, unless you request something different.</p>

                            <div class="warning-box">
                                <strong>Important:</strong> Please note that if you continue to take courses before we have completed the consolidation then you will only receive the CERewards discount level that you have earned for that one account. We cannot retroactively apply CERewards discounts, so please ask for account consolidation before taking any further courses in order to receive the highest CERewards discount.
                            </div>

                            <h3>Will the CERewards program ever change or be discontinued?</h3>
                            <p>We reserve the right to change the CERewards program at any time. However, we promise to give you at least 30 days notice on our home page if we decide to change it in a way that reduces your benefits. That way, you can be confident that you will have enough time to complete your courses and receive the discounts that you expect. If we decide to change the program in a way that benefits you, we will simply implement it immediately.</p>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="rewards-section text-center">
                        <h2><i class="bi bi-star-fill"></i> Start Earning Rewards Today!</h2>
                        <p>Browse our courses and start building your CERewards discount level.</p>
                        <a href="<?php echo get_custom_page_url_by_template('page-course-listing.php') ?>" class="btn-custom" style="background: white; color: var(--primary-blue); margin-top: 15px;">
                            View All Courses
                        </a>
                    </div>

                </div>               
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('custom_static_rewards', 'custom_static_rewards_shortcode');


<?php

function custom_static_links_shortcode() {
    ob_start();
    ?>
    <style>
        .accreditation-item p
        {
            margin-bottom:10px;
        }
    </style>
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Page Title Section -->
                    <div class="page-title-section">
                        <h1 class="page-title">Professional Links & Affiliations</h1>
                    </div>

                    <!-- Affiliations Section -->
                    <div class="content-card">
                        <h2 class="section-title">
                            <i class="bi bi-link-45deg"></i> Our Affiliations
                        </h2>
                        
                        <div class="content-text">
                            <!-- Accreditation Items -->
                            <div class="accreditation-item">
                                <p>
                                    <strong><a href="http://www.apa.org/" target="_blank" rel="noopener">American Psychological Association</a></strong> - 
                                    ContinuingEdCourses.Net is an <a href="http://www.apa.org/education/ce/sponsors.aspx?item=2#california" target="_blank" rel="noopener">APA Approved course sponsor</a>.
                                </p>
                            </div>

                            <div class="accreditation-item">
                                <p>
                                    <strong><a href="http://www.aswb.org/" target="_blank" rel="noopener">Association of Social Work Boards</a></strong> - 
                                    ContinuingEdCourses.Net is an <a href="https://www.datapathdesign.com/ASWB/ACEdswb/Prod/cgi-bin/ACESearchDSWBDLL.dll/acesShowProviders" target="_blank" rel="noopener">ASWB Approved course sponsor</a>.
                                </p>
                            </div>

                            <div class="accreditation-item">
                                <p>
                                    <strong><a href="http://www.nbcc.org/" target="_blank" rel="noopener">National Board for Certified Counselors</a></strong> - 
                                    ContinuingEdCourses.Net is an <a href="http://www.nbcc.org/ACEPdirectory/List" target="_blank" rel="noopener">NBCC Approved course sponsor</a>.
                                </p>
                            </div>

                            <div class="accreditation-item">
                                <p>
                                    <strong><a href="http://www.bbs.ca.gov/" target="_blank" rel="noopener">California Board of Behavioral Sciences</a></strong> - 
                                    ContinuingEdCourses.Net is a <a href="http://www.bbs.ca.gov/pdf/forms/celist.pdf" target="_blank" rel="noopener">CA-BBS Approved course sponsor</a>.
                                </p>
                            </div>

                            <div class="accreditation-item">
                                <p>
                                    <strong><a href="http://cswmft.ohio.gov/Home.aspx" target="_blank" rel="noopener">Ohio Counselor, Social Worker, & Marriage and Family Therapist Board</a></strong> - 
                                    ContinuingEdCourses.Net is an <a href="http://cswmft.ohio.gov/Portals/0/CONT ED/provider.pdf" target="_blank" rel="noopener">OH-CSWMFT Approved course sponsor</a>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Associations -->
                    <div class="content-card">
                        <h2 class="section-title">
                            <i class="bi bi-building"></i> Professional Associations
                        </h2>
                        
                        <div class="content-text">
                            <p>Look for our advertisements in the following professional publications:</p>
                            
                            <div class="learning-objectives">
                                <ul>
                                    <li>
                                        <strong><a href="http://www.cpapsych.org/" target="_blank" rel="noopener">California Psychological Association</a></strong> - 
                                        <em>California Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.sdpsych.org/" target="_blank" rel="noopener">San Diego Psychological Association</a></strong> - 
                                        <em>San Diego Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.flapsych.com/" target="_blank" rel="noopener">Florida Psychological Association</a></strong> - 
                                        <em>Florida Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.azpa.org/" target="_blank" rel="noopener">Arizona Psychological Association</a></strong> - 
                                        <em>Arizona Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.papsy.org/" target="_blank" rel="noopener">Pennsylvania Psychological Association</a></strong> - 
                                        <em>Pennsylvania Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.ohpsych.org/" target="_blank" rel="noopener">Ohio Psychological Association</a></strong> - 
                                        <em>Ohio Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.masspsych.org/" target="_blank" rel="noopener">Massachusetts Psychological Association</a></strong> - 
                                        <em>Massachusetts Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.marylandpsychology.org/" target="_blank" rel="noopener">Maryland Psychological Association</a></strong> - 
                                        <em>Maryland Psychologist</em> magazine
                                    </li>
                                    <li>
                                        <strong><a href="http://www.naswil.org/" target="_blank" rel="noopener">NASW Illinois</a></strong> - 
                                        <em>Social Work Networker</em> magazine
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Sister Site -->
                    <div class="content-card">
                        <h2 class="section-title">
                            <i class="bi bi-globe"></i> Sister Site
                        </h2>
                        
                        <div class="highlight-box">
                            <p>
                                <strong><a href="http://www.SocialWorkCoursesOnline.com/" target="_blank" rel="noopener">SocialWorkCoursesOnline.com</a></strong><br>
                                Our dedicated site for social workers, offering specialized continuing education courses.
                            </p>
                        </div>
                    </div>

                    <!-- Link to Us Section -->
                    <div class="rewards-section text-center" style="margin-bottom:10px;">
                        <h2><i class="bi bi-share-fill"></i> Link to Us</h2>
                        <p>If you wish to link to us on your website, we'd be honored!</p>
                        <a href="/links-to-us/" class="btn-custom" style="background: white; color: var(--primary-blue); margin-top: 15px;">
                            View Link Examples
                        </a>
                    </div>

                </div>               
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_static_links', 'custom_static_links_shortcode');
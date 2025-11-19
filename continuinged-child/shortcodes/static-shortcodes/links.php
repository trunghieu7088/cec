<?php

function custom_static_links_shortcode() {
    ob_start();
    ?>
<div class="page-header">
        <div class="container page-header-content">
            <h1>Links</h1>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">           
            <div class="col-lg-12">
                <div class="content-section">
                    <h2>Affiliations</h2>
                    <p><a href="http://www.apa.org/" target="_blank">American Psychological Association</a> 
                    - ContinuingEdCourses.Net is an <a href="http://www.apa.org/education/ce/sponsors.aspx?item=2#california" target="_blank">APA 
                    Approved course sponsor</a>.</p>
                    <p><a href="http://www.aswb.org/" target="_blank">Association of Social Work Boards</a> 
                    - ContinuingEdCourses.Net is an <a href="https://www.datapathdesign.com/ASWB/ACEdswb/Prod/cgi-bin/ACESearchDSWBDLL.dll/acesShowProviders" target="_blank">ASWB 
                    Approved course sponsor</a>.</p>
                    <p><a href="http://www.nbcc.org/" target="_blank">National Board for Certified Counselors</a> 
                    - ContinuingEdCourses.Net is an <a href="http://www.nbcc.org/ACEPdirectory/List" target="_blank">NBCC 
                    Approved course sponsor</a>.</p>
                    <p><a href="http://www.bbs.ca.gov/" target="_blank">California Board of Behavioral 
                    Sciences</a> - ContinuingEdCourses.Net is a <a href="http://www.bbs.ca.gov/pdf/forms/celist.pdf" target="_blank">CA-BBS 
                    Approved course sponsor</a>. </p>
                    <p><a href="http://cswmft.ohio.gov/Home.aspx" target="_blank">Ohio Counselor, Social Worker, 
                    &amp; Marriage and Family Therapist Board</a> - ContinuingEdCourses.Net 
                    is an <a href="http://cswmft.ohio.gov/Portals/0/CONT ED/provider.pdf" target="_blank">OH-CSWMFT 
                    Approved course sponsor</a>. </p>
                    <p><a href="http://www.cpapsych.org/" target="_blank">California Psychological 
                    Association</a> - Look for our ad in the <i>California Psychologist</i> 
                    magazine.</p>
                    <p><a href="http://www.sdpsych.org/" target="_blank">San Diego Psychological Association</a> 
                    - Look for our ad in the <i>San Diego Psychologist</i> magazine.</p>
                    <p><a href="http://www.flapsych.com/" target="_blank">Florida Psychological Association</a> 
                    - Look for our ad in the <i>Florida Psychologist</i> magazine.</p>
                    <p><a href="http://www.azpa.org/" target="_blank">Arizona Psychological Association</a> 
                    - Look for our ad in the <i>Arizona Psychologist</i> magazine.</p>
                    <p><a href="http://www.papsy.org/" target="_blank">Pennsylvania Psychological Association</a> 
                    - Look for our ad in the <i>Pennsylvania Psychologist</i> magazine.</p>
                    <p><a href="http://www.ohpsych.org/" target="_blank">Ohio Psychological Association</a> 
                    - Look for our ad in the <i>Ohio Psychologist</i> magazine.</p>
                    <p><a href="http://www.masspsych.org/" target="_blank">Massachusetts Psychological 
                    Association</a> - Look for our ad in the <i>Massachusetts Psychologist</i> 
                    magazine.</p>
                    <p><a href="http://www.marylandpsychology.org/" target="_blank">Maryland Psychological 
                    Association</a> - Look for our ad in the <i>Maryland Psychologist</i> 
                    magazine.</p>
                    <p><a href="http://www.naswil.org/" target="_blank">NASW Illinois</a> - Look for our 
                    ad in the <i>Social Work Networker</i> magazine.</p>
                    <p><a href="http://www.SocialWorkCoursesOnline.com/" target="_blank">SocialWorkCoursesOnline.com</a> 
                    - Our sister site for social workers.            </p>
                </div>

                <div class="content-section mt-4">
                    <h2>Link to Us</h2>
                    <p>If you wish to link to us, <a href="/links-to-us/">click here for 
                    some examples.</a></p>                 
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_static_links', 'custom_static_links_shortcode');


<?php

function custom_static_linktous_shortcode() {
    ob_start();
    ?>
   <div class="page-header">
    <div class="container page-header-content">
        <h1>Link to Us</h1>
    </div>
</div>

<div class="container mt-4">
    <div class="row">           
        <div class="col-lg-12">
            <div class="content-section">
                <h2>Link to Us</h2>              
                <p>Here are some examples of ways to link to us. Please feel free 
                  to copy the html and add it to your website. After you have placed 
                  the link, feel free to contact us with a request to link back to 
                  you. We will consider each request individually and will be especially 
                  willing to link to sites that are useful to our customers and which 
                  complement our site.</p>
                <p><a href="/">Continuing Education 
                  Courses on the Internet for Psychologists</a> - ContinuingEdCourses.Net 
                  provides APA Approved continuing education courses for psychologists 
                  entirely on the Internet. Courses include Law &amp; Ethics, Supervision, 
                  Aging and Long Term Care, Spousal Abuse, Personality Disorders, 
                  ADHD, Eating Disorders, Psychopharmacology, Pain Management, 
                  and more.</p>
                <p><a href="/">Psychology Continuing 
                  Education on the Internet</a> - ContinuingEdCourses.Net provides 
                  APA Approved continuing education courses for psychologists taken 
                  entirely on the Internet.</p>
                <p><a href="/">Continuing Education 
                  Courses on the Internet for Social Workers</a> - ContinuingEdCourses.Net 
                  provides ASWB and CA-BBS Approved continuing education courses for 
                  social workers entirely on the Internet. Courses include Law &amp; 
                  Ethics, Supervision, Aging and Long Term Care, Spousal Abuse, 
                  Personality Disorders, ADHD, Eating Disorders, Psychopharmacology, 
                  Pain Management, and more.</p>
                <p><a href="/">Social Work Continuing 
                  Education on the Internet</a> - ContinuingEdCourses.Net provides 
                  ASWB and CA-BBS Approved continuing education courses for social 
                  workers taken entirely on the Internet.</p>
                <p><a href="/">Continuing Education 
                  Courses on the Internet for MFTs</a> - ContinuingEdCourses.Net provides 
                  CA-BBS Approved continuing education courses for marriage and family 
                  therapists entirely on the Internet. Courses include Law &amp; Ethics, 
                  Supervision, Aging and Long Term Care, Spousal Abuse, Personality 
                  Disorders, ADHD, Eating Disorders, Psychopharmacology, Pain Management, 
                  and more.</p>
                <p><a href="/">MFT Continuing Education 
                  on the Internet</a> - ContinuingEdCourses.Net provides CA-BBS Approved 
                  continuing education courses for marriage and family therapists 
                  taken entirely on the Internet.</p>
                <p><a href="/">Continuing Education 
                  Courses on the Internet for Mental Health Professionals</a> - ContinuingEdCourses.Net 
                  provides Nationally Approved continuing education courses for mental 
                  health professionals entirely on the Internet. Courses include Law 
                  &amp; Ethics, Aging and Long Term Care, Spousal Abuse, Personality 
                  Disorders, ADHD, Eating Disorders, Psychopharmacology, Pain Management, 
                  and more.</p>
                <p><b>If you wish to link to specific courses</b>, here are some examples 
                  of how to do it:</p>
                <p><a href="courses/course036.php">"You 
                  Can't Make Me!" - Effective Techniques for Managing Highly Resistant 
                  Clients by Clifton Mitchell, Ph.D.</a>, at <a href="/">ContinuingEdCourses.Net</a></p>
                <p><a href="courses/course047.php">"Making 
                  Up Is Hard To Do" - Couples Therapy After Infidelity by Steven D. 
                  Solomon, Ph.D. and Lorie J. Teagno, Ph.D.</a>, at <a href="/">ContinuingEdCourses.Net</a></p>
                <p><a href="courses/course049.php">"What 
                  Should I Do?" - 38 Ethical Dilemmas Involving Confidentiality by 
                  Gerald P. Koocher, Ph.D. and Patricia Keith-Spiegel, Ph.D.</a>, 
                  at <a href="/">ContinuingEdCourses.Net</a></p>
                <p><a href="courses/course050.php">"What 
                  Should I Do?" - Making Ethical Decisions and Taking Action by Gerald 
                  P. Koocher, Ph.D. and Patricia Keith-Spiegel, Ph.D.</a>, at <a href="/">ContinuingEdCourses.Net</a></p>
                <p>&nbsp;</p>
            </div>
        </div>
    </div>
</div>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_static_linktous', 'custom_static_linktous_shortcode');


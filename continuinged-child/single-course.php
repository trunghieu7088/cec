<?php
/**
 * Template for displaying a single LifterLMS Course
 *
 * @package YourTheme
 */

get_header();

// Get course data using the custom function
$course_id = get_the_ID();
$course_data = my_lifterlms_courses()->get_single_course_data($course_id);

if (!$course_data) {
    echo '<p>Course not found.</p>';
    get_footer();
    exit;
}

// Extract course data
$course_title = $course_data['post_title'];
$course_content = $course_data['post_content'];
//$course_main_content = $course_data['coursemaincontent'];
$course_main_content = $course_data['post_content'];
$course_excerpt = $course_data['post_excerpt'];
$ce_hours = $course_data['llmscehours'] ?? '';
$course_objectives = $course_data['courseobjectives'] ?? '';
$course_outline = $course_data['courseoutline'] ?? '';
$course_introduction = $course_data['courseintroduction'] ?? '';
$last_revised = $course_data['courselastrevised'] ?? '';
$copyright_info = $course_data['coursecopyright'] ?? '';
$instructors = $course_data['instructors'] ?? array();
$categories = $course_data['course_categories'] ?? array();
$difficulties = $course_data['course_difficulties'] ?? array();
$access_plan = $course_data['access_plans'] ?? array();
$author_list_page_url = get_custom_page_url_by_template('page-author-list.php');
$quiz_page = get_custom_page_url_by_template('page-quiz-test.php');

// Get price from access plan
$price = '$0';
if (!empty($access_plan) && is_object($access_plan)) {
    $price = $access_plan->get_price('price');
}

// Get difficulty level
$difficulty_level = !empty($difficulties) ? $difficulties[0]['name'] : 'Intermediate Level';

// Get category name
$category_name = !empty($categories) ? $categories[0]['name'] : 'General';
?>

<div class="container">
    <!-- Course Header -->
    <div class="course-header ">
        <h1 class="course-title"><?php echo esc_html($course_title); ?></h1>
        
        <?php if (!empty($instructors)): ?>
            <p class="course-author">
                by 
                <?php 
                $instructor_links = array();
                foreach ($instructors as $instructor) {
                    $degrees = !empty($instructor['llms_degrees_certs']) ? ', ' . $instructor['llms_degrees_certs'] : '';
                    $instructor_links[] = '<strong><a style="text-decoration:underline;" href="' . esc_url($author_list_page_url . '#' . $instructor['user_login']) . '">' . 
                                         esc_html($instructor['display_name'] . $degrees) . '</a></strong>';
                }
                echo implode(' & ', $instructor_links);
                ?>
            </p>
        <?php endif; ?>
        
        <?php if (!empty($instructors)): ?>
            <div class="instructor-images-course-detail">
            <?php foreach ($instructors as $instructor): ?>
                <img src="<?php echo esc_url($instructor['avatar']); ?>" 
                     alt="<?php echo esc_attr($instructor['display_name']); ?>" 
                     class="author-image-course-detail">
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="course-info-box">
            <h3>Course Information</h3>
            <div class="course-details">
                <?php if ($ce_hours): ?>
                <div class="detail-item">
                    <div class="detail-label">CE Hours</div>
                    <div class="detail-value"><?php echo esc_html($ce_hours); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($price): ?>
                <div class="detail-item">
                    <div class="detail-label">Price</div>
                    <div class="detail-value"><?php echo $price; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if ($last_revised): ?>
                <div class="detail-item">
                    <div class="detail-label">Last Revised</div>
                    <div class="detail-value"><?php echo esc_html($last_revised); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="action-buttons">
            <a href="#learning-objective-section" class="btn-custom">Take the Course</a>
            <a href="<?php echo esc_url($quiz_page . get_post_field('post_name', $course_id)); ?>" class="btn-custom">Take the Test</a>
            <a href="<?php echo get_custom_page_url_by_template('page-account.php'); ?>" class="btn-custom">Print Certificate</a>
        </div>

        <?php if ($copyright_info): ?>
        <p style="text-align: center; font-size: 0.9rem; color: #666; margin-top: 20px;">
            <?php echo wp_kses_post($copyright_info); ?>
        </p>
        <?php endif; ?>
    </div>


    <!-- Accreditation Section -->
     <!-- xem xet xai shortcode co san -->       
      <?php echo do_shortcode('[accreditation_section]'); ?>

    <?php if ($course_objectives): ?>
    <!-- Learning Objectives -->
    <div class="content-card ">
        <h2 id="learning-objective-section" class="section-title">Learning Objectives</h2>
        
        <p style="font-size: 1.05rem; margin-bottom: 20px;">
            This is an introductory-level course. Upon completing this course, mental health professionals will be able to:
        </p>

        <div class="learning-objectives">
            <?php echo wp_kses_post($course_objectives); ?>
        </div>

        <div class="warning-box">
            <strong>Important Note:</strong> This course utilizes the most accurate information available to the author at the time of writing.
        </div>

        <div class="warning-box">
            <strong>Content Warning:</strong> Completing this course may evoke disturbing feelings in readers due to the sensitive nature of topics such as death, grief, and complicated bereavement. If these feelings endure and/or become pronounced, readers may wish to seek supervision, consultation, or personal therapy.
        </div>
    </div>
    <?php endif; ?>

    <?php if ($course_outline): ?>
    <!-- Course Outline -->
    <div class="content-card ">
        <h2 class="section-title">Course Outline</h2>
        
        <div class="course-outline">
            <?php echo wp_kses_post($course_outline); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($course_introduction): ?>
    <!-- Course Introduction -->
    <div class="content-card ">
        <h2 class="section-title">Introduction</h2>
        
        <div class="content-text">
            <?php echo wp_kses_post($course_introduction); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($course_main_content): ?>
    <!-- Course Main Content -->
    <div class="content-card ">
        <h2 class="section-title">Course Content</h2>
        
        <div class="content-text">
            <?php echo wp_kses_post($course_main_content); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Call to Action -->
    <div class="content-card " style="background: var(--primary-blue); color: white; text-align: center;">
        <h2 style="color: white; border-bottom: none; margin-bottom: 20px;">Ready to Begin?</h2>
        <p style="font-size: 1.1rem; margin-bottom: 30px;">Start your continuing education journey today</p>
        <a href="<?php echo esc_url($quiz_page . get_post_field('post_name', $course_id)); ?>" 
           class="btn-custom" 
           style="background: white; color: var(--primary-blue);">Take the Test</a>
    </div>
</div>

<?php
get_footer();
?>
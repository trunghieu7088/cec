<?php
/*
Template Name: Quiz Page
*/

$course_slug = get_query_var('course_quiz_slug') ? get_query_var('course_quiz_slug') : '';

if (empty($course_slug)) {
    wp_die('Not found the course', 'Error', array('response' => 404));
}

$course = get_posts(array(
    'post_type' => 'course',
    'name' => $course_slug,
    'posts_per_page' => 1,
    'post_status' => 'publish'
));

if (!$course) {    
    wp_die('Not found the course', 'Error', array('response' => 404));
}

$course_id = $course[0]->ID;

// Get course data
$course_data = my_lifterlms_courses()->get_single_course_data($course_id);

if (!$course_data) {
    wp_die('Course data not found', 'Error', array('response' => 404));
}

// Extract course data
$course_title = $course_data['post_title'];
$copyright_info = $course_data['coursecopyright'] ?? '';
$instructors = $course_data['instructors'] ?? array();
$ce_hours = $course_data['ce_hours'] ?? '';
$price = $course_data['price'] ?? '';
$last_revised = $course_data['last_revised'] ?? '';

// Get author list page URL
$author_list_page_url = get_custom_page_url_by_template('page-author-list.php');

// Get quiz page URL
$quiz_page = get_custom_page_url_by_template('page-quiz-test.php');

// Get quiz questions
$course_data_manager = CourseLessonData::get_instance();
$question_list = $course_data_manager->get_course_structured_data($course_id);

if (!$question_list) {
    wp_die('No questions found for this course', 'Error', array('response' => 404));
}
$quiz_nonce = wp_create_nonce('quiz_submit_nonce');

get_header();

?>

<!-- Course Header -->
<section class="container course-header" style="padding:20px;">
    <div class="container">
        <h1 class="course-title"><?php echo esc_html($course_title); ?> - Test</h1>
        
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
            <h3>Test Information</h3>
            <p><?php echo count($question_list). ' Questions, No Time Limit, Can Retake '; ?></p>           
        </div>    
    </div>
     <div class="action-buttons">
            <a href="<?php echo get_permalink($course_id); ?>" class="btn-custom">Back to Course</a>
            <a href="javascript:void(0)" id="trigger-score-the-test" class="btn-custom"><i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i> Score the Test</a>
            <a href="#" class="btn-custom">Print Certificate</a>
        </div>
         <?php if ($copyright_info): ?>
        <p style="text-align: center; font-size: 0.9rem; color: #666; margin-top: 10px;">
            <?php echo wp_kses_post($copyright_info); ?>
        </p>
        <?php endif; ?>
</section>

<!-- Quiz Content -->
<section class="course-content">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-12">
                <!-- Important Notice -->
                <div class="content-section note-section">
                    <p>
                        <i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i>
                        <strong>Please note:</strong> Printing this page does not constitute proof of completion of the course. After successfully completing this test, you may purchase the course and print your Certificate of Completion immediately, print it later, or have it mailed to you.
                    </p>
                </div>

                <!-- Quiz Form -->
                <form id="quiz-form" class="content-section">
                    <input type="hidden" id="course-id" value="<?php echo esc_attr($course_id); ?>">
                    <input type="hidden" id="quiz-nonce" value="<?php echo esc_attr($quiz_nonce); ?>">
                    <h2 id="start-course-test" class="mb-4"><i class="bi bi-pencil-square" style="margin-right: 0.5rem;"></i>Course Test</h2>
                    
                    <?php foreach ($question_list as $index => $question): ?>
                    <div class="quiz-question" data-question-id="<?php echo esc_attr($question['question_id']); ?>">
                        <div class="question-header">
                            <span class="question-number">Question <?php echo ($index + 1); ?></span>
                        </div>
                        
                        <div class="question-title">
                            <?php echo esc_html($question['question_title']); ?>
                        </div>
                        
                        <?php if (!empty($question['question_content'])): ?>
                        <div class="question-content">
                            <?php echo wp_kses_post($question['question_content']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="question-choices">
                            <?php foreach ($question['choices'] as $choice): ?>
                            <div class="choice-item">
                                <label class="choice-label">
                                    <input type="radio" 
                                            name="question_<?php echo esc_attr($question['question_id']); ?>" 
                                            value="<?php echo esc_attr($choice['id']); ?>"
                                            class="choice-radio"
                                        >
                                    <span class="choice-marker"><?php echo esc_html($choice['marker']); ?></span>
                                    <span class="choice-text"><?php echo esc_html($choice['choice']); ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="question-feedback" style="display: none;"></div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="quiz-actions mb-4">
                        <button type="submit" id="submit-test-btn" class="btn-custom" style="max-width: 300px;">
                            <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>
                            Score the Test
                        </button>
                    </div>
                    
                    <!-- Quiz Results (Hidden initially) -->
                    <div id="quiz-results" class="quiz-results" style="display: none;">
                        <div class="results-card">
                            <h3 style="color:#ffffff;"><i class="bi bi-trophy" style="margin-right: 0.5rem;"></i>Test Results</h3>
                            <div class="results-content">
                                <div class="score-display">
                                    <span class="score-label">Your Score:</span>
                                    <span class="score-value" id="score-value">0%</span>
                                </div>
                                <div class="score-details">
                                    <p><strong>Correct:</strong> <span id="correct-count">0</span> / <?php echo count($question_list); ?></p>
                                    <p><strong>Incorrect:</strong> <span id="incorrect-count">0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>
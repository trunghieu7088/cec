<?php
/*
Template Name: Quiz Page
*/

$course_slug = get_query_var('course_slug') ? get_query_var('course_slug') : '';

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
<section class="course-header">
    <div class="container">
        <h1><?php echo esc_html($course_title); ?> - Test</h1>
        <div class="course-meta">
            <?php if (!empty($instructors)): ?>
            <div class="meta-item">
                <i class="bi bi-person"></i>
                <span>
                    <?php 
                    $instructor_names = array();
                    foreach ($instructors as $instructor) {
                        $degrees = !empty($instructor['llms_degrees_certs']) ? ', ' . $instructor['llms_degrees_certs'] : '';
                        $instructor_names[] = $instructor['display_name'] . $degrees;
                    }
                    echo esc_html(implode(' & ', $instructor_names));
                    ?>
                </span>
            </div>
            <?php endif; ?>
            
            <div class="meta-item">
                <i class="bi bi-question-circle"></i>
                <span><?php echo count($question_list); ?> Questions</span>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html($course_title); ?></li>                
            </ol>
        </nav>
    </div>
</section>

<!-- Quiz Content -->
<section class="course-content">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Important Notice -->
                <div class="content-section" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                    <p style="margin-bottom: 0; color: #856404;">
                        <i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i>
                        <strong>Please note:</strong> Printing this page does not constitute proof of completion of the course. After successfully completing this test, you may purchase the course and print your Certificate of Completion immediately, print it later, or have it mailed to you.
                    </p>
                </div>

                <!-- Quiz Form -->
                <form id="quiz-form" class="content-section">
                    <input type="hidden" id="course-id" value="<?php echo esc_attr($course_id); ?>">
                    <input type="hidden" id="quiz-nonce" value="<?php echo esc_attr($quiz_nonce); ?>">
                    <h2><i class="bi bi-pencil-square" style="margin-right: 0.5rem;"></i>Course Test</h2>
                    
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
                    
                    <div class="quiz-actions">
                        <button type="submit" id="submit-test-btn" class="btn-enroll" style="max-width: 300px;">
                            <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>
                            Submit Test
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

                <?php if ($copyright_info): ?>
                <!-- Copyright Section -->
                <div class="content-section">
                    <h2>Copyright Information</h2>
                    <div class="references">
                        <?php echo wp_kses_post($copyright_info); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <?php if (!empty($instructors)): ?>
                    <!-- Authors -->
                    <div class="sidebar-card">
                        <h3>About the <?php echo count($instructors) > 1 ? 'Authors' : 'Author'; ?></h3>
                        <?php foreach ($instructors as $index => $instructor): 
                            $author_list_page_url = get_custom_page_url_by_template('page-author-list.php');
                        ?>
                        <div class="about-author-wrapper">
                            <a href="<?php echo $author_list_page_url.'#'.$instructor['user_login']; ?>">     
                                <div class="img-wrapper">                            
                                    <img src="<?php echo esc_url($instructor['avatar']); ?>" 
                                    alt="<?php echo esc_attr($instructor['display_name']); ?>">
                                </div>                                                                                                                                         
                            </a>
                            <div class="author-info">
                                <p>
                                    <a href="<?php echo $author_list_page_url.'#'.$instructor['user_login']; ?>">
                                        <strong>
                                            <?php echo esc_html($instructor['display_name']); ?>
                                            <?php if (!empty($instructor['llms_degrees_certs'])): ?>, 
                                                <?php echo esc_html($instructor['llms_degrees_certs']); ?>
                                            <?php endif; ?>
                                        </strong>
                                    </a>
                                    <?php if (!empty($instructor['llms_instructor_bio'])): ?>
                                     <?php echo wp_strip_all_tags(wp_trim_words($instructor['llms_instructor_bio'],30,'..')); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Test Info -->
                    <div class="sidebar-card">
                        <h3><i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i>Test Information</h3>
                        <ul>
                            <li><i class="bi bi-question-circle"></i> <?php echo count($question_list); ?> Questions</li>                            
                            <li><i class="bi bi-clock"></i> No Time Limit</li>
                            <li><i class="bi bi-arrow-clockwise"></i> Can Retake</li>
                        </ul>
                    </div>

                    <!-- Quick Tips -->
                    <div class="sidebar-card" style="background: #e3f2fd; border-left-color: var(--secondary-color);">
                        <h3><i class="bi bi-lightbulb" style="margin-right: 0.5rem;"></i>Test Tips</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="padding: 0.5rem 0; border: none;">
                                <i class="bi bi-check2" style="color: var(--accent-color); margin-right: 0.5rem;"></i>
                                Read each question carefully
                            </li>
                            <li style="padding: 0.5rem 0; border: none;">
                                <i class="bi bi-check2" style="color: var(--accent-color); margin-right: 0.5rem;"></i>
                                Select only one answer per question
                            </li>
                            <li style="padding: 0.5rem 0; border: none;">
                                <i class="bi bi-check2" style="color: var(--accent-color); margin-right: 0.5rem;"></i>
                                Review all answers before submitting
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>
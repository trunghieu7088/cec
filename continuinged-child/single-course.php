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
$course_main_content=$course_data['coursemaincontent'];
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
$quiz_page=get_custom_page_url_by_template('page-quiz-test.php');

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

   
    <!-- Course Header -->
    <section class="course-header">
        <div class="container">
            <h1><?php echo esc_html($course_title); ?></h1>
            <?php if ($course_excerpt): ?>
                <p class="lead"><?php echo esc_html($course_excerpt); ?></p>
            <?php endif; ?>
            <div class="course-meta">
                <?php if ($ce_hours): ?>
                <div class="meta-item">
                    <i class="bi bi-clock"></i>
                    <span><?php echo esc_html($ce_hours); ?> CE Hours</span>
                </div>
                <?php endif; ?>
                
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
                    <i class="bi bi-bar-chart"></i>
                    <span><?php echo esc_html($difficulty_level); ?></span>
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
                    <li class="breadcrumb-item active" aria-current="page"><?php echo  esc_html($course_title); ?> </li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Course Content -->
    <section class="course-content">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <?php if ($copyright_info || $last_revised): ?>
                    <!-- References Section -->
                    <div class="content-section">
                        <h2>Additional Information</h2>
                        <div class="references">
                            <?php if ($last_revised): ?>
                                <p><strong>Last Revised:</strong> <?php echo esc_html($last_revised); ?></p>
                            <?php endif; ?>
                            <?php if ($copyright_info): ?>
                                <?php echo wp_kses_post($copyright_info); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($course_objectives): ?>
                    <!-- Learning Objectives -->
                    <div class="content-section">
                        <h2>Learning Objectives</h2>
                        <?php echo wp_kses_post($course_objectives); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($course_outline): ?>
                    <!-- Outline section -->
                    <div class="content-section">
                        <h2>Outline</h2>
                        <?php echo wp_kses_post($course_outline); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($course_introduction): ?>
                    <!-- Course Description -->
                    <div class="content-section">
                        <h2>Introduction</h2>
                        <?php echo wp_kses_post($course_introduction); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($course_main_content): ?>
                    <!-- Course Content Sections -->
                    <div class="content-section">
                        <?php  echo wp_kses_post($course_main_content); ?>
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
                            <?php foreach ($instructors as $index => $instructor): ?>
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
                        
                        <!-- Course Info -->
                        <div class="sidebar-card">
                            <h3>Course Information</h3>
                            <ul>
                               <!-- <li><i class="bi bi-journal-check"></i> Course #<?php echo esc_html($course_id); ?></li> -->
                                <?php if (!empty($categories)): ?>
                                <li><i class="bi bi-bookmark"></i> Category: <?php echo esc_html($category_name); ?></li>
                                <?php endif; ?>
                                <?php if ($ce_hours): ?>
                                <li><i class="bi bi-clock"></i> <?php echo esc_html($ce_hours); ?> CE Hours</li>
                                <?php endif; ?>
                                <li><i class="bi bi-bar-chart"></i> <?php echo esc_html($difficulty_level); ?></li>
                                <li><i class="bi bi-award"></i> ASWB Approved</li>
                                <li><i class="bi bi-patch-check"></i> NBCC Approved</li>
                            </ul>
                            <?php if ($price): ?>
                            <div class="price-tag"><?php echo $price; ?></div>
                            <?php endif; ?>
                            <a href="<?php echo $quiz_page.get_post_field( 'post_name', $course_id );  ?>" class="btn-enroll">Take The Test</a>                         
                            <p class="text-center mt-3" style="font-size: 0.9rem; color: var(--text-light);">
                                Pay only after you pass the test
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

 
<?php
get_footer();
?>
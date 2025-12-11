<?php
function llms_custom_course_list_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'limit' => -1,
        'course_type'=>'course',
    ), $atts, 'llms_course_list');
    
    ob_start();
    ?>
    
    <div class="container">
        <?php if($atts['course_type']=='course'): ?>
        <h1 class="main-title mt-4">Courses</h1>

        <!-- Intro Box -->
        <div class="intro-box">
            <p>ContinuingEdCourses.Net, Inc. is approved by the <a href="<?php echo site_url('accreditation.php'); ?>">APA, ASWB, NBCC, and NYSED</a> as a provider of continuing education. Feel free to contact us if you have any questions at 858-484-4304 or <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>. Although we may provide guidance, it is your responsibility to verify your continuing education requirements with your licensing board.</p>
        </div>

        <!-- Info Banner -->
        <div class="info-banner">
            <h3>Click on a course to view it for free. You pay only after you pass the test.</h3>
        </div>
        <?php else: ?>
            <h1 class="main-title mt-4">North Carolina Jurisprudence Exams</h1>

        <!-- Intro Box -->
        <div class="intro-box">
            <p>
                ContinuingEdCourses.Net, Inc. is approved by the North Carolina Board of Licensed Clinical Mental Health Counselors (NCBLCMHC) to provide these Jurisprudence Exams. When renewing your license you must provide a Certificate of Completion to the Board as proof of having passed the appropriate exam.
                Feel free to contact us if you have any questions at <strong>858-484-4304</strong> or <a href="mailto:Contact@ContinuingEdCourses.Net">Contact@ContinuingEdCourses.Net</a>. Although we may provide guidance, it is your responsibility to verify your continuing education requirements with your licensing board.
            </p>
        </div>

        <!-- Info Banner -->
        <div class="note-box mt-4">                   
            <strong>Note:</strong> Be sure to choose the appropriate exam for your license type. You pay for your certificate only after you pass the exam.
        </div>
        <?php endif; ?>

        <?php          
        //init currency
        $llms_currency_symbol = get_lifterlms_currency_symbol();  
        $author_list_page_url = get_custom_page_url_by_template('page-author-list.php');       
        
        // Xử lý riêng cho North Carolina courses (không có category)
        if ($atts['course_type'] == 'ncourse') {
            $filtered_courses = my_lifterlms_courses()->get_courses(array(
                'post_status'    => 'publish',
                'posts_per_page' => $atts['limit'],
                'orderby'        => 'title',  
                'order'          => 'ASC',
                'course_type'    => 'ncourse',
            ));
            
            if (!empty($filtered_courses)) {
                ?>
                <div class="category-section">
                     <h2 class="category-header">North Carolina Board of Licensed Clinical Mental Health Counselors Jurisprudence Exams</h2>
                    <div class="course-list">
                        <?php foreach($filtered_courses as $course_item): ?>
                            <div class="course-item">
                                <?php if($course_item['statusupdatelabel']): ?>
                                    <span class="course-badge badge-updated">
                                        <?php echo ucwords($course_item['statusupdatelabel']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url($course_item['course_link']); ?>" class="course-title-list">
                                    <?php echo esc_html($course_item['post_title']); ?>
                                </a>
                                
                                <div class="course-meta">
                                    <div class="course-author-list">
                                        <?php 
                                        $instructors_list = $course_item['instructors'];
                                        $total_instructors = count($instructors_list);
                                        $count = 0;                                         
                                        foreach($instructors_list as $course_instructor) {   
                                            $count++;                                                                                       
                                            echo '<a href="' . $author_list_page_url . '#' . $course_instructor['user_login'] . '">';
                                            echo esc_html($course_instructor['display_name'] . ' ' . $course_instructor['llms_degrees_certs']); 
                                            echo '</a>';
                                            if ($count < $total_instructors) {
                                                echo ' and ';
                                            }                                                    
                                        }
                                        ?>                                        
                                    </div>
                                    <div class="course-price">
                                        <?php if (!empty($course_item['llmscehours'])) : ?>
                                            <?php echo $course_item['llmscehours']; ?> CE Hours: 
                                        <?php endif; ?>
                                        <?php echo $llms_currency_symbol . $course_item['access_plans']->price; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
            }
        } else {
            // Xử lý bình thường cho regular courses (theo categories)
            $categories = get_terms(array(
                'taxonomy' => 'course_cat',
                'hide_empty' => true,
                'meta_key' => 'order',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));
            
            if (empty($categories) || is_wp_error($categories)) {
                $categories = get_terms(array(
                    'taxonomy' => 'course_cat',
                    'hide_empty' => true,
                    'orderby' => 'name',
                    'order' => 'DESC',
                ));
            }
            
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    $category_icon = get_term_meta($category->term_id, 'icon', true);                                    
                    $filtered_courses = my_lifterlms_courses()->get_courses(array(
                        'category_id'    => $category->term_id,
                        'post_status'    => 'publish',
                        'posts_per_page' => 10,     
                        'orderby'        => 'title',  
                        'order'          => 'ASC', 
                        'meta_key'       => '_category_order',
                        'course_type'    => $atts['course_type'], 
                    ));
                    
                    if (!empty($filtered_courses)) {
                        ?>
                        <!-- <?php echo esc_html($category->name); ?> Category -->
                        <div class="category-section">
                            <h2 class="category-header"><?php echo esc_html($category->name); ?></h2>
                            <div class="course-list">
                                <?php foreach($filtered_courses as $course_item): ?>
                                    <div class="course-item">
                                        <?php if($course_item['statusupdatelabel']): ?>
                                            <span class="course-badge badge-updated">
                                                <?php echo ucwords($course_item['statusupdatelabel']); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo esc_url($course_item['course_link']); ?>" class="course-title-list">
                                            <?php echo esc_html($course_item['post_title']); ?>
                                        </a>
                                        
                                        <div class="course-meta">
                                            <div class="course-author-list">
                                                <?php 
                                                $instructors_list = $course_item['instructors'];
                                                $total_instructors = count($instructors_list);
                                                $count = 0;                                         
                                                foreach($instructors_list as $course_instructor) {   
                                                    $count++;                                                     
                                                    echo '<a href="' . $author_list_page_url . '#' . $course_instructor['user_login'] . '">';
                                                    echo esc_html($course_instructor['display_name'] . ' ' . $course_instructor['llms_degrees_certs']); 
                                                    echo '</a>';
                                                    if ($count < $total_instructors) {
                                                        echo ' and ';
                                                    }                                                    
                                                }
                                                ?>                                        
                                            </div>
                                            <div class="course-price">
                                                <?php if (!empty($course_item['llmscehours'])) : ?>
                                                    <?php echo $course_item['llmscehours']; ?> CE Hours: 
                                                <?php endif; ?>
                                                <?php echo $llms_currency_symbol . $course_item['access_plans']->price; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }
        ?>
    </div>
    
    <?php
    return ob_get_clean();
}

add_shortcode('llms_course_list_custom', 'llms_custom_course_list_shortcode');
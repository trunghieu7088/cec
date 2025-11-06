<?php
/**
 * LifterLMS Course List Shortcode
 * Usage: [llms_course_list limit="10"]
 */

function llms_custom_course_list_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'limit' => -1, // -1 means all courses
    ), $atts, 'llms_course_list');
    
    ob_start();
    ?>
    
    <section id="courses" class="courses-section">
        <div class="container">
            <h2 class="section-title">Our Courses</h2>
            <p class="lead mb-5">Click on a course to view it for free. You pay only after you pass the test.</p>

            <?php
            // Get all course categories ordered by 'order' term meta
            $categories = get_terms(array(
                'taxonomy' => 'course_cat',
                'hide_empty' => true,
                'meta_key' => 'order',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));
            
            // If no order meta exists, fallback to name ordering
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
                    // Query courses for this category
                    $args = array(
                        'post_type' => 'course',
                        'post_status' => 'publish',
                        'posts_per_page' => $atts['limit'],
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'course_cat',
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
                        ),
                    );
                    
                    $courses = new WP_Query($args);
                    
                    if ($courses->have_posts()) {
                        ?>
                        <!-- <?php echo esc_html($category->name); ?> Category -->
                        <div class="category-card">
                            <div class="category-header">
                                <i class="bi bi-life-preserver category-icon"></i>
                                <h3 class="category-title"><?php echo esc_html($category->name); ?></h3>
                            </div>
                            
                            <?php
                            while ($courses->have_posts()) {
                                $courses->the_post();
                                $course_id = get_the_ID();
                                
                                // Get CE Hours
                                $ce_hours = get_post_meta($course_id, '_llms_ce_hours', true);
                                
                                // Get instructors
                                $instructors_data = get_post_meta($course_id, '_llms_instructors', true);
                                $instructor_names = array();
                                
                                if (!empty($instructors_data)) {
                                    $instructors_array = maybe_unserialize($instructors_data);
                                    
                                    if (is_array($instructors_array)) {
                                        foreach ($instructors_array as $instructor) {
                                            if (isset($instructor['id'])) {
                                                $user_id = $instructor['id'];
                                                $user_info = get_userdata($user_id);
                                                
                                                if ($user_info) {
                                                    $display_name = $user_info->display_name;
                                                    $degrees = get_user_meta($user_id, 'llms_degrees_certs', true);
                                                    
                                                    if (!empty($degrees)) {
                                                        $instructor_names[] = $display_name . ', ' . $degrees;
                                                    } else {
                                                        $instructor_names[] = $display_name;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                
                                $author_string = !empty($instructor_names) ? implode(' & ', $instructor_names) : get_the_author();
                                
                                // Get course URL
                                $course_url = get_permalink($course_id);
                                ?>
                                
                                <div class="course-item">
                                    <a href="<?php echo esc_url($course_url); ?>" class="course-title"><?php echo esc_html(get_the_title()); ?></a>
                                    <div class="course-meta">
                                        <span class="course-author"><?php echo esc_html($author_string); ?></span>
                                        <?php if (!empty($ce_hours)) : ?>
                                            <span>|</span>
                                            <span><?php echo esc_html($ce_hours); ?> CE Hours</span>
                                        <?php endif; ?>
                                        <span>|</span>
                                        <span class="course-price">$100</span>
                                    </div>
                                </div>
                                
                                <?php
                            }
                            ?>
                            
                        </div>
                        <?php
                    }
                    
                    wp_reset_postdata();
                }
            }
            ?>
            
        </div>
    </section>
    
    <?php
    return ob_get_clean();
}

add_shortcode('llms_course_list_custom', 'llms_custom_course_list_shortcode');
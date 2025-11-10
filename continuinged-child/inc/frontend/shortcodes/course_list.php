<?php
/**
 * Custom LifterLMS Course List Shortcode
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
              //init currency
            $llms_currency_symbol = get_lifterlms_currency_symbol();  
            $author_list_page_url = get_author_list_page_url();        
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
                    $category_icon=get_term_meta( $category->term_id, 'icon', true );                                    
                    $filtered_courses = my_lifterlms_courses()->get_courses( array(
                    'category_id'    => $category->term_id,
                    'post_status'    => 'publish',
                    'posts_per_page' => 10,     
                    'orderby'        => 'title',  
                    'order'          => 'ASC', 
                ) );
                    
                    if (! empty( $filtered_courses ) ) {
                        ?>
                        <!-- <?php echo esc_html($category->name); ?> Category -->
                        <div class="category-card">
                            <div class="category-header">
                                <?php if($category_icon): ?>
                                    <i class="category-icon bi <?php echo esc_attr( $category_icon ); ?>"></i>
                                <?php else: ?>
                                <i class="bi bi-life-preserver category-icon"></i>
                                <?php endif; ?>
                                <h3 class="category-title">                                    
                                   <?php 
                                         echo esc_html($category->name);                                     
                                    ?>                            
                                </h3>
                            </div>
                            
                           <?php foreach($filtered_courses as $course_item): ?>
                            <div class="course-item">                                   
                                    <?php if($course_item['statusupdatelabel']): ?>
                                            <span class="course-badge new"><?php echo ucwords($course_item['statusupdatelabel']); ?></span>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($course_item['course_link']); ?>" class="course-title"><?php echo esc_html($course_item['post_title']); ?></a>
                                    <div class="course-meta">
                                        <span class="course-author">
                                            <?php 
                                            $instructors_list = $course_item['instructors'];
                                            $total_instructors = count($instructors_list);
                                            $count = 0;                                         
                                                foreach( $instructors_list as $course_instructor)
                                                {   
                                                    $count++;                                                     
                                                    echo '<a href="'.$author_list_page_url.'#'.$course_instructor['user_login'].'">';
                                                    echo esc_html($course_instructor['display_name'].' '.$course_instructor['llms_degrees_certs']); 
                                                    echo '</a>';
                                                    if ($count < $total_instructors) {
                                                            echo ' and '; // Add & if not the last author
                                                    }                                                    
                                                }
                                            ?>                                        
                                        </span>
                                        <?php if (!empty($course_item['llmscehours'])) : ?>
                                            <span>|</span>
                                            <span><?php echo $course_item['llmscehours']; ?> CE Hours</span>
                                        <?php endif; ?>
                                        <span>|</span>                                                                            
                                        <span class="course-price"><?php echo $llms_currency_symbol.$course_item['access_plans']->price ; ?></span>
                                    </div>
                                </div>

                          <?php endforeach; ?>
                            
                        </div>
                        <?php
                    }
                    
                   
                }
            }
            ?>
      
        </div>
    </section>
    
    <?php
    return ob_get_clean();
}

add_shortcode('llms_course_list_custom', 'llms_custom_course_list_shortcode');

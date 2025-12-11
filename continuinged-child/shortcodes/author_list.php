<?php
function render_instructors_section() {
   
    $all_instructors = my_lifterlms_courses()->get_instructors_list();
    
    if (empty($all_instructors)) {
        return '<p>Not found any Authors</p>';
    }
    
    ob_start();
    ?>
    
    <div class="container">
        <!-- Page Title -->
        <div class="page-title-section">
            <h1 class="page-title">Authors</h1>
        </div>

        <?php 
        $instructor_count = count($all_instructors);
        $current = 0;
        foreach ($all_instructors as $instructor): 
            $current++;
        ?>
            <!-- Author Card -->
            <div class="author-card clearfix">
                <h2 class="author-name" id="<?php echo esc_attr($instructor['user_login']); ?>">
                    <?php echo esc_html($instructor['display_name']); ?>
                    <?php if (!empty($instructor['degrees_certs'])): ?>
                        , <?php echo esc_html($instructor['degrees_certs']); ?>
                    <?php endif; ?>
                </h2>
                
                <?php if (!empty($instructor['avatar_url'])): ?>
                    <img src="<?php echo esc_url($instructor['avatar_url']); ?>" 
                         alt="<?php echo esc_attr($instructor['display_name']); ?>" 
                         class="author-image author-image-list">
                <?php endif; ?>
                
                <div class="author-content">
                    <?php if (!empty($instructor['bio'])): ?>
                        <?php echo wpautop($instructor['bio']); ?>         
                                                    
                    <?php endif; ?>
                    
                    <?php if (!empty($instructor['website'])): ?>
                        <a href="<?php echo esc_url($instructor['website']); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="website-link">Visit Website</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($current < $instructor_count): ?>
                <hr class="author-divider">
            <?php endif; ?>
            
        <?php endforeach; ?>
    </div>    
    
    <?php
    return ob_get_clean();
}

add_shortcode('instructors_list', 'render_instructors_section');
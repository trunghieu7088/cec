<?php
function render_instructors_section() {
   
    $all_instructors = my_lifterlms_courses()->get_instructors_list();
    
  
    if (empty($all_instructors)) {
        return '<p>Not found any Authors</p>';
    }
    
    ob_start();
    ?>
    
    <section class="authors-section">
        <div class="container">
            <?php foreach ($all_instructors as $instructor): ?>
                <div class="author-card" id="<?php echo $instructor['user_login']; ?>">
                    <div class="author-header">
                        <div class="author-icon">
                            <?php if (!empty($instructor['avatar_url'])): ?>
                                <img src="<?php echo esc_url($instructor['avatar_url']); ?>" 
                                     alt="<?php echo esc_attr($instructor['display_name']); ?>" 
                                     class="author-avatar">
                            <?php else: ?>
                                <i class="bi bi-person-badge"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h2 class="author-name"><?php echo esc_html($instructor['display_name']); ?></h2>
                            <?php if (!empty($instructor['degrees_certs'])): ?>
                                <div class="author-credentials"><?php echo esc_html($instructor['degrees_certs']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="author-bio">
                        <?php if (!empty($instructor['bio'])): ?>
                           <?php echo $instructor['bio']; ?>
                        <?php endif; ?>                       
                        <?php if (!empty($instructor['website'])): ?>
                           <p> <a href="<?php echo esc_url($instructor['website']); ?>" 
                               class="author-link" 
                               target="_blank" 
                               rel="noopener noreferrer">
                                <i class="bi bi-globe"></i> Visit Website
                            </a> </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <?php
    return ob_get_clean();
}


add_shortcode('instructors_list', 'render_instructors_section');

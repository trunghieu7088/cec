<?php
/* add_action('save_post_lesson', function($post_id, $post, $update) {
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
   
    if ($post->post_type !== 'lesson') {
        return;
    }
   
    $post_content = $post->post_content;
  
    $post_content = str_replace(
        ['<!-- wp:llms/lesson-progression /-->', '<!-- wp:llms/lesson-navigation /-->'],
        '',
        $post_content
    );
   
    $additional_content = "\n<!-- wp:llms/lesson-progression /-->\n<!-- wp:llms/lesson-navigation /-->";
    $updated_content = $post_content . $additional_content;
 
    remove_action('save_post_lesson', __FUNCTION__);
    wp_update_post([
        'ID' => $post_id,
        'post_content' => $updated_content,
    ]);
    add_action('save_post_lesson', __FUNCTION__, 10, 3);
}, 10, 3); */
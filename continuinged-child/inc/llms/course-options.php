<?php
/**
 * Create meta box for course - WordPress 6.8+ Compatible
 */

add_action('add_meta_boxes', 'add_course_detail_metaboxes');
function add_course_detail_metaboxes() {
    add_meta_box(
        'course_details_meta',
        'Detail Course',
        'render_course_details_metabox',
        'course',
        'normal',
        'high'
    );
}

function render_course_details_metabox($post) {
    wp_nonce_field('course_details_nonce', 'course_details_nonce_field');
    
    $introduction = get_post_meta($post->ID, '_course_introduction', true);
    $objectives = get_post_meta($post->ID, '_course_objectives', true);
    $outline = get_post_meta($post->ID, '_course_outline', true);
    $main_content = get_post_meta($post->ID, '_course_main_content', true);
    $ce_hours = get_post_meta($post->ID, '_llms_ce_hours', true);
    $status_update_label= get_post_meta($post->ID, '_status_update_label', true);
    ?>
    
    <style>
        .course-details-fields .editor-wrapper {
            margin-bottom: 20px;
        }
        .course-details-fields .editor-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {
            $('#course_last_revised').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true
            });
        });
    </script>

   
    
    <div class="course-details-fields">

        <!-- North Carolina Course -->
        <div class="editor-wrapper">
            <label class="editor-label">
                <input type="checkbox" 
                    name="north_carolina_course" 
                    id="north_carolina_course" 
                    value="1"
                    <?php checked(get_post_meta($post->ID, '_north_carolina_course', true), '1'); ?> />
                North Carolina Course
            </label>
            <p class="description">Check this if this is a North Carolina specific course.</p>
        </div>

         <!-- CE Hours -->
        <div class="editor-wrapper">
            <label class="editor-label">CE Hours:</label>
            <input type="number" 
                name="llms_ce_hours" 
                id="llms_ce_hours" 
                value="<?php echo esc_attr($ce_hours); ?>" 
                step="0.1" 
                min="0"
                style="width: 150px; padding: 8px; font-size: 14px;" />
            <p class="description">Enter the number of Continuing Education hours for this course.</p>
        </div>

        <!-- Course Copyright -->
        <div class="editor-wrapper">
            <label class="editor-label">Course Copyright:</label>
            <input type="text" 
                name="course_copyright" 
                id="course_copyright" 
                value="<?php echo esc_attr(get_post_meta($post->ID, '_course_copyright', true)); ?>" 
                style="width: 100%; padding: 8px; font-size: 14px;" />
            <p class="description">Enter copyright information for this course.</p>
        </div>

        <!-- Course Last Revised -->
        <div class="editor-wrapper">
            <label class="editor-label">Course Last Revised:</label>
            <input type="text" 
                name="course_last_revised" 
                id="course_last_revised" 
                value="<?php echo esc_attr(get_post_meta($post->ID, '_course_last_revised', true)); ?>" 
                style="width: 300px; padding: 8px; font-size: 14px;" />
            <p class="description">Select the date and time when this course was last revised.</p>
        </div>


        <!-- status update label -->
        <div class="editor-wrapper">
            <label class="editor-label">Update Status Label</label>
            <input type="text" 
                name="status_update_label" 
                id="status_update_label" 
                placeholder="Updated!, Expanded!, New!"
                value="<?php echo esc_attr(get_post_meta($post->ID, '_status_update_label', true)); ?>" 
                style="width: 100%; padding: 8px; font-size: 14px;" />
            <p class="description">Enter the update status</p>
        </div>

        <!-- Category Order -->
        <div class="editor-wrapper">
            <label class="editor-label">Category Order:</label>
            <input type="number" 
                name="category_order" 
                id="category_order" 
                value="<?php echo esc_attr(get_post_meta($post->ID, '_category_order', true)); ?>" 
                step="1" 
                min="0"
                style="width: 150px; padding: 8px; font-size: 14px;" />
            <p class="description">Enter the order number for this course in category listing.</p>
        </div>

        <!-- Introduction -->
        <div class="editor-wrapper">
            <label class="editor-label">Introduction:</label>
            <?php 
            wp_editor($introduction, 'course_introduction', array(
                'textarea_name' => 'course_introduction',
                'textarea_rows' => 10,
                'teeny' => false,
                'media_buttons' => true,
                'quicktags' => true,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,link,unlink',
                    'toolbar2' => 'pastetext,removeformat,undo,redo',
                )
            )); 
            ?>
        </div>
        
        <!-- Learning Objectives -->
        <div class="editor-wrapper">
            <label class="editor-label">Learning Objectives:</label>
            <?php 
            wp_editor($objectives, 'course_objectives', array(
                'textarea_name' => 'course_objectives',
                'textarea_rows' => 8,
                'teeny' => false,
                'media_buttons' => false,
                'quicktags' => true,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,bullist,numlist,link,unlink',
                    'toolbar2' => 'undo,redo',
                )
            )); 
            ?>
        </div>
        
        <!-- Course Outline -->
        <div class="editor-wrapper">
            <label class="editor-label">Course Outline:</label>
            <?php 
            wp_editor($outline, 'course_outline', array(
                'textarea_name' => 'course_outline',
                'textarea_rows' => 10,
                'teeny' => false,
                'media_buttons' => false,
                'quicktags' => true,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,bullist,numlist,indent,outdent',
                    'toolbar2' => 'undo,redo',
                )
            )); 
            ?>
        </div>
        
        <!-- Main Content -->
        <div class="editor-wrapper">
            <label class="editor-label">Main Content (All Parts):</label>
            <?php 
            wp_editor($main_content, 'course_main_content', array(
                'textarea_name' => 'course_main_content',
                'textarea_rows' => 15,
                'teeny' => false,
                'media_buttons' => true,
                'quicktags' => true,
                'tinymce' => array(
                    'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,link,unlink',
                    'toolbar2' => 'pastetext,removeformat,charmap,hr,undo,redo,fullscreen',
                )
            )); 
            ?>
        </div>


          
    </div>
    
    <?php
}

// Save data
add_action('save_post_course', 'save_course_details_meta', 10, 3);
function save_course_details_meta($post_id, $post,$update) {
    // Security checks
    if (!isset($_POST['course_details_nonce_field'])) return;
    if (!$update) return;
    if (!wp_verify_nonce($_POST['course_details_nonce_field'], 'course_details_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    remove_action('save_post_course', 'save_course_details_meta', 10);

    // Save fields
    $fields = array(
        'course_introduction',
        'course_objectives', 
        'course_outline',
        'course_main_content',
        'llms_ce_hours',
        'course_copyright',
        'course_last_revised',
        'status_update_label',
        'category_order'
    );
    
    foreach($fields as $field) {
        if (isset($_POST[$field])) {
       
            $value = wp_kses_post($_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        } else {
            
            delete_post_meta($post_id, '_' . $field);
        }
    }

    if (isset($_POST['north_carolina_course']) && $_POST['north_carolina_course'] == '1') {
    update_post_meta($post_id, '_north_carolina_course', '1');
    } else {
        delete_post_meta($post_id, '_north_carolina_course');
    }
}

// Display function
function display_course_custom_content() {
    $course_id = get_the_ID();
    
    $intro = get_post_meta($course_id, '_course_introduction', true);
    $objectives = get_post_meta($course_id, '_course_objectives', true);
    $outline = get_post_meta($course_id, '_course_outline', true);
    $main = get_post_meta($course_id, '_course_main_content', true);
    $ce_hours = get_post_meta($course_id, '_llms_ce_hours', true);
    $copyright = get_post_meta($course_id, '_course_copyright', true);    
    $last_revised = get_post_meta($course_id, '_course_last_revised', true);
    $update_status=get_post_meta($course_id, '_status_update_label', true);
    $category_order = get_post_meta($course_id, '_category_order', true);
    $north_carolina = get_post_meta($course_id, '_north_carolina_course', true);

    if (!$intro && !$objectives && !$outline && !$main) {
        return;
    }
    
    echo '<div class="course-custom-content">';

    if($north_carolina) {
        echo '<div class="content-section">';
        echo '<h2>North Carolina Course</h2>';
        echo '<p>✓ This is a North Carolina specific course</p>';
        echo '</div>';
    }

    if($ce_hours) {
        echo '<div class="content-section">';
        echo '<h2>CE Hours</h2>';
        echo '<p>' . esc_html($ce_hours) . ' hours</p>';
        echo '</div>';
    }

    if($copyright) {
        echo '<div class="content-section">';
        echo '<h2>Copyright</h2>';
        echo '<p>' . esc_html($copyright) . '</p>';
        echo '</div>';
    }

    if($last_revised) {
        echo '<div class="content-section">';
        echo '<h2>Last Revised</h2>';
        echo '<p>' . date_i18n('F j, Y g:i A', strtotime($last_revised)) . '</p>';
        echo '</div>';
    }

    if($update_status) {
        echo '<div class="content-section">';
        echo '<h2>Update Status</h2>';
        echo '<p>' . esc_html($update_status) . '</p>';
        echo '</div>';
    }

      if($category_order) {
        echo '<div class="content-section">';
        echo '<h2>Category Order</h2>';
        echo '<p>' . esc_html($category_order) . '</p>';
        echo '</div>';
    }
    
    if($intro) {
        echo '<div class="content-section">';
        echo '<h2>Introduction</h2>';
        echo wp_kses_post($intro);
        echo '</div>';
    }
    
    if($objectives) {
        echo '<div class="content-section">';
        echo '<h2>Learning Objectives</h2>';
        echo wp_kses_post($objectives);
        echo '</div>';
    }
    
    if($outline) {
        echo '<div class="content-section">';
        echo '<h2>Outline</h2>';
        echo wp_kses_post($outline);
        echo '</div>';
    }
    
    if($main) {
        echo '<div class="content-section">';
        echo wp_kses_post($main);
        echo '</div>';
    }
    
    echo '</div>';
}

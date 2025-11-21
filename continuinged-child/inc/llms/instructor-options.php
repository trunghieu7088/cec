<?php
/**
 * 1. Display the custom 'Instructor Bio' (using wp_editor) and 'Instructor Website' fields 
 * on the Edit User screen.
 */
function add_instructor_custom_fields( $user ) {
    ?>
    
    <h3 id="llms-instructor-details">LifterLMS Instructor Details</h3>
    
    <table class="form-table">

     <tr>
            <th><label for="llms_instructor_cover_img">Image File Name</label></th>
            <td>
                <input 
                    type="text" 
                    name="llms_instructor_cover_img" 
                    id="llms_instructor_cover_img" 
                    value="<?php echo esc_attr( get_the_author_meta( 'llms_instructor_cover_img', $user->ID ) ); ?>" 
                    class="regular-text"
                    placeholder="e.g. drjim.png"
                />
                <br />
                <span class="description">
                    Enter the exact filename of the image uploaded to <code>/assets/author-image/</code> inside your child theme.
                </span>

                <?php 
                $img_name = get_the_author_meta( 'llms_instructor_cover_img', $user->ID );
                
                if ( ! empty( $img_name ) ) {
                    // Tạo đường dẫn URL tới folder trong child theme
                    $img_url = get_stylesheet_directory_uri() . '/assets/author-image/' . $img_name;
                    ?>
                    <br>
                    <div style="margin-top: 10px; background: #f1f1f1; padding: 10px; display: inline-block; border: 1px solid #ccc;">
                        <strong>Preview:</strong><br>
                        <img src="<?php echo esc_url( $img_url ); ?>" 
                             alt="Instructor Preview" 
                             style="max-width: 150px; height: auto; display: block; margin-top: 5px;" 
                             onerror="this.style.display='none'; this.insertAdjacentHTML('afterend', '<p style=\'color:red; margin:5px 0 0;\'>File not found in assets/author-image/</p>');"
                        />
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>

        
        <tr>
            <th><label for="llms_instructor_bio">Instructor Bio (Visual Editor)</label></th>
            <td>
                <?php 
                // Retrieve the saved value for the user
                $bio = get_the_author_meta( 'llms_instructor_bio', $user->ID );
               
                // Define settings for the wp_editor
                $editor_settings = array(
                    'textarea_name' => 'llms_instructor_bio', // MUST match the meta key/name
                    'editor_height' => 200, // Set the height
                    'media_buttons' => false, // Disable media buttons (optional)
                    'tinymce'       => array( // Configure TinyMCE
                        'toolbar1' => 'bold,italic,bullist,numlist,link,unlink,undo,redo', // Simple toolbar
                    ),
                    'quicktags'     => true, // Enable quick tags (HTML buttons)
                );

                // Display the WordPress visual editor
                wp_editor( $bio, 'llms_instructor_bio_editor', $editor_settings );
                ?>
                <span class="description">
                    Please enter the instructor's biography using the visual editor. **HTML formatting is fully supported**.
                </span>
            </td>
        </tr>
        
        <tr>
            <th><label for="llms_instructor_website">Instructor Website</label></th>
            <td>
                <input 
                    type="url" 
                    name="llms_instructor_website" 
                    id="llms_instructor_website" 
                    placeholder="https://example.com"
                    value="<?php echo esc_url( get_the_author_meta( 'llms_instructor_website', $user->ID ) ); ?>" 
                    class="regular-text code" 
                /><br />
                <span class="description">
                    Enter the URL of the instructor's personal website or portfolio.
                </span>
            </td>
        </tr>
        <tr>
            <th><label for="llms_degrees_certs">Degrees & Certifications</label></th>
            <td>
                 <input 
                    type="text" 
                    name="llms_degrees_certs" 
                    id="llms_degrees_certs" 
                    value="<?php echo  get_the_author_meta( 'llms_degrees_certs', $user->ID ) ; ?>" 
                    class="regular-text code" 
                /><br />                
                <span class="description">
                    List the instructor's relevant degrees and certifications.
                </span>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'edit_user_profile', 'add_instructor_custom_fields' );
add_action( 'show_user_profile', 'add_instructor_custom_fields' );


/**
 * 2. Save the data from both custom fields. 
 * The saving logic remains the same because wp_editor submits its content just like a textarea.
 */
function save_instructor_custom_fields( $user_id ) {
    
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    
    // --- Save Instructor Bio (Content from wp_editor) ---
    if ( isset( $_POST['llms_instructor_bio'] ) ) {
        // wp_editor content needs to be sanitized. wp_kses_post() is still the correct function.
        $bio_content =  $_POST['llms_instructor_bio'];
        update_user_meta( $user_id, 'llms_instructor_bio', $bio_content );
    }

    // --- Save Instructor Website ---
    if ( isset( $_POST['llms_instructor_website'] ) ) {
        $website_url = esc_url_raw( $_POST['llms_instructor_website'] );
        update_user_meta( $user_id, 'llms_instructor_website', $website_url );
    }

      // --- Save degrees and certification
    if ( isset( $_POST['llms_degrees_certs'] ) ) {
        
        $degree_cert =  sanitize_text_field($_POST['llms_degrees_certs']);
        update_user_meta( $user_id, 'llms_degrees_certs', $degree_cert );
    }

     // --- Save Image File Name ---
    if ( isset( $_POST['llms_instructor_cover_img'] ) ) {
        // Sanitize file name (remove special chars usually not allowed in filenames)
        $img_name = sanitize_file_name( $_POST['llms_instructor_cover_img'] );
        update_user_meta( $user_id, 'llms_instructor_cover_img', $img_name );
    }
}
add_action( 'personal_options_update', 'save_instructor_custom_fields' );
add_action( 'edit_user_profile_update', 'save_instructor_custom_fields' );
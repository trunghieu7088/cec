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

         <tr>
            <th><label for="llms_instructor_hide">Hide author</label></th>
            <td>
                <?php 
                $hide_author = get_the_author_meta( 'llms_instructor_hide', $user->ID );
                ?>
                <label>
                    <input 
                        type="checkbox" 
                        name="llms_instructor_hide" 
                        id="llms_instructor_hide" 
                        value="1"
                        <?php checked( '1', $hide_author ); ?>
                    />
                    Check this box to hide the instructor's details on the frontend.
                </label>
            </td>
        </tr>

        <tr>
            <th><label for="author_list_order">Author List Order</label></th>
            <td>
                 <input 
                    type="number" 
                    name="author_list_order" 
                    id="author_list_order" 
                    value="<?php echo esc_attr( get_the_author_meta( 'author_list_order', $user->ID ) ); ?>" 
                    class="regular-text code"
                    min="1" 
                    step="1"
                    placeholder="e.g. 10" 
                /><br>
                <span class="description">
                    Enter a number to control the display order (lower number means higher on the list). Default is 9999.
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

     // --- START: Save Hide Author Checkbox ---
    $hide_value = isset( $_POST['llms_instructor_hide'] ) ? '1' : '0';
    update_user_meta( $user_id, 'llms_instructor_hide', $hide_value );
    // --- END: Save Hide Author Checkbox ---

    if ( isset( $_POST['author_list_order'] ) ) {
        // Đảm bảo giá trị là số nguyên
        $list_order = absint( $_POST['author_list_order'] ); 
        update_user_meta( $user_id, 'author_list_order', $list_order );
    }
    else
    {
         update_user_meta( $user_id, 'author_list_order', 1 );
    }
}
add_action( 'personal_options_update', 'save_instructor_custom_fields' );
add_action( 'edit_user_profile_update', 'save_instructor_custom_fields' );

function update_all_instructors_with_list_order() {
    
    // 1. Chỉ cho phép Admin chạy chức năng này
    if ( ! current_user_can( 'manage_options' ) ) {
        return 'Permission denied.';
    }

    $args = array(
        'role'    => 'instructor', // Thay đổi nếu tên vai trò của bạn khác (ví dụ: 'llms_instructor')
        'fields'  => 'ID',         // Chỉ lấy ID để tiết kiệm tài nguyên
        'number'  => -1,           // Lấy tất cả user
    );

    // Lấy danh sách ID của tất cả Instructor
    $instructor_ids = get_users( $args );

    $updated_count = 0;
    $default_order = 9999; // Giá trị thứ tự mặc định

    if ( ! empty( $instructor_ids ) ) {
        foreach ( $instructor_ids as $user_id ) {
            
            // Lấy giá trị hiện tại của meta. Nếu không có, hàm get_user_meta sẽ trả về rỗng.
            $current_order = get_user_meta( $user_id, 'author_list_order', true );

            // Chỉ thêm/cập nhật nếu meta chưa tồn tại hoặc rỗng.
            if ( empty( $current_order ) ) {
                // Thêm meta mới. set_user_meta là hàm an toàn để thêm hoặc cập nhật.
                update_user_meta( $user_id, 'author_list_order', $default_order );
                $updated_count++;
            }
        }
    }

    return "✅ Hoàn thành cập nhật! Đã thêm meta 'author_list_order' cho $updated_count instructor.";
}
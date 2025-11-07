<?php
/**
 * Custom Avatar Upload for WordPress Admin
 * Add this code to your theme's functions.php or a custom plugin
 */

// Enqueue media uploader scripts
add_action('admin_enqueue_scripts', 'enqueue_avatar_uploader_scripts');

function enqueue_avatar_uploader_scripts($hook) {
    // Only load on user profile pages
    if ($hook !== 'profile.php' && $hook !== 'user-edit.php') {
        return;
    }
    
    // Enqueue WordPress media uploader
    wp_enqueue_media();
}

// Add custom avatar field to user profile page
add_action('show_user_profile', 'custom_avatar_field');
add_action('edit_user_profile', 'custom_avatar_field');

function custom_avatar_field($user) {
    ?>
    <h3><?php _e('Custom Avatar'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="custom_avatar"><?php _e('Profile Picture'); ?></label></th>
            <td>
                <?php
                $avatar_id = get_user_meta($user->ID, 'custom_avatar', true);
                $avatar_url = $avatar_id ? wp_get_attachment_url($avatar_id) : '';
                ?>
                
                <div id="custom-avatar-preview" style="margin-bottom: 10px;">
                    <?php if ($avatar_url): ?>
                        <img src="<?php echo esc_url($avatar_url); ?>" style="max-width: 150px; height: auto; border-radius: 50%;" />
                    <?php endif; ?>
                </div>
                
                <input type="hidden" id="custom_avatar" name="custom_avatar" value="<?php echo esc_attr($avatar_id); ?>" />
                
                <button type="button" class="button" id="upload_avatar_button">
                    <?php echo $avatar_url ? __('Change Avatar') : __('Upload Avatar'); ?>
                </button>
                
                <?php if ($avatar_url): ?>
                    <button type="button" class="button" id="remove_avatar_button"><?php _e('Remove Avatar'); ?></button>
                <?php endif; ?>
                
                <p class="description"><?php _e('Click to upload a custom avatar image.'); ?></p>
            </td>
        </tr>
    </table>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        // Upload avatar
        $('#upload_avatar_button').on('click', function(e) {
            e.preventDefault();
            
            // If media uploader exists, open it
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Create new media uploader
            mediaUploader = wp.media({
                title: '<?php _e('Choose Avatar'); ?>',
                button: {
                    text: '<?php _e('Use as Avatar'); ?>'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            // When image is selected
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#custom_avatar').val(attachment.id);
                $('#custom-avatar-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto; border-radius: 50%;" />');
                $('#upload_avatar_button').text('<?php _e('Change Avatar'); ?>');
                
                // Show remove button if not exists
                if ($('#remove_avatar_button').length === 0) {
                    $('#upload_avatar_button').after('<button type="button" class="button" id="remove_avatar_button"><?php _e('Remove Avatar'); ?></button>');
                }
            });
            
            mediaUploader.open();
        });
        
        // Remove avatar
        $(document).on('click', '#remove_avatar_button', function(e) {
            e.preventDefault();
            $('#custom_avatar').val('');
            $('#custom-avatar-preview').html('');
            $('#upload_avatar_button').text('<?php _e('Upload Avatar'); ?>');
            $(this).remove();
        });
    });
    </script>
    <?php
}

// Save custom avatar
add_action('personal_options_update', 'save_custom_avatar');
add_action('edit_user_profile_update', 'save_custom_avatar');

function save_custom_avatar($user_id) {
    // Check permissions
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // Update or delete avatar
    if (isset($_POST['custom_avatar']) && !empty($_POST['custom_avatar'])) {
        update_user_meta($user_id, 'custom_avatar', intval($_POST['custom_avatar']));
    } else {
        delete_user_meta($user_id, 'custom_avatar');
    }
}

// Override WordPress avatar with custom avatar
add_filter('get_avatar_url', 'custom_get_avatar_url', 10, 3);

function custom_get_avatar_url($url, $id_or_email, $args) {
    // Get user ID
    $user_id = null;
    
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif (is_object($id_or_email) && isset($id_or_email->user_id)) {
        $user_id = (int) $id_or_email->user_id;
    } elseif (is_string($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if ($user) {
            $user_id = $user->ID;
        }
    }
    
    // Get custom avatar if exists
    if ($user_id) {
        $avatar_id = get_user_meta($user_id, 'custom_avatar', true);
        if ($avatar_id) {
            $custom_url = wp_get_attachment_url($avatar_id);
            if ($custom_url) {
                return $custom_url;
            }
        }
    }
    
    return $url;
}

// Also filter the get_avatar function (HTML output)
add_filter('get_avatar', 'custom_get_avatar', 10, 5);

function custom_get_avatar($avatar, $id_or_email, $size, $default, $alt) {
    // Get custom avatar URL
    $custom_url = custom_get_avatar_url('', $id_or_email, array());
    
    // Check if custom avatar exists and is different from default
    if ($custom_url && strpos($custom_url, 'gravatar.com') === false) {
        $avatar = sprintf(
            '<img alt="%s" src="%s" class="avatar avatar-%d photo" height="%d" width="%d" />',
            esc_attr($alt),
            esc_url($custom_url),
            esc_attr($size),
            esc_attr($size),
            esc_attr($size)
        );
    }
    
    return $avatar;
}
?>
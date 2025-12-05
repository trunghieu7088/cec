<?php
/**
 * Register CE Contact Custom Post Type
 */
function register_cecontact_post_type() {
    $labels = array(
        'name'                  => 'CE Contacts',
        'singular_name'         => 'CE Contact',
        'menu_name'             => 'CE Contacts',
        'name_admin_bar'        => 'CE Contact',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Contact',
        'new_item'              => 'New Contact',
        'edit_item'             => 'Edit Contact',
        'view_item'             => 'View Contact',
        'all_items'             => 'All Contacts',
        'search_items'          => 'Search Contacts',
        'not_found'             => 'No contacts found.',
        'not_found_in_trash'    => 'No contacts found in Trash.'
    );

    $args = array(
        'labels'                => $labels,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-email',
        'capability_type'       => 'post',
        'capabilities'          => array(
            'create_posts' => false, // Prevents "Add New" button
        ),
        'map_meta_cap'          => true,
        'hierarchical'          => false,
        'supports'              => array('title'),
        'has_archive'           => false,
        'rewrite'               => false,
        'query_var'             => false,
    );

    register_post_type('cecontact', $args);
}
add_action('init', 'register_cecontact_post_type');

/**
 * Add custom columns to CE Contact list table
 */
function cecontact_custom_columns($columns) {
    $new_columns = array(
        'cb'         => $columns['cb'],
        'first_name' => 'First Name',
        'last_name'  => 'Last Name',
        'email'      => 'Email',
        'phone'      => 'Phone Number',
        'subject'    => 'Subject',
        'status'     => 'Status',
        'date'       => 'Date'
    );
    return $new_columns;
}
add_filter('manage_cecontact_posts_columns', 'cecontact_custom_columns');

/**
 * Populate custom columns with data
 */
function cecontact_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'first_name':
            echo esc_html(get_post_meta($post_id, '_cecontact_first_name', true));
            break;
        case 'last_name':
            echo esc_html(get_post_meta($post_id, '_cecontact_last_name', true));
            break;
        case 'email':
            $email = get_post_meta($post_id, '_cecontact_email', true);
            echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            break;
        case 'phone':
            echo esc_html(get_post_meta($post_id, '_cecontact_phone', true));
            break;
        case 'subject':
            echo esc_html(get_post_meta($post_id, '_cecontact_subject', true));
            break;
        case 'status': 
            $status = get_post_meta($post_id, '_cecontact_status', true);         
           // echo esc_html(ucfirst($status)); 
            if($status=='pending')
            {
                echo '<span style="padding:5px;background-color:#FFA500;color:#fff;border-radius:5px;">'.$status.'</span>';
            }
            else
            {
                echo '<span style="padding:5px;background-color:#228B22 ;color:#fff;border-radius:5px;">'.$status.'</span>';
            }
            break;
    }
}
add_action('manage_cecontact_posts_custom_column', 'cecontact_custom_column_content', 10, 2);

/**
 * Make columns sortable
 */
function cecontact_sortable_columns($columns) {
    $columns['first_name'] = 'first_name';
    $columns['last_name'] = 'last_name';
    $columns['email'] = 'email';
    $columns['subject'] = 'subject';
    return $columns;
}
add_filter('manage_edit-cecontact_sortable_columns', 'cecontact_sortable_columns');

/**
 * Add meta box for contact details
 */
function cecontact_add_meta_boxes() {
    add_meta_box(
        'cecontact_details',
        'Contact Details',
        'cecontact_details_callback',
        'cecontact',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cecontact_add_meta_boxes');

/**
 * Meta box callback function
 */
function cecontact_details_callback($post) {

    wp_nonce_field('cecontact_save_meta_data', 'cecontact_meta_nonce');
    $first_name = get_post_meta($post->ID, '_cecontact_first_name', true);
    $last_name = get_post_meta($post->ID, '_cecontact_last_name', true);
    $email = get_post_meta($post->ID, '_cecontact_email', true);
    $phone = get_post_meta($post->ID, '_cecontact_phone', true);
    $subject = get_post_meta($post->ID, '_cecontact_subject', true);
    $message = get_post_meta($post->ID, '_cecontact_message', true);
    $status = get_post_meta($post->ID, '_cecontact_status', true); 
    $status = $status ? $status : 'pending';

    ?>
    <table class="form-table">
        <tr>
            <th><label>First Name:</label></th>
            <td><strong><?php echo esc_html($first_name); ?></strong></td>
        </tr>
        <tr>
            <th><label>Last Name:</label></th>
            <td><strong><?php echo esc_html($last_name); ?></strong></td>
        </tr>
        <tr>
            <th><label>Email:</label></th>
            <td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td>
        </tr>
        <tr>
            <th><label>Phone:</label></th>
            <td><?php echo esc_html($phone ? $phone : 'N/A'); ?></td>
        </tr>
        <tr>
            <th><label>Subject:</label></th>
            <td><strong><?php echo esc_html($subject); ?></strong></td>
        </tr>
        <tr>
            <th><label>Message:</label></th>
            <td>
                <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                    <?php echo nl2br(esc_html($message)); ?>
                </div>
            </td>
        </tr>
        <tr>
            <th><label>Status:</label></th>
            <td>
                <select name="cecontact_status" id="cecontact_status">
                    <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
                    <option value="solved" <?php selected($status, 'solved'); ?>>Solved</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save custom meta data when the post is saved/updated
 */
function cecontact_save_meta_data($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['cecontact_meta_nonce'])) {
        return $post_id;
    }

    $nonce = $_POST['cecontact_meta_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'cecontact_save_meta_data')) {
        return $post_id;
    }

    // If this is an autosave, our form data is not set.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check the user's permissions.
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    // Check if we are saving the 'cecontact' post type
    if (isset($_POST['post_type']) && 'cecontact' !== $_POST['post_type']) {
        return $post_id;
    }

    // Check and save the status field.
    if (isset($_POST['cecontact_status'])) {
        $new_status = sanitize_text_field($_POST['cecontact_status']);
        
        // Only allow 'pending' or 'solved'
        if (in_array($new_status, array('pending', 'solved'))) {
            update_post_meta($post_id, '_cecontact_status', $new_status);
        }
    }
}
add_action('save_post', 'cecontact_save_meta_data');


/**
 * Add "Settings" submenu under CE Contacts
 */
function cecontact_add_settings_submenu() {
    add_submenu_page(
        'edit.php?post_type=cecontact',
        'CE Contact Settings',
        'Settings',
        'manage_options',
        'cecontact-settings',
        'cecontact_settings_page_callback'
    );
}
add_action('admin_menu', 'cecontact_add_settings_submenu');

/**
 * Settings page - Form to enter up to 5 additional recipient emails
 */
function cecontact_settings_page_callback() {
    // Save emails when form is submitted
    if (isset($_POST['cecontact_save_emails']) && current_user_can('manage_options')) {
        check_admin_referer('cecontact_save_emails_nonce');

        $emails = array();
        for ($i = 1; $i <= 5; $i++) {
            $email = isset($_POST["additional_email_{$i}"]) ? sanitize_email($_POST["additional_email_{$i}"]) : '';
            if ($email && is_email($email)) {
                $emails[] = $email;
            }
        }
        update_option('cecontact_additional_emails', $emails);

        echo '<div class="updated notice is-dismissible"><p>Additional email addresses saved successfully!</p></div>';
    }

    $saved_emails = get_option('cecontact_additional_emails', array());
    // Make sure we always have 5 fields (fill empty ones)
    $saved_emails = array_pad((array)$saved_emails, 5, '');

    ?>
    <div class="wrap">
        <h1>CE Contact Form – Notification Settings</h1>

        <form method="post" action="">
            <?php wp_nonce_field('cecontact_save_emails_nonce'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Main Admin Email</th>
                    <td>
                        <code><?php echo esc_html(get_option('admin_email')); ?></code>
                        <p class="description">This email will <strong>always</strong> receive notifications (cannot be disabled).</p>
                    </td>
                </tr>

                <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr>
                    <th scope="row"><label for="additional_email_<?php echo $i; ?>">Additional Recipient <?php echo $i; ?></label></th>
                    <td>
                        <input 
                            name="additional_email_<?php echo $i; ?>" 
                            type="email" 
                            id="additional_email_<?php echo $i; ?>" 
                            value="<?php echo esc_attr($saved_emails[$i - 1] ?? ''); ?>" 
                            class="regular-text" 
                            placeholder="example<?php echo $i; ?>@yourdomain.com"
                        >
                    </td>
                </tr>
                <?php endfor; ?>
            </table>

            <p class="description">
                Leave any field empty if you don't want to use it. Only valid email addresses will be saved.
            </p>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Additional Emails">
                <input type="hidden" name="cecontact_save_emails" value="1">
            </p>
        </form>
    </div>
    <?php
}


/**
 * Handle AJAX contact form submission
 */
function handle_contact_form_submission() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'contact_form_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
        wp_die();
    }

    // Sanitize inputs
    $first_name = sanitize_text_field($_POST['firstName']);
    $last_name  = sanitize_text_field($_POST['lastName']);
    $email      = sanitize_email($_POST['email']);
    $phone      = sanitize_text_field($_POST['phone']);
    $subject    = sanitize_text_field($_POST['subject']);
    $message    = sanitize_textarea_field($_POST['message']);

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
        wp_die();
    }
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        wp_die();
    }

    // Create custom post
    $post_id = wp_insert_post(array(
        'post_title'  => $subject . ' - ' . $first_name . ' ' . $last_name,
        'post_type'   => 'cecontact',
        'post_status' => 'publish',
    ));

    if ($post_id) {
        update_post_meta($post_id, '_cecontact_first_name', $first_name);
        update_post_meta($post_id, '_cecontact_last_name',  $last_name);
        update_post_meta($post_id, '_cecontact_email',      $email);
        update_post_meta($post_id, '_cecontact_phone',      $phone);
        update_post_meta($post_id, '_cecontact_subject',    $subject);
        update_post_meta($post_id, '_cecontact_message',    $message);
        update_post_meta($post_id, '_cecontact_status',     'pending');

        // === Send email to main admin + additional recipients ===
        $main_admin_email = get_option('admin_email');
        $additional_emails = get_option('cecontact_additional_emails', array());

        $recipients = array($main_admin_email);
        foreach ((array)$additional_emails as $e) {
            if (is_email($e)) {
                $recipients[] = $e;
            }
        }
        $recipients = array_unique($recipients);

        $email_subject = 'New Contact Form Submission: ' . $subject;
        $email_body = "You have a new contact form message:\n\n";
        $email_body .= "Name: {$first_name} {$last_name}\n";
        $email_body .= "Email: {$email}\n";
        $email_body .= "Phone: {$phone}\n";
        $email_body .= "Subject: {$subject}\n\n";
        $email_body .= "Message:\n{$message}\n\n";
        //$email_body .= "View all messages: " . admin_url('edit.php?post_type=cecontact');

        $headers = array('Content-Type: text/plain; charset=UTF-8');

        wp_mail($recipients, $email_subject, $email_body, $headers);

        wp_send_json_success(array('message' => 'Thank you! Your message has been sent successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Something went wrong. Please try again.'));
    }

    wp_die();
}
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');
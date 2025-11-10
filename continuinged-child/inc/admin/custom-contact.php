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
    $first_name = get_post_meta($post->ID, '_cecontact_first_name', true);
    $last_name = get_post_meta($post->ID, '_cecontact_last_name', true);
    $email = get_post_meta($post->ID, '_cecontact_email', true);
    $phone = get_post_meta($post->ID, '_cecontact_phone', true);
    $subject = get_post_meta($post->ID, '_cecontact_subject', true);
    $message = get_post_meta($post->ID, '_cecontact_message', true);
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
    </table>
    <?php
}

/**
 * Handle AJAX contact form submission
 */
function handle_contact_form_submission() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'contact_form_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
        wp_die();
    }

    // Sanitize and validate input
    $first_name = sanitize_text_field($_POST['firstName']);
    $last_name = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
        wp_die();
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        wp_die();
    }

    // Create post
    $post_data = array(
        'post_title'    => $subject . ' - ' . $first_name . ' ' . $last_name,
        'post_type'     => 'cecontact',
        'post_status'   => 'publish',
    );

    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        // Save meta data
        update_post_meta($post_id, '_cecontact_first_name', $first_name);
        update_post_meta($post_id, '_cecontact_last_name', $last_name);
        update_post_meta($post_id, '_cecontact_email', $email);
        update_post_meta($post_id, '_cecontact_phone', $phone);
        update_post_meta($post_id, '_cecontact_subject', $subject);
        update_post_meta($post_id, '_cecontact_message', $message);

        // Send notification email to admin (optional)
        $admin_email = get_option('admin_email');
        $email_subject = 'New Contact Form Submission: ' . $subject;
        $email_message = "New contact form submission:\n\n";
        $email_message .= "Name: {$first_name} {$last_name}\n";
        $email_message .= "Email: {$email}\n";
        $email_message .= "Phone: {$phone}\n";
        $email_message .= "Subject: {$subject}\n\n";
        $email_message .= "Message:\n{$message}";

        wp_mail($admin_email, $email_subject, $email_message);

        wp_send_json_success(array('message' => 'Thank you for your message! We will get back to you soon.'));
    } else {
        wp_send_json_error(array('message' => 'There was an error submitting your message. Please try again.'));
    }

    wp_die();
}
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');
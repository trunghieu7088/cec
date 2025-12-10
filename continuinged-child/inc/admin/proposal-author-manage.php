<?php
/**
 * Add this to your functions.php to replace the previous admin page code
 */

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'enqueue_proposals_admin_scripts');

function enqueue_proposals_admin_scripts($hook) {
    // Only load on our admin page
    if ($hook != 'toplevel_page_author-proposals') {
        return;
    }
    
    //fontawesome
      wp_enqueue_style('fontawesome-admin', get_stylesheet_directory_uri().'/assets/fontawesome6/css/all.min.css');

    // DataTables CSS (Bootstrap 5 version)
    wp_enqueue_style('author-datatable-style', get_stylesheet_directory_uri().'/assets/datatables/datatables.min.css');
    wp_enqueue_script('author-datatables-js-script', get_stylesheet_directory_uri().'/assets/datatables/datatables.min.js', array('jquery'), null, true);

    // Custom admin script
    wp_enqueue_script('proposals-admin', get_stylesheet_directory_uri() . '/assets/js/proposals-admin.js', array('jquery', 'author-datatables-js-script'), '1.0.0', true);
    
    // Localize script
    wp_localize_script('proposals-admin', 'proposalsAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('proposals_admin_nonce')
    ));
    

}

// Admin menu
add_action('admin_menu', 'author_proposals_admin_menu');

function author_proposals_admin_menu() {
    add_menu_page(
        'Author Proposals',
        'Author Proposals',
        'manage_options',
        'author-proposals',
        'display_author_proposals_page',
        'dashicons-welcome-write-blog',
        30
    );
}

function display_author_proposals_page() {
    ?>
    <style>
         .proposals-admin-wrap {
            margin: 20px 20px 0 0;
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .details-row {
            background: #f8f9fa;
            padding: 20px;
        }
        .details-section {
            margin-bottom: 20px;
        }
        .details-section h4 {
            color: #336666;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            border-bottom: 2px solid #336666;
            padding-bottom: 5px;
        }
        .details-section p {
            margin: 5px 0;
            line-height: 1.6;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            display: inline-block;
            min-width: 150px;
        }
        .detail-value {
            color: #212529;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-reviewed {
            background: #cfe2ff;
            color: #084298;
        }
        .status-approved {
            background: #d1e7dd;
            color: #0f5132;
        }
        .status-rejected {
            background: #f8d7da;
            color: #842029;
        }
        .reference-item {
            background: #fff;
            padding: 10px;
            margin: 5px 0;
            border-left: 3px solid #336666;
            border-radius: 4px;
        }
        .detail-text-block {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            white-space: pre-wrap;
            font-size: 13px;
            line-height: 1.8;
        }
       /* table.dataTable tbody tr.shown td.dt-control:before {
            content: "\f068";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        } */
      
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn-action {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-approve {
            background: #198754;
            color: white;
        }
        .btn-approve:hover {
            background: #157347;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background: #bb2d3b;
        }
        .btn-download {
            background: #0d6efd;
            color: white;
        }
        .btn-download:hover {
            background: #0b5ed7;
        }
    </style>
    <div class="wrap proposals-admin-wrap">
        <h1 class="wp-heading-inline">
            <i class="fas fa-book-medical"></i> Author Proposals Management
        </h1>
        <hr class="wp-header-end">
        
        <div class="proposals-stats mb-4">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'author_proposals';
            $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $pending = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
            $approved = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'approved'");
            $rejected = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'rejected'");
            ?>
            <div class="row mt-3 mb-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Proposals</h5>
                            <p class="card-text display-6"><?php echo $total; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-warning">
                        <div class="card-body">
                            <h5 class="card-title text-warning">Pending</h5>
                            <p class="card-text display-6 text-warning"><?php echo $pending; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">Approved</h5>
                            <p class="card-text display-6 text-success"><?php echo $approved; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger">Rejected</h5>
                            <p class="card-text display-6 text-danger"><?php echo $rejected; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="proposalsTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course Name</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'author_proposals';
                    $proposals = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_date DESC");
                    
                    foreach ($proposals as $proposal) {
                        $status_class = 'status-' . $proposal->status;
                        ?>
                        <tr data-id="<?php echo esc_attr($proposal->id); ?>">
                            <td class="dt-control"></td>
                            <td><?php echo esc_html($proposal->id); ?></td>
                            <td><?php echo esc_html(date('M d, Y', strtotime($proposal->submitted_date))); ?></td>
                            <td><?php echo esc_html($proposal->name); ?></td>
                            <td><?php echo esc_html($proposal->email); ?></td>
                            <td><?php echo esc_html($proposal->course_name); ?></td>
                            <td><span class="badge bg-primary"><?php echo esc_html($proposal->hours); ?></span></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo esc_html(ucfirst($proposal->status)); ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($proposal->status === 'pending'): ?>
                                        <button class="btn-action btn-approve" onclick="updateProposalStatus(<?php echo $proposal->id; ?>, 'approved')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button class="btn-action btn-reject" onclick="updateProposalStatus(<?php echo $proposal->id; ?>, 'rejected')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Hidden data for details -->
    <script type="text/javascript">
        var proposalsData = <?php echo json_encode($proposals); ?>;
    </script>
    <?php
}

// AJAX handler to update proposal status
add_action('wp_ajax_update_proposal_status', 'handle_update_proposal_status');

function handle_update_proposal_status() {
    global $wpdb;
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'proposals_admin_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Insufficient permissions.'));
    }
    
    $proposal_id = intval($_POST['proposal_id']);
    $status = sanitize_text_field($_POST['status']);
    
    // Validate status
    $allowed_statuses = array('pending', 'reviewed', 'approved', 'rejected');
    if (!in_array($status, $allowed_statuses)) {
        wp_send_json_error(array('message' => 'Invalid status.'));
    }
    
    $table_name = $wpdb->prefix . 'author_proposals';
    
    // Update status
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $proposal_id),
        array('%s'),
        array('%d')
    );
    
    if ($result === false) {
        wp_send_json_error(array('message' => 'Failed to update status.'));
    }
    
    // Get proposal details for email
    $proposal = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $proposal_id));
    
    // Send email notification to author
    if ($proposal) {
        $subject = 'Course Proposal Status Update - ContinuingEdCourses.Net';
        $message = "Dear " . $proposal->name . ",\n\n";
        $message .= "Your course proposal \"" . $proposal->course_name . "\" has been " . $status . ".\n\n";
        
        if ($status === 'approved') {
            $message .= "Congratulations! Your proposal has been approved. We will contact you shortly to discuss the next steps.\n\n";
        } elseif ($status === 'rejected') {
            $message .= "Unfortunately, your proposal has been rejected at this time. If you have any questions, please feel free to contact us.\n\n";
        }
        
        $message .= "Best regards,\n";
        $message .= "ContinuingEdCourses.Net Team";
        
        wp_mail($proposal->email, $subject, $message);
    }
    
    wp_send_json_success(array('message' => 'Status updated successfully.'));
}

// AJAX handler to download CV
add_action('wp_ajax_download_cv_file', 'handle_download_cv_file');

function handle_download_cv_file() {
    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'proposals_admin_nonce')) {
        wp_die('Security check failed.');
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions.');
    }
    
    global $wpdb;
    $proposal_id = intval($_GET['proposal_id']);
    $table_name = $wpdb->prefix . 'author_proposals';
    
    $proposal = $wpdb->get_row($wpdb->prepare("SELECT cv_file, name FROM $table_name WHERE id = %d", $proposal_id));
    
    if (!$proposal || !$proposal->cv_file) {
        wp_die('File not found.');
    }
    
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . $proposal->cv_file;
    
    if (!file_exists($file_path)) {
        wp_die('File does not exist.');
    }
    
    // Set headers for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));
    
    readfile($file_path);
    exit;
}
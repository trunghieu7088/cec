<?php
/**
 * Plugin Name: LifterLMS Group Discounts
 * Description: Tạo template discount codes hàng loạt cho LifterLMS
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Type: Group Discount
 */
function llms_register_group_discount_cpt() {
    $labels = array(
        'name'                  => 'Group Discounts',
        'singular_name'         => 'Group Discount',
        'menu_name'             => 'Group Discounts',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Group Discount',
        'edit_item'             => 'Edit Group Discount',
        'new_item'              => 'New Group Discount',
        'view_item'             => 'View Group Discount',
        'search_items'          => 'Search Group Discounts',
        'not_found'             => 'No group discounts found',
        'not_found_in_trash'    => 'No group discounts found in trash',
        'all_items'             => 'All Group Discounts',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => false,
        'show_ui'               => true,
       // 'show_in_menu'          => true,
       'show_in_menu'          => 'edit.php?post_type=llms_order',   
        'menu_icon'             => 'dashicons-tickets-alt',
        'menu_position'         => 56, // Sau LifterLMS
        'capability_type'       => 'post',
        'capabilities'          => array(
            'create_posts' => 'manage_options',
        ),
        'map_meta_cap'          => true,
        'hierarchical'          => false,
        'supports'              => array('title'),
        'has_archive'           => false,
        'rewrite'               => false,
        'query_var'             => false,
        'show_in_rest'          => false,
    );

    register_post_type('llms_group_discount', $args);
}
add_action('init', 'llms_register_group_discount_cpt');

/**
 * Add Meta Boxes
 */
function llms_group_discount_add_meta_boxes() {
    add_meta_box(
        'llms_group_discount_details',
        'Group Discount Configuration',
        'llms_group_discount_meta_box_callback',
        'llms_group_discount',
        'normal',
        'high'
    );
    
    add_meta_box(
        'llms_group_discount_stats',
        'Statistics',
        'llms_group_discount_stats_meta_box_callback',
        'llms_group_discount',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'llms_group_discount_add_meta_boxes');

/**
 * Meta Box HTML
 */
function llms_group_discount_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('llms_group_discount_meta_box', 'llms_group_discount_nonce');
    
    // Get existing values
    $prefix = get_post_meta($post->ID, '_discount_code_prefix', true);
    $value_from = get_post_meta($post->ID, '_discount_code_value_from', true);
    $value_to = get_post_meta($post->ID, '_discount_code_value_to', true);
    $increment = get_post_meta($post->ID, '_discount_code_increment', true) ?: 1;
    $expires = get_post_meta($post->ID, '_expires', true);
    $discount_valid = get_post_meta($post->ID, '_discount_valid', true);
    $description = get_post_meta($post->ID, '_description', true);
    $license_type = get_post_meta($post->ID, '_probable_license_type', true);
    $max_dollars = get_post_meta($post->ID, '_maximum_number_of_dollars_per', true);
    
    // Calculate potential codes
    $total_codes = 0;
    if ($value_from && $value_to && $increment) {
        $total_codes = floor(($value_to - $value_from) / $increment) + 1;
    }
    ?>
    
    <style>
        .llms-gd-field {
            margin-bottom: 20px;
        }
        .llms-gd-field label {
            display: inline-block;
            width: 220px;
            font-weight: 600;
        }
        .llms-gd-field input[type="text"],
        .llms-gd-field input[type="number"],
        .llms-gd-field input[type="date"],
        .llms-gd-field select,
        .llms-gd-field textarea {
            width: 400px;
        }
        .llms-gd-field textarea {
            height: 80px;
        }
        .llms-gd-help {
            display: block;
            margin-left: 220px;
            font-style: italic;
            color: #666;
            font-size: 12px;
        }
        .llms-gd-preview {
            background: #f0f0f1;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2271b1;
        }
        .llms-gd-preview strong {
            color: #2271b1;
        }
        .llms-gd-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .llms-gd-section:last-child {
            border-bottom: none;
        }
        .llms-gd-section h3 {
            margin-top: 0;
            color: #1d2327;
        }
    </style>
    
    <div class="llms-gd-section">
        <h3>📝 Code Generation Rules</h3>
        
        <div class="llms-gd-field">
            <label for="discount_code_prefix">Discount Code Prefix *</label>
            <input type="text" 
                   id="discount_code_prefix" 
                   name="discount_code_prefix" 
                   value="<?php echo esc_attr($prefix); ?>"
                   maxlength="1"
                   required
                   style="width: 60px; text-transform: uppercase;">
            <span class="llms-gd-help">Single character (e.g., H, M, P)</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="discount_code_value_from">Value From *</label>
            <input type="number" 
                   id="discount_code_value_from" 
                   name="discount_code_value_from" 
                   value="<?php echo esc_attr($value_from); ?>"
                   min="0"
                   step="1"
                   required
                   style="width: 150px;">
            <span class="llms-gd-help">Starting number (e.g., 2112)</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="discount_code_value_to">Value To *</label>
            <input type="number" 
                   id="discount_code_value_to" 
                   name="discount_code_value_to" 
                   value="<?php echo esc_attr($value_to); ?>"
                   min="0"
                   step="1"
                   required
                   style="width: 150px;">
            <span class="llms-gd-help">Ending number (e.g., 2469)</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="discount_code_increment">Increment *</label>
            <input type="number" 
                   id="discount_code_increment" 
                   name="discount_code_increment" 
                   value="<?php echo esc_attr($increment); ?>"
                   min="1"
                   step="1"
                   required
                   style="width: 100px;">
            <span class="llms-gd-help">Step between codes (e.g., 3 = H2112, H2115, H2118...)</span>
        </div>
        
        <?php if ($total_codes > 0): ?>
        <div class="llms-gd-preview">
            <strong>📊 Potential Codes:</strong> <?php echo number_format($total_codes); ?> codes can be generated<br>
            <strong>📋 Examples:</strong> 
            <?php
            $examples = array();
            for ($i = 0; $i < 5 && $i < $total_codes; $i++) {
                $val = $value_from + ($i * $increment);
                if ($val <= $value_to) {
                    $examples[] = $prefix . $val;
                }
            }
            echo implode(', ', $examples);
            if ($total_codes > 5) echo ', ...';
            ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="llms-gd-section">
        <h3>💰 Discount Settings</h3>
        
        <div class="llms-gd-field">
            <label for="maximum_number_of_dollars_per">Max Dollars Per Code *</label>
            <input type="number" 
                   id="maximum_number_of_dollars_per" 
                   name="maximum_number_of_dollars_per" 
                   value="<?php echo esc_attr($max_dollars); ?>"
                   min="0"
                   step="0.01"
                   required
                   style="width: 150px;">
            <span class="llms-gd-help">Maximum discount amount per code (e.g., 50.00)</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="expires">Expiration Date</label>
            <input type="date" 
                   id="expires" 
                   name="expires" 
                   value="<?php echo esc_attr($expires); ?>"
                   style="width: 200px;">
            <span class="llms-gd-help">Leave empty for no expiration</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="discount_valid">Status *</label>
            <select id="discount_valid" name="discount_valid" required style="width: 200px;">
                <option value="1" <?php selected($discount_valid, '1'); ?>>✅ Active</option>
                <option value="0" <?php selected($discount_valid, '0'); ?>>❌ Inactive</option>
            </select>
            <span class="llms-gd-help">Only active templates can generate codes</span>
        </div>
    </div>
    
    <div class="llms-gd-section">
        <h3>ℹ️ Additional Information</h3>
        
        <div class="llms-gd-field">
            <label for="description">Description</label>
            <textarea id="description" 
                      name="description" 
                      rows="3"><?php echo esc_textarea($description); ?></textarea>
            <span class="llms-gd-help">Internal notes (e.g., "University of Texas - Spring 2024")</span>
        </div>
        
        <div class="llms-gd-field">
            <label for="probable_license_type">Probable License Type</label>
            <select id="probable_license_type" name="probable_license_type" style="width: 200px;">
                <option value="">-- Select --</option>
                <option value="P" <?php selected($license_type, 'P'); ?>>P - Psychologist</option>
                <option value="S" <?php selected($license_type, 'S'); ?>>S - Social Worker</option>
                <option value="M" <?php selected($license_type, 'M'); ?>>M - Marriage & Family Therapist</option>
                <option value="C" <?php selected($license_type, 'C'); ?>>C - Counselor</option>
                <option value="O" <?php selected($license_type, 'O'); ?>>O - Other</option>
            </select>
            <span class="llms-gd-help">For analytics/reporting purposes</span>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Auto-uppercase prefix
        $('#discount_code_prefix').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Validate value ranges
        $('#discount_code_value_from, #discount_code_value_to, #discount_code_increment').on('change', function() {
            var from = parseInt($('#discount_code_value_from').val()) || 0;
            var to = parseInt($('#discount_code_value_to').val()) || 0;
            var increment = parseInt($('#discount_code_increment').val()) || 1;
            
            if (from > to) {
                alert('⚠️ "Value From" must be less than or equal to "Value To"');
                return false;
            }
            
            if (increment < 1) {
                alert('⚠️ "Increment" must be at least 1');
                return false;
            }
        });
    });
    </script>
    
    <?php
}

/**
 * Stats Meta Box
 */
function llms_group_discount_stats_meta_box_callback($post) {
    // Get data
    $prefix = get_post_meta($post->ID, '_discount_code_prefix', true);
    $value_from = get_post_meta($post->ID, '_discount_code_value_from', true);
    $value_to = get_post_meta($post->ID, '_discount_code_value_to', true);
    $increment = get_post_meta($post->ID, '_discount_code_increment', true) ?: 1;
    
    // Calculate stats
    $total_possible = 0;
    if ($value_from && $value_to && $increment) {
        $total_possible = floor(($value_to - $value_from) / $increment) + 1;
    }
    
    // Count used codes (you'll implement this later)
    global $wpdb;
    $used_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
         WHERE meta_key = '_parent_group_discount_id' 
         AND meta_value = %d",
        $post->ID
    ));
    
    $remaining = $total_possible - $used_count;
    $percentage_used = $total_possible > 0 ? round(($used_count / $total_possible) * 100, 1) : 0;
    
    ?>
    <style>
        .llms-gd-stat {
            padding: 10px;
            margin-bottom: 10px;
            background: #f0f0f1;
            border-radius: 3px;
        }
        .llms-gd-stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .llms-gd-stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #1d2327;
        }
        .llms-gd-progress {
            height: 20px;
            background: #ddd;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 10px;
        }
        .llms-gd-progress-bar {
            height: 100%;
            background: #2271b1;
            transition: width 0.3s;
        }
    </style>
    
    <div class="llms-gd-stat">
        <div class="llms-gd-stat-label">Total Possible</div>
        <div class="llms-gd-stat-value"><?php echo number_format($total_possible); ?></div>
    </div>
    
    <div class="llms-gd-stat">
        <div class="llms-gd-stat-label">Codes Used</div>
        <div class="llms-gd-stat-value" style="color: #d63638;"><?php echo number_format($used_count); ?></div>
    </div>
    
    <div class="llms-gd-stat">
        <div class="llms-gd-stat-label">Remaining</div>
        <div class="llms-gd-stat-value" style="color: #00a32a;"><?php echo number_format($remaining); ?></div>
    </div>
    
    <?php if ($total_possible > 0): ?>
    <div class="llms-gd-progress">
        <div class="llms-gd-progress-bar" style="width: <?php echo $percentage_used; ?>%"></div>
    </div>
    <p style="text-align: center; margin: 5px 0; font-size: 12px;">
        <?php echo $percentage_used; ?>% used
    </p>
    <?php endif; ?>
    
    <hr style="margin: 20px 0;">
    
    <p style="font-size: 12px; color: #666;">
        <strong>Created:</strong><br>
        <?php echo get_the_date('M j, Y @ g:i a', $post->ID); ?>
    </p>
    
    <?php if (get_the_modified_date('', $post->ID) !== get_the_date('', $post->ID)): ?>
    <p style="font-size: 12px; color: #666;">
        <strong>Last Modified:</strong><br>
        <?php echo get_the_modified_date('M j, Y @ g:i a', $post->ID); ?>
    </p>
    <?php endif; ?>
    <?php
}

/**
 * Save Meta Box Data - PHIÊN BẢN SỬA LẠI
 */
function llms_group_discount_save_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['llms_group_discount_nonce']) || 
        !wp_verify_nonce($_POST['llms_group_discount_nonce'], 'llms_group_discount_meta_box')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // MAPPING ĐÚNG giữa form name và meta key
    $field_mapping = array(
        'discount_code_prefix' => '_discount_code_prefix',
        'discount_code_value_from' => '_discount_code_value_from',
        'discount_code_value_to' => '_discount_code_value_to',
        'discount_code_increment' => '_discount_code_increment',
        'expires' => '_expires',
        'discount_valid' => '_discount_valid',
        'description' => '_description',
        'probable_license_type' => '_probable_license_type',
        'maximum_number_of_dollars_per' => '_maximum_number_of_dollars_per',
    );
    
    foreach ($field_mapping as $form_name => $meta_key) {
        if (isset($_POST[$form_name])) {
            $value = $_POST[$form_name];
            
            // Sanitize theo từng loại field
            switch ($meta_key) {
                case '_discount_code_prefix':
                    $value = strtoupper(substr(sanitize_text_field($value), 0, 1));
                    break;
                    
                case '_discount_code_value_from':
                case '_discount_code_value_to':
                case '_discount_valid':
                    $value = absint($value);
                    break;
                    
                case '_discount_code_increment':
                    $value = max(1, absint($value)); // Tối thiểu là 1
                    break;
                    
                case '_maximum_number_of_dollars_per':
                    $value = sanitize_text_field($value);
                    // Có thể thêm validation số thập phân nếu cần
                    $value = floatval($value);
                    break;
                    
                case '_description':
                    $value = sanitize_textarea_field($value);
                    break;
                    
                default:
                    $value = sanitize_text_field($value);
            }
            
            update_post_meta($post_id, $meta_key, $value);
        }
    }
    
    // Validate range
    $value_from = get_post_meta($post_id, '_discount_code_value_from', true);
    $value_to = get_post_meta($post_id, '_discount_code_value_to', true);
    
    if ($value_from > $value_to) {
        add_filter('redirect_post_location', function($location) {
            return add_query_arg('llms_gd_error', 'invalid_range', $location);
        });
    }
}
add_action('save_post_llms_group_discount', 'llms_group_discount_save_meta_box');

/**
 * Admin notices
 */
function llms_group_discount_admin_notices() {
    if (isset($_GET['llms_gd_error']) && $_GET['llms_gd_error'] === 'invalid_range') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><strong>Error:</strong> "Value From" must be less than or equal to "Value To".</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'llms_group_discount_admin_notices');

/**
 * Customize columns in admin list
 */
function llms_group_discount_custom_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = 'Template Name';
        } elseif ($key === 'date') {
            $new_columns['prefix'] = 'Prefix';
            $new_columns['range'] = 'Range';
            $new_columns['total_codes'] = 'Total Codes';
            $new_columns['used_codes'] = 'Used';
            $new_columns['max_discount'] = 'Max Discount';
            $new_columns['status'] = 'Status';
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_llms_group_discount_posts_columns', 'llms_group_discount_custom_columns');

/**
 * Populate custom columns
 */
function llms_group_discount_custom_column_content($column, $post_id) {
    switch ($column) {
        case 'prefix':
            $prefix = get_post_meta($post_id, '_discount_code_prefix', true);
            echo $prefix ? '<strong>' . esc_html($prefix) . '</strong>' : '—';
            break;
            
        case 'range':
            $from = get_post_meta($post_id, '_discount_code_value_from', true);
            $to = get_post_meta($post_id, '_discount_code_value_to', true);
            $increment = get_post_meta($post_id, '_discount_code_increment', true) ?: 1;
            
            if ($from && $to) {
                echo esc_html($from) . ' → ' . esc_html($to);
                echo '<br><small style="color:#666;">Step: ' . esc_html($increment) . '</small>';
            } else {
                echo '—';
            }
            break;
            
        case 'total_codes':
            $from = get_post_meta($post_id, '_discount_code_value_from', true);
            $to = get_post_meta($post_id, '_discount_code_value_to', true);
            $increment = get_post_meta($post_id, '_discount_code_increment', true) ?: 1;
            
            if ($from && $to && $increment) {
                $total = floor(($to - $from) / $increment) + 1;
                echo '<strong>' . number_format($total) . '</strong>';
            } else {
                echo '—';
            }
            break;
            
        case 'used_codes':
            global $wpdb;
            $used = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->postmeta} 
                 WHERE meta_key = '_parent_group_discount_id' 
                 AND meta_value = %d",
                $post_id
            ));
            
            if ($used > 0) {
                echo '<span style="color:#d63638;"><strong>' . number_format($used) . '</strong></span>';
            } else {
                echo '<span style="color:#666;">0</span>';
            }
            break;
            
        case 'max_discount':
            $max = get_post_meta($post_id, '_maximum_number_of_dollars_per', true);
            echo $max ? '$' . number_format($max, 2) : '—';
            break;
            
        case 'status':
            $valid = get_post_meta($post_id, '_discount_valid', true);
            $expires = get_post_meta($post_id, '_expires', true);
            
            if ($valid == '1') {
                echo '<span style="color:#00a32a;">✅ Active</span>';
                if ($expires) {
                    $exp_date = strtotime($expires);
                    if ($exp_date < time()) {
                        echo '<br><small style="color:#d63638;">Expired</small>';
                    } else {
                        echo '<br><small style="color:#666;">Exp: ' . date('M j, Y', $exp_date) . '</small>';
                    }
                }
            } else {
                echo '<span style="color:#d63638;">❌ Inactive</span>';
            }
            break;
    }
}
add_action('manage_llms_group_discount_posts_custom_column', 'llms_group_discount_custom_column_content', 10, 2);

/**
 * Make columns sortable
 */
function llms_group_discount_sortable_columns($columns) {
    $columns['prefix'] = 'prefix';
    $columns['status'] = 'status';
    return $columns;
}
add_filter('manage_edit-llms_group_discount_sortable_columns', 'llms_group_discount_sortable_columns');

/**
 * Activation hook
 */
function llms_group_discount_activate() {
    llms_register_group_discount_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'llms_group_discount_activate');

/**
 * Deactivation hook
 */
function llms_group_discount_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'llms_group_discount_deactivate');

//generate discount code here

/**
 * Auto-generate LifterLMS Coupon from Group Discount Template
 * 
 * @param string $discount_code The discount code to check/generate (e.g., "H2145")
 * @return int|false Post ID of generated coupon, or false if invalid
 */
function llms_auto_generate_discount_code($discount_code) {
    // Validate input
    if (empty($discount_code)) {
        return false;
    }
    
    // Sanitize discount code
    $discount_code = strtoupper(trim($discount_code));
    
    // Step 1: Check if code already exists in LifterLMS coupons
    $existing_coupon = get_llms_coupon_by_title_exact($discount_code);
    if ($existing_coupon) {
        // Code already generated
        return $existing_coupon->ID;
    }
    
    // Step 2: Parse discount code (prefix + value)
    if (strlen($discount_code) < 2) {
        return false;
    }
    
    $prefix = substr($discount_code, 0, 1);
    $value = substr($discount_code, 1);
    
    // Validate that value is numeric
    if (!is_numeric($value)) {
        return false;
    }
    
    $value = intval($value);
    
    // Step 3: Find matching Group Discount template
    $template_id = llms_find_group_discount_template($prefix, $value);
    
    if (!$template_id) {
        // No matching template found
        return false;
    }
    
    // Step 4: Get template data
    $template_data = llms_get_group_discount_template_data($template_id);
    
    if (!$template_data) {
        return false;
    }
    
    // Step 5: Validate template is active and not expired
    if (!llms_validate_group_discount_template($template_data)) {
        return false;
    }
    
    // Step 6: Generate the coupon
    $coupon_id = llms_create_coupon_from_template($discount_code, $template_data);
    
    return $coupon_id;
}

/**
 * Find Group Discount template matching the discount code
 * 
 * @param string $prefix Single character prefix
 * @param int $value Numeric value from code
 * @return int|false Template post ID or false
 */
function llms_find_group_discount_template($prefix, $value) {
    global $wpdb;
    
    // Query to find matching template
    $query = $wpdb->prepare("
        SELECT p.ID,
               pm1.meta_value as value_from,
               pm2.meta_value as value_to,
               pm3.meta_value as increment
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_prefix 
            ON p.ID = pm_prefix.post_id 
            AND pm_prefix.meta_key = '_discount_code_prefix'
            AND pm_prefix.meta_value = %s
        INNER JOIN {$wpdb->postmeta} pm1 
            ON p.ID = pm1.post_id 
            AND pm1.meta_key = '_discount_code_value_from'
        INNER JOIN {$wpdb->postmeta} pm2 
            ON p.ID = pm2.post_id 
            AND pm2.meta_key = '_discount_code_value_to'
        INNER JOIN {$wpdb->postmeta} pm3 
            ON p.ID = pm3.post_id 
            AND pm3.meta_key = '_discount_code_increment'
        INNER JOIN {$wpdb->postmeta} pm_valid
            ON p.ID = pm_valid.post_id
            AND pm_valid.meta_key = '_discount_valid'
            AND pm_valid.meta_value = '1'
        WHERE p.post_type = 'llms_group_discount'
        AND p.post_status = 'publish'
        AND CAST(pm1.meta_value AS UNSIGNED) <= %d
        AND CAST(pm2.meta_value AS UNSIGNED) >= %d
    ", $prefix, $value, $value);
    
    $templates = $wpdb->get_results($query);
    
    if (empty($templates)) {
        return false;
    }
    
    // Check increment rule: MOD((value - from), increment) = 0
    foreach ($templates as $template) {
        $from = intval($template->value_from);
        $increment = intval($template->increment);
        
        if ($increment < 1) {
            $increment = 1;
        }
        
        // Check if value matches the increment pattern
        if (($value - $from) % $increment === 0) {
            return intval($template->ID);
        }
    }
    
    return false;
}

/**
 * Get template data
 * 
 * @param int $template_id Group Discount template post ID
 * @return array|false Template data array or false
 */
function llms_get_group_discount_template_data($template_id) {
    if (!$template_id) {
        return false;
    }
    
    $template = get_post($template_id);
    
    if (!$template || $template->post_type !== 'llms_group_discount') {
        return false;
    }
    
    return array(
        'id' => $template_id,
        'name' => $template->post_title,
        'prefix' => get_post_meta($template_id, '_discount_code_prefix', true),
        'value_from' => get_post_meta($template_id, '_discount_code_value_from', true),
        'value_to' => get_post_meta($template_id, '_discount_code_value_to', true),
        'increment' => get_post_meta($template_id, '_discount_code_increment', true),
        'amount' => get_post_meta($template_id, '_maximum_number_of_dollars_per', true),
        'expires' => get_post_meta($template_id, '_expires', true),
        'valid' => get_post_meta($template_id, '_discount_valid', true),
        'description' => get_post_meta($template_id, '_description', true),
        'license_type' => get_post_meta($template_id, '_probable_license_type', true),
    );
}

/**
 * Validate template status
 * 
 * @param array $template_data Template data
 * @return bool True if valid
 */
function llms_validate_group_discount_template($template_data) {
    // Check if active
    if ($template_data['valid'] != '1') {
        return false;
    }
    
    // Check expiration
    if (!empty($template_data['expires'])) {
        $expiration_timestamp = strtotime($template_data['expires']);
        if ($expiration_timestamp && $expiration_timestamp < time()) {
            return false;
        }
    }
    
    // Check amount is set
    if (empty($template_data['amount']) || $template_data['amount'] <= 0) {
        return false;
    }
    
    return true;
}

/**
 * Create LifterLMS coupon from template
 * 
 * @param string $discount_code The discount code
 * @param array $template_data Template data
 * @return int|false Coupon post ID or false
 */
function llms_create_coupon_from_template($discount_code, $template_data) {
    // Prepare coupon post
    $coupon_post = array(
        'post_title' => $discount_code,
        'post_type' => 'llms_coupon',
        'post_status' => 'publish',
        'post_author' => 1, // Admin user
    );
    
    // Insert coupon post
    $coupon_id = wp_insert_post($coupon_post, true);
    
    if (is_wp_error($coupon_id)) {
        error_log('LLMS Group Discount: Failed to create coupon - ' . $coupon_id->get_error_message());
        return false;
    }
    
    // Format expiration date (m/d/Y format)
    $expiration_date = '';
    if (!empty($template_data['expires'])) {
        $expiration_date = date('m/d/Y', strtotime($template_data['expires']));
    }
    
    // Set required meta fields
    $meta_fields = array(
        '_llms_coupon_courses' => array(),  // Empty serialized array
        '_llms_coupon_membership' => array(),  // Empty serialized array
        '_llms_coupon_amount' => $template_data['amount'],
        '_llms_usage_limit' => '1',
        '_llms_discount_type' => 'dollar',
        '_llms_description' => !empty($template_data['description']) ? $template_data['description'] : '',
        '_llms_expiration_date' => $expiration_date,
        '_llms_plan_type' => 'any',
        '_llms_enable_trial_discount'=> 'no',
        '_llms_trial_amount'=>0,
    );
    
    // Add meta fields
    foreach ($meta_fields as $meta_key => $meta_value) {
        update_post_meta($coupon_id, $meta_key, $meta_value);
    }
    
    // Add custom meta to link back to template
    update_post_meta($coupon_id, '_parent_group_discount_id', $template_data['id']);
    update_post_meta($coupon_id, '_generated_date', current_time('mysql'));
    update_post_meta($coupon_id, '_probable_license_type', $template_data['license_type']);
    
    // Log the generation
    do_action('llms_group_discount_code_generated', $coupon_id, $discount_code, $template_data['id']);
    
    return $coupon_id;
}

/**
 * Admin function to test code generation
 * Usage: llms_test_discount_code('H2145')
 */
function llms_test_discount_code($code) {
    $result = llms_auto_generate_discount_code($code);
    
    if ($result) {
        echo "✅ Success! Coupon generated with ID: {$result}\n";
        echo "Code: " . get_the_title($result) . "\n";
        echo "Amount: $" . get_post_meta($result, '_llms_coupon_amount', true) . "\n";
        echo "Expires: " . get_post_meta($result, '_llms_expiration_date', true) . "\n";
        
        $template_id = get_post_meta($result, '_parent_group_discount_id', true);
        echo "Template: " . get_the_title($template_id) . " (ID: {$template_id})\n";
    } else {
        echo "❌ Failed to generate coupon for code: {$code}\n";
        echo "Possible reasons:\n";
        echo "- Code doesn't match any template\n";
        echo "- Template is inactive or expired\n";
        echo "- Invalid code format\n";
    }
}
<?php
// Thêm menu vào Tools trong admin
add_action('admin_menu', 'add_course_taxonomy_tools_menu');

function add_course_taxonomy_tools_menu() {
    add_management_page(
        'Create Course Taxonomies',
        'Create Course Taxonomies',
        'manage_options',
        'create-course-taxonomies',
        'render_course_taxonomy_tools_page'
    );
}

// Hàm xử lý và hiển thị form
function render_course_taxonomy_tools_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }

    $message = '';

    if (isset($_POST['create_course_terms']) && 
        wp_verify_nonce($_POST['course_nonce'], 'create_course_terms_action')) {

        $created = 0;
        $updated = 0;

        // Danh sách course_cat + thứ tự mong muốn (index +1 = term_order)
        $course_categories = [
            'Ethics',
            'Contemporary Approaches to Therapy',
            'Therapist Self-Care',
            'Pain Management',
            'Couple and Family Therapy',
            'Difficult Clients',
            'Addiction',
            'Attention-Deficit/Hyperactivity Disorder',
            'Cultural Diversity / Multicultural Competency',
            'Trauma / PTSD',
            'Psychopharmacology',
            'Transference and Countertransference',
            'Eating Disorders and Obesity',
            'Suicide Prevention',
            'Supervision',
            'DSM-5',
            'Mental Health and Medical Issues',
            'Aging and Long Term Care',
            'Grief'
        ];

        foreach ($course_categories as $index => $term_name) {
            $order = $index + 1; // 1 đến 19
            remove_action('created_course_cat', 'save_course_cat_order_meta');
            remove_action('edited_course_cat', 'save_course_cat_order_meta');
            // Kiểm tra term có tồn tại chưa
            $existing = term_exists($term_name, 'course_cat');

            if (!$existing) {
                // Insert mới
                $result = wp_insert_term($term_name, 'course_cat');
                if (!is_wp_error($result)) {
                    $term_id = $result['term_id'];
                    add_term_meta($term_id, 'order', $order, true);
                    $created++;
                }
            } else {
                // Term đã tồn tại → lấy term_id
                if (is_array($existing)) {
                    $term_id = $existing['term_id'];
                } else {
                    $term = get_term($existing, 'course_cat');
                    $term_id = $term->term_id;
                }

                // Kiểm tra xem đã có meta term_order chưa, nếu chưa thì thêm
                $current_order = get_term_meta($term_id, 'term_order', true);
                if ($current_order === '' || $current_order != $order) {
                    update_term_meta($term_id, 'order', $order);
                    $updated++;
                }
            }
        }

        // === Phần course_difficulty (không cần order, giữ nguyên) ===
        $difficulties = [
            'beginning',
            'beginning to intermediate',
            'introductory to intermediate',
            'intermediate',
            'advanced',
            'intermediate to advanced'
        ];

        foreach ($difficulties as $term) {
            if (!term_exists($term, 'course_difficulty')) {
                wp_insert_term($term, 'course_difficulty');
                $created++;
            }
        }

        // Thông báo kết quả
        $msg = '';
        if ($created > 0) {
            $msg .= sprintf('<p><strong>%d term mới đã được tạo</strong> và gán thứ tự tự động.</p>', $created);
        }
        if ($updated > 0) {
            $msg .= sprintf('<p><strong>%d term đã tồn tại được cập nhật lại thứ tự</strong> cho đúng.</p>', $updated);
        }
        if ($created == 0 && $updated == 0) {
            $msg = '<p><strong>Tất cả term đã tồn tại và đã có thứ tự đúng.</strong></p>';
        }

        $message = '<div class="updated"><p>' . $msg . '</p></div>';
    }
    ?>

    <div class="wrap">
        <h1>Course Taxonomies Tool</h1>

        <?php echo $message; ?>

        <div class="card" style="max-width: 800px;">
            <h2>Create Default Terms</h2>
            <p>Tool này sẽ tự động tạo các term còn thiếu và <strong>gán meta `term_order` theo đúng thứ tự từ 1 → 19</strong> cho taxonomy <code>course_cat</code>.</p>
            
            <ul style="list-style: disc inside;">
                <li><strong>Course Category</strong> (course_cat) – 19 terms + thứ tự</li>
                <li><strong>Course Difficulty</strong> (course_difficulty) – 6 terms</li>
            </ul>

            <form method="post" onsubmit="return confirm('Tạo/cập nhật tất cả term và thứ tự ngay bây giờ?');">
                <?php wp_nonce_field('create_course_terms_action', 'course_nonce'); ?>
                <p>
                    <input type="submit" name="create_course_terms" class="button button-primary button-large" 
                           value="Tạo & Cập nhật Thứ tự Ngay">
                </p>
            </form>
        </div>
    </div>

    <?php
}
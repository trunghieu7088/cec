<?php
/**
 * Insert a new CE hours record into the database.
 *
 * @param int $user_id    User ID (required)
 * @param int $course_id  Course ID (required)
 * @param string $date    Date in 'Y-m-d H:i:s' format (e.g. '2025-11-17 13:15:00')
 * @param float $ce_hours CE hours earned (e.g. 2.5)
 *
 * @return int|false ID of the inserted record, or false on failure
 */
function insert_ce_hours_record($user_id, $course_id, $date, $ce_hours) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'complete_course_ce_hours';

    // Validate input
    if (empty($user_id) || empty($course_id) || empty($date) || !is_numeric($ce_hours)) {
        return false;
    }

    // Format date if needed
    $formatted_date = date('Y-m-d H:i:s', strtotime($date));

    // Insert data
    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id'    => absint($user_id),
            'course_id'  => absint($course_id),
            'date'       => $formatted_date,
            'ce_hours'   => floatval($ce_hours)
        ),
        array(
            '%d', // user_id
            '%d', // course_id
            '%s', // date
            '%f'  // ce_hours
        )
    );

    // Return inserted ID or false
    return $result ? $wpdb->insert_id : false;
}

/**
 * Lấy tổng số CE hours của một user.
 *
 * @param int $user_id ID của user
 * @return float Tổng CE hours (0 nếu không có dữ liệu)
 */
function get_user_total_ce_hours( $user_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'complete_course_ce_hours';

    // Kiểm tra user_id hợp lệ
    if ( ! $user_id || ! is_numeric( $user_id ) ) {
        return 0.0;
    }

    $user_id = absint( $user_id );

    // Truy vấn tổng CE hours
    $total = $wpdb->get_var( $wpdb->prepare(
        "SELECT SUM(ce_hours) FROM $table_name WHERE user_id = %d",
        $user_id
    ) );

    // Trả về 0.0 nếu không có dữ liệu
    return $total ? floatval( $total ) : 0.0;
}

/**
 * 2. Tính toán % discount dựa trên CE hours
 * 
 * @param int $user_id ID của user (optional)
 * @param float $ce_hours Số giờ học (nếu không truyền user_id)
 * @return array Thông tin discount ['discount' => 15, 'level' => '41-80']
 */
function calculate_ce_rewards_discount($user_id = 0, $ce_hours = 0) {
    // Nếu có user_id thì lấy total hours từ database
    if ($user_id > 0) {
        $ce_hours = get_user_total_ce_hours($user_id);
    }
    
    // Query tất cả rewards levels, sắp xếp theo from_hours tăng dần
    $args = array(
        'post_type' => 'rewards',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => '_from_hours',
        'order' => 'ASC'
    );
    
    $rewards = get_posts($args);
    
    $current_discount = 0;
    $current_level = '';
    
    // Duyệt qua các mức discount để tìm mức phù hợp
    foreach ($rewards as $reward) {
        $from_hours = floatval(get_post_meta($reward->ID, '_from_hours', true));
        $to_hours = get_post_meta($reward->ID, '_to_hours', true);
        $discount = floatval(get_post_meta($reward->ID, '_discount', true));
        
        // Xử lý trường hợp "501+" (to_hours có thể là rỗng hoặc 0)
        $is_unlimited = empty($to_hours) || $to_hours == 0 || $to_hours == '999999';
        
        if ($ce_hours >= $from_hours) {
            if ($is_unlimited || $ce_hours <= floatval($to_hours)) {
                $current_discount = $discount;
                $current_level = $from_hours . '-' . ($is_unlimited ? '∞' : $to_hours);
            }
        }
    }
    
    return array(
        'discount' => $current_discount,
        'level' => $current_level,
        'total_hours' => $ce_hours
    );
}


/**
 * 3. Tính số giờ cần để đạt mốc tiếp theo
 * 
 * @param int $user_id ID của user
 * @return array Thông tin về mốc tiếp theo hoặc null nếu đã đạt mức cao nhất
 */
function get_next_ce_rewards_level($user_id) {
    $current_hours = get_user_total_ce_hours($user_id);
    $current_discount_info = calculate_ce_rewards_discount($user_id);
    
    // Query tất cả rewards levels
    $args = array(
        'post_type' => 'rewards',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => '_from_hours',
        'order' => 'ASC'
    );
    
    $rewards = get_posts($args);
    
    $next_level = null;
    
    // Tìm mức tiếp theo
    foreach ($rewards as $reward) {
        $from_hours = floatval(get_post_meta($reward->ID, '_from_hours', true));
        $to_hours = get_post_meta($reward->ID, '_to_hours', true);
        $discount = floatval(get_post_meta($reward->ID, '_discount', true));
        
        // Nếu from_hours lớn hơn số giờ hiện tại, đây là mức tiếp theo
        if ($from_hours > $current_hours) {
            $hours_needed = $from_hours - $current_hours;
            
            $next_level = array(
                'from_hours' => $from_hours,
                'to_hours' => $to_hours,
                'discount' => $discount,
                'hours_needed' => $hours_needed,
                'current_hours' => $current_hours,
                'current_discount' => $current_discount_info['discount']
            );
            break;
        }
    }
    
    return $next_level;
}



/**
 * Áp dụng CE Rewards discount vào giá sản phẩm
 * 
 * @param float $price Giá gốc
 * @param int $user_id ID của user
 * @param float $promotional_discount Discount từ mã khuyến mãi (%)
 * @return array ['original_price', 'ce_discount', 'promotional_discount', 'total_discount', 'final_price']
 */
function apply_ce_rewards_discount($price, $user_id, $promotional_discount = 0) {
    $ce_info = calculate_ce_rewards_discount($user_id);
    $ce_discount = $ce_info['discount'];
    
    $total_discount = $ce_discount + $promotional_discount;
    $discount_amount = ($price * $total_discount) / 100;
    $final_price = $price - $discount_amount;
    
    return array(
        'original_price' => $price,
        'ce_discount' => $ce_discount,
        'promotional_discount' => $promotional_discount,
        'total_discount' => $total_discount,
        'discount_amount' => $discount_amount,
        'final_price' => max(0, $final_price) // Đảm bảo không âm
    );
}

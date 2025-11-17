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
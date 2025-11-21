<?php
/**
 * Course Cache Manager với Redis
 */
class Course_Redis_Cache {
    private $redis;
    private $cache_key = 'courses_cache';
    private $cache_ttl = 86400 * 30; // 24 hours * 30 days = 1 month
    
    public function __construct() {
        $this->init_redis();
    }
    
    /**
     * Khởi tạo kết nối Redis
     */
    private function init_redis() {
       /* local with docker */
       /*
        try {
            $this->redis = new Redis();
            $this->redis->connect('127.0.0.1', 6379);
            // BỎ serializer tự động, dùng json_encode/decode thủ công
            // $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_JSON);
            error_log('Redis connected successfully');
        } catch (Exception $e) {
            error_log('Redis connection failed: ' . $e->getMessage());
            $this->redis = null;
        } */
        /* end */

        /* live with redis io */
        try {
        $this->redis = new Redis();
          
            
             $redis_options = get_option('redis_cache_options', [
            'endpoint' => '127.0.0.1',
            'port'     => 6379,
            'username' => '',
            'password' => ''
        ]);
            $this->redis->connect($redis_options['endpoint'], (int)$redis_options['port'], 2);

                if (!empty($redis_options['password'])) {
                if (!empty($redis_options['username'])) {
                    $this->redis->auth([$redis_options['username'], $redis_options['password']]);
                } else {
                    $this->redis->auth($redis_options['password']);
                }
            }
        
        error_log('Redis connected successfully');
    } catch (Exception $e) {
        error_log('Redis connection failed: ' . $e->getMessage());
        $this->redis = null;
    }
        /* end */
    }
    
    /**
     * Kiểm tra Redis có khả dụng không
     */
    public function is_available() {
        return $this->redis !== null;
    }
    
    /**
     * Lấy tất cả courses và cache vào Redis
     */
    public function rebuild_cache() {
        if (!$this->is_available()) {
            error_log('Redis not available');
            return false;
        }
        
        $args = array(
            'post_type' => 'course',
            'post_status' => 'publish',
            'posts_per_page' => -1, // Lấy tất cả
            'orderby' => 'title',
            'order' => 'ASC',
        );
        
        $query = new WP_Query($args);
        $courses = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $main_content = get_post_meta(get_the_ID(), '_course_main_content', true);
                
                $courses[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => wp_trim_words(wp_strip_all_tags($main_content), 50, '...'),
                    'url' => get_permalink(),
                    'content' => $main_content,
                    // Thêm các field để search tốt hơn
                    'title_lower' => mb_strtolower(get_the_title(), 'UTF-8'),
                    'content_lower' => mb_strtolower(wp_strip_all_tags($main_content), 'UTF-8'),
                );
            }
            wp_reset_postdata();
        }
        
        error_log('Total courses found: ' . count($courses));
        
        // Lưu vào Redis
        try {
            // Dùng set thay vì setex để test
            $result = $this->redis->set($this->cache_key, json_encode($courses));
            if ($result) {
                $this->redis->expire($this->cache_key, $this->cache_ttl);
                error_log('Redis cache saved successfully. Total: ' . count($courses));
                return true;
            } else {
                error_log('Redis set returned false');
                return false;
            }
        } catch (Exception $e) {
            error_log('Redis cache save failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy courses từ cache
     */
    public function get_cached_courses() {
        if (!$this->is_available()) {
            error_log('Redis not available in get_cached_courses');
            return null;
        }
        
        try {
            $data = $this->redis->get($this->cache_key);
            error_log('Redis get result: ' . ($data === false ? 'FALSE' : 'has data'));
            
            if ($data === false) {
                error_log('Cache empty, rebuilding...');
                $this->rebuild_cache();
                $data = $this->redis->get($this->cache_key);
            }
            
            // Decode JSON nếu cần
            if (is_string($data)) {
                $courses = json_decode($data, true);
                error_log('Decoded courses count: ' . (is_array($courses) ? count($courses) : 'not array'));
                return $courses;
            }
            
            return $data;
        } catch (Exception $e) {
            error_log('Redis cache get failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Tìm kiếm courses trong cache
     */
    public function search_courses($search_term) {
        $courses = $this->get_cached_courses();
        
        if ($courses === null) {
            return array();
        }
        
        $search_term_lower = mb_strtolower($search_term, 'UTF-8');
        $results = array();
        
        foreach ($courses as $course) {
            // Tìm kiếm trong title và content
            if (mb_strpos($course['title_lower'], $search_term_lower) !== false) {
                
                // Không trả về các field _lower
                $results[] = array(
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'excerpt' => $course['excerpt'],
                    'url' => $course['url'],
                );
            }
        }
        
        // Giới hạn 10 kết quả
        return array_slice($results, 0, 10);
    }
    
    /**
     * Xóa cache (dùng khi có course mới/cập nhật/xóa)
     */
    public function clear_cache() {
        if (!$this->is_available()) {
            return false;
        }
        
        try {
            $this->redis->del($this->cache_key);
            return true;
        } catch (Exception $e) {
            error_log('Redis cache clear failed: ' . $e->getMessage());
            return false;
        }
    }
}

// Khởi tạo instance global
$GLOBALS['course_cache'] = new Course_Redis_Cache();

/**
 * AJAX Handler - Search courses từ Redis cache
 */
add_action('wp_ajax_search_courses', 'search_courses_callback');
add_action('wp_ajax_nopriv_search_courses', 'search_courses_callback');

function search_courses_callback() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'search_courses_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    $search_term = sanitize_text_field($_POST['search_term']);
    
    if (empty($search_term)) {
        wp_send_json_error('Empty search term');
        return;
    }
    
    global $course_cache;
    
    // Tìm kiếm từ cache
    $results = $course_cache->search_courses($search_term);
    
    // Nếu Redis không khả dụng, fallback về database query
    /*
    if ($results === array() && !$course_cache->is_available()) {
        $args = array(
            'post_type' => 'course',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => $search_term,
            'orderby' => 'relevance',
        );
        
        $query = new WP_Query($args);
        $results = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $main_content = get_post_meta(get_the_ID(), '_course_main_content', true);
                
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => wp_trim_words(wp_strip_all_tags($main_content), 50, '...'),
                    'url' => get_permalink(),
                );
            }
            wp_reset_postdata();
        }
    }
    */
    wp_send_json_success($results);
}


/**
 * Admin action để rebuild cache thủ công
 */
add_action('admin_post_rebuild_course_cache', 'admin_rebuild_course_cache');

function admin_rebuild_course_cache() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Verify nonce
    if (!isset($_POST['rebuild_cache_nonce']) || !wp_verify_nonce($_POST['rebuild_cache_nonce'], 'rebuild_course_cache_action')) {
        wp_die('Invalid nonce');
    }
    
    global $course_cache;
    
    if ($course_cache->rebuild_cache()) {
        wp_redirect(add_query_arg('cache_rebuilt', '1', admin_url('tools.php?page=course-cache-manager')));
    } else {
        wp_redirect(add_query_arg('cache_error', '1', admin_url('tools.php?page=course-cache-manager')));
    }
    exit;
}

/**
 * Admin action để clear cache thủ công
 */
add_action('admin_post_clear_course_cache', 'admin_clear_course_cache');

function admin_clear_course_cache() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    
    // Verify nonce
    if (!isset($_POST['clear_cache_nonce']) || !wp_verify_nonce($_POST['clear_cache_nonce'], 'clear_course_cache_action')) {
        wp_die('Invalid nonce');
    }
    
    global $course_cache;
    
    if ($course_cache->clear_cache()) {
        wp_redirect(add_query_arg('cache_cleared', '1', admin_url('tools.php?page=course-cache-manager')));
    } else {
        wp_redirect(add_query_arg('cache_error', '1', admin_url('tools.php?page=course-cache-manager')));
    }
    exit;
}

/**
 * Thêm menu page để quản lý cache
 */
add_action('admin_menu', 'add_course_cache_menu');

function add_course_cache_menu() {
    add_management_page(
        'Course Cache Manager',
        'Course Cache',
        'manage_options',
        'course-cache-manager',
        'course_cache_manager_page'
    );
}

/**
 * Trang quản lý cache
 */
function course_cache_manager_page() {
    global $course_cache;
    
    // Lấy thông tin cache
    $cache_info = array();
    if ($course_cache->is_available()) {
        $courses = $course_cache->get_cached_courses();
        $cache_info['total_courses'] = $courses ? count($courses) : 0;
        $cache_info['status'] = 'connected';
    } else {
        $cache_info['status'] = 'disconnected';
    }
    
    ?>
    <div class="wrap">
        <h1>Course Cache Manager</h1>
        
        <div class="card">
            <h2>Redis Status</h2>
            <?php if ($cache_info['status'] === 'connected'): ?>
                <p><span class="dashicons dashicons-yes-alt" style="color: green;"></span> Redis đang hoạt động</p>
                <?php if (isset($cache_info['total_courses'])): ?>
                    <p><strong>Số courses trong cache:</strong> <?php echo $cache_info['total_courses']; ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p><span class="dashicons dashicons-warning" style="color: red;"></span> Redis không kết nối được</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Quản lý Cache</h2>
            <p>Rebuild cache sẽ lấy toàn bộ courses từ database và lưu vào Redis.</p>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="rebuild_course_cache">
                <?php wp_nonce_field('rebuild_course_cache_action', 'rebuild_cache_nonce'); ?>
                
                <p>
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-update"></span> Rebuild Cache
                    </button>
                </p>
            </form>
            
            <hr>
            
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="clear_course_cache">
                <?php wp_nonce_field('clear_course_cache_action', 'clear_cache_nonce'); ?>
                
                <p>
                    <button type="submit" class="button button-secondary">
                        <span class="dashicons dashicons-trash"></span> Clear Cache
                    </button>
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2>Thông tin</h2>
            <ul>
                <li><strong>Cache TTL:</strong> 24 giờ</li>
                <li><strong>Auto rebuild:</strong> Khi save/delete course</li>
                <li><strong>Cron job:</strong> Rebuild mỗi ngày</li>
            </ul>
        </div>
    </div>
    
    <style>
        .card { max-width: 800px; margin-bottom: 20px; }
        .card .dashicons { vertical-align: middle; }
        .card button { margin-top: 10px; }
        .card button .dashicons { vertical-align: middle; margin-right: 5px; }
    </style>
    <?php
}

/**
 * Thêm button rebuild cache vào admin bar
 */
add_action('admin_bar_menu', 'add_rebuild_cache_button', 100);

function add_rebuild_cache_button($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'rebuild_course_cache',
        'title' => 'Rebuild Course Cache',
        'href' => admin_url('tools.php?page=course-cache-manager'),
    ));
}

/**
 * Hiển thị thông báo sau khi rebuild cache
 */
add_action('admin_notices', 'course_cache_admin_notices');

function course_cache_admin_notices() {
    if (isset($_GET['cache_rebuilt'])) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Thành công!</strong> Course cache đã được rebuild.</p></div>';
    }
    
    if (isset($_GET['cache_cleared'])) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Thành công!</strong> Course cache đã được xóa.</p></div>';
    }
    
    if (isset($_GET['cache_error'])) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Lỗi!</strong> Không thể thao tác với cache. Kiểm tra Redis connection.</p></div>';
    }
}

/**
 * Cron job để rebuild cache định kỳ (mỗi ngày)
 */
add_action('wp', 'schedule_course_cache_rebuild');

function schedule_course_cache_rebuild() {
    if (!wp_next_scheduled('rebuild_course_cache_cron')) {
        wp_schedule_event(time(), 'daily', 'rebuild_course_cache_cron');
    }
}

add_action('rebuild_course_cache_cron', 'rebuild_course_cache_cron_job');

function rebuild_course_cache_cron_job() {
    global $course_cache;
    $course_cache->rebuild_cache();
}

/**
 * REST API endpoint - Nhanh hơn admin-ajax.php
 */
add_action('rest_api_init', function() {
    register_rest_route('course-cache/v1', '/search', array(
        'methods' => 'GET',
        'callback' => 'rest_search_courses_callback',
        'permission_callback' => '__return_true',
        'args' => array(
            'q' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
});

function rest_search_courses_callback($request) {
    $start_time = microtime(true);
    
    $search_term = $request->get_param('q');
    
    if (empty($search_term)) {
        return new WP_Error('empty_search', 'Search term is required', array('status' => 400));
    }
    
    global $course_cache;
    
    // Tìm kiếm từ cache
    $results = $course_cache->search_courses($search_term);
    
    // Fallback nếu Redis không khả dụng
    if ($results === array() && !$course_cache->is_available()) {
        $args = array(
            'post_type' => 'course',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => $search_term,
            'orderby' => 'relevance',
        );
        
        $query = new WP_Query($args);
        $results = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                $main_content = get_post_meta(get_the_ID(), '_course_main_content', true);
                
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => wp_trim_words(wp_strip_all_tags($main_content), 50, '...'),
                    'url' => get_permalink(),
                );
            }
            wp_reset_postdata();
        }
    }
    
    $execution_time = round((microtime(true) - $start_time) * 1000, 2);
    
    return array(
        'results' => $results,
        'execution_time' => $execution_time . 'ms',
        'cache_used' => $course_cache->is_available(),
        'total' => count($results),
    );
}


?>



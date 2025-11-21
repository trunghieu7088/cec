<?php
/**
 * File: wp-content/course-search-api.php
 * 
 * Standalone search endpoint - KHÔNG load WordPress
 * Truy cập: https://yoursite.com/wp-content/course-search-api.php?q=javascript
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // CORS nếu cần

// Chỉ cho phép GET request
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Lấy search term
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_term)) {
    http_response_code(400);
    echo json_encode(['error' => 'Search term required']);
    exit;
}

$start_time = microtime(true);

        // Lấy cấu hình Redis từ option đã lưu trong admin
        $redis_options = array(
            'endpoint' => 'redis-14367.crce185.ap-seast-1-1.ec2.cloud.redislabs.com',
            'port'     => 14367,
            'username' => '',
            'password' => 'VODhQgVUnLvmQ2kSwxVxpHesc4euLhWm'
        );

try {
    
    $redis = new Redis();
  
    $redis->pconnect($redis_options['endpoint'], $redis_options['port'], 2); // pconnect thay vì connect

// Auth chỉ chạy lần đầu tiên thôi
if (!empty($redis_options['password'])) {
    try {
        if (!empty($redis_options['username'])) {
            $redis->auth([$redis_options['username'], $redis_options['password']]);
        } else {
            $redis->auth($redis_options['password']);
        }
    } catch (RedisException $e) {
        // Nếu đã auth rồi thì sẽ lỗi "ERR AUTH called without any password", bỏ qua là được
        if (strpos($e->getMessage(), 'AUTH') === false) {
            throw $e;
        }
    }
}
    
    // Lấy cache
    $cache_key = 'courses_cache';
    $data = $redis->get($cache_key);
    
    if ($data === false) {
        http_response_code(503);
        echo json_encode([
            'error' => 'Cache not available',
            'message' => 'Please rebuild cache from WordPress admin'
        ]);
        exit;
    }
    
    // Decode JSON
    $courses = json_decode($data, true);
    
    if (!is_array($courses)) {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid cache data']);
        exit;
    }
    
    // Search
    $search_term_lower = mb_strtolower($search_term, 'UTF-8');
    $results = [];
    $count = 0;
    
    foreach ($courses as $course) {
        if ($count >= 10) {
            break;
        }
        
        // Search in title first
        $title_match = mb_strpos($course['title_lower'], $search_term_lower) !== false;
        
     
        
        if ($title_match) {
            $results[] = [
                'id' => $course['id'],
                'title' => $course['title'],
                'excerpt' => $course['excerpt'],
                'url' => $course['url'],
            ];
            $count++;
        }
    }
    
    $execution_time = round((microtime(true) - $start_time) * 1000, 2);
    
    // Response
    echo json_encode([
        'success' => true,
        'results' => $results,
        'total' => count($results),
        'execution_time' => $execution_time . 'ms',
        'cache_used' => true,
        'search_term' => $search_term,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
?>
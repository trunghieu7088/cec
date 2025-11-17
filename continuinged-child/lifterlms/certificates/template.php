<?php
/**
 * Simple Certificate Template
 * 
 * Đường dẫn: wp-content/themes/your-theme/lifterlms/certificates/template.php
 */

defined( 'ABSPATH' ) || exit;

// Lấy thông tin certificate
global $post;
$certificate = new LLMS_User_Certificate( $post->ID );

// Lấy user ID và student object
$user_id = $certificate->get( 'user_id' );
$student = llms_get_student( $user_id );

// Lấy thông tin với fallback

get_header();
?>
    
  <h3>Testing</h3>
<?php 
get_footer();
?>
<?php 
require('certificate_quiz_logic.php');
require('authenticate_register_logic.php');
require('ce_hours_handler.php');
require('search_course.php');
require('discount_code_handler.php');
require('print-certificate.php');

add_action('wp_head','init_ajax_url_frontend',20);

function init_ajax_url_frontend()
{
    ?>
     <script type="text/javascript">
        var ajaxurl_global = "<?php echo esc_url( admin_url('admin-ajax.php') ); ?>";
    </script>
    <?php
}
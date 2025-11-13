<?php 
require('certificate_logic.php');

add_action('wp_head','init_ajax_url_frontend',20);

function init_ajax_url_frontend()
{
    ?>
     <script type="text/javascript">
        var ajaxurl_global = "<?php echo esc_url( admin_url('admin-ajax.php') ); ?>";
    </script>
    <?php
}
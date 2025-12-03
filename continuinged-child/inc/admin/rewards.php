<?php
// 1. Register Custom Post Type "Rewards"
function register_rewards_cpt() {
    $labels = array(
        'name'                  => _x( 'Rewards', 'Post type general name', 'textdomain' ),
        'singular_name'         => _x( 'Reward', 'Post type singular name', 'textdomain' ),
        'menu_name'             => _x( 'Rewards', 'Admin Menu text', 'textdomain' ),
        'name_admin_bar'        => _x( 'Reward', 'Add New on Toolbar', 'textdomain' ),
        'add_new'               => __( 'Add New', 'textdomain' ),
        'add_new_item'          => __( 'Add New Reward', 'textdomain' ),
        'new_item'              => __( 'New Reward', 'textdomain' ),
        'edit_item'             => __( 'Edit Reward', 'textdomain' ),
        'view_item'             => __( 'View Reward', 'textdomain' ),
        'all_items'             => __( 'All Rewards', 'textdomain' ),
        'search_items'          => __( 'Search Rewards', 'textdomain' ),
        'not_found'             => __( 'No rewards found.', 'textdomain' ),
        'not_found_in_trash'    => __( 'No rewards found in Trash.', 'textdomain' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'reward' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-awards',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    );

    register_post_type( 'rewards', $args );
}
add_action( 'init', 'register_rewards_cpt' );


// 2. Add Meta Box
function rewards_add_meta_box() {
    add_meta_box(
        'rewards_time_discount',
        __( 'Happy Hour Settings', 'textdomain' ),
        'rewards_meta_box_callback',
        'rewards',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'rewards_add_meta_box' );


// 3. Meta Box Content
function rewards_meta_box_callback( $post ) {
    wp_nonce_field( 'rewards_save_meta_box_data', 'rewards_meta_box_nonce' );

    $from_hours = get_post_meta( $post->ID, '_from_hours', true );
    $to_hours   = get_post_meta( $post->ID, '_to_hours', true );
    $discount   = get_post_meta( $post->ID, '_discount', true );
    ?>

    <table class="form-table">
        <tr>
            <th><label for="from_hours"><?php _e( 'From Hour', 'textdomain' ); ?></label></th>
            <td>
                <input type="number" id="from_hours" name="from_hours" value="<?php echo esc_attr( $from_hours ); ?>" style="width:200px;" />
               
            </td>
        </tr>
        <tr>
            <th><label for="to_hours"><?php _e( 'To Hour', 'textdomain' ); ?></label></th>
            <td>
                <input type="number" id="to_hours" name="to_hours" value="<?php echo esc_attr( $to_hours ); ?>" style="width:200px;" />
           
            </td>
        </tr>
        <tr>
            <th><label for="discount"><?php _e( 'Discount (%)', 'textdomain' ); ?></label></th>
            <td>
                <input type="number" id="discount" name="discount" min="0" max="100" step="1"
                       value="<?php echo esc_attr( $discount ); ?>" style="width:100px;" /> %
                <span class="description">Enter a number from 0 to 100 (e.g. 20 = 20% off)</span>
            </td>
        </tr>
    </table>

    <?php
}


// 4. Save Meta Box Data
function rewards_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['rewards_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['rewards_meta_box_nonce'], 'rewards_save_meta_box_data' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save From Hour
    if ( isset( $_POST['from_hours'] ) ) {
        update_post_meta( $post_id, '_from_hours', sanitize_text_field( $_POST['from_hours'] ) );
    }

    // Save To Hour
    if ( isset( $_POST['to_hours'] ) ) {
        update_post_meta( $post_id, '_to_hours', sanitize_text_field( $_POST['to_hours'] ) );
    }

    // Save Discount
    if ( isset( $_POST['discount'] ) ) {
        $discount = absint( $_POST['discount'] );
        $discount = min( 100, max( 0, $discount ) );
        update_post_meta( $post_id, '_discount', $discount );
    }
}
add_action( 'save_post_rewards', 'rewards_save_meta_box_data' );


// 5. Add custom columns to admin list (optional but very useful)
function rewards_custom_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        if ( $key === 'title' ) {
            $new_columns['from_hours'] = __( 'From', 'textdomain' );
            $new_columns['to_hours']   = __( 'To', 'textdomain' );
            $new_columns['discount']   = __( 'Discount', 'textdomain' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_rewards_posts_columns', 'rewards_custom_columns' );

function rewards_custom_column_data( $column, $post_id ) {
    switch ( $column ) {
        case 'from_hours':
            echo esc_html( get_post_meta( $post_id, '_from_hours', true ) ?: '—' );
            break;
        case 'to_hours':
            echo esc_html( get_post_meta( $post_id, '_to_hours', true ) ?: '—' );
            break;
        case 'discount':
            $d = get_post_meta( $post_id, '_discount', true );
            echo $d !== '' && $d !== false ? esc_html( $d ) . '%' : '—';
            break;
    }
}
add_action( 'manage_rewards_posts_custom_column', 'rewards_custom_column_data', 10, 2 );


// 6. Add submenu "Generate CE Rewards" under Rewards CPT
function cer_add_generate_rewards_menu() {
    add_submenu_page(
        'edit.php?post_type=rewards',          // Parent menu slug
        'Generate CE Rewards',                  // Page title
        'Generate CE Rewards',                  // Menu title
        'manage_options',                       // Capability
        'generate-ce-rewards',                  // Menu slug
        'cer_generate_rewards_page_callback'    // Callback function
    );
}
add_action('admin_menu', 'cer_add_generate_rewards_menu');

// 7. Page content + form
function cer_generate_rewards_page_callback() {
    // Only admins can access
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Handle form submission
    if (isset($_POST['generate_ce_rewards']) && wp_verify_nonce($_POST['cer_nonce'], 'generate_ce_rewards')) {
        cer_create_all_rewards();
        echo '<div class="updated"><p>All 8 CE Rewards have been created/updated successfully!</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Generate CE Rewards™ Levels</h1>
        <p>This tool will create (or update if titles match) all 8 reward tiers according to the official discount table.</p>

        <form method="post">
            <?php wp_nonce_field('generate_ce_rewards', 'cer_nonce'); ?>
            <p>
                <input type="submit" name="generate_ce_rewards" class="button button-primary button-large" value="Generate All 8 Reward Levels Now" />
            </p>
        </form>

        <h2>Preview of what will be created:</h2>
        <table class="widefat fixed" style="max-width:600px;">
            <thead><tr><th>Title</th><th>From Hours</th><th>To Hours</th><th>Discount</th></tr></thead>
            <tbody>
                <tr><td>First Rank</td><td>1</td><td>10</td><td>0</td></tr>
                <tr><td>Second Rank</td><td>11</td><td>20</td><td>5</td></tr>
                <tr><td>Third Rank</td><td>21</td><td>40</td><td>10</td></tr>
                <tr><td>Fourth Rank</td><td>41</td><td>80</td><td>15</td></tr>
                <tr><td>Fifth Rank</td><td>81</td><td>150</td><td>20</td></tr>
                <tr><td>Sixth Rank</td><td>151</td><td>300</td><td>25</td></tr>
                <tr><td>Seventh Rank</td><td>301</td><td>500</td><td>30</td></tr>
                <tr><td>Eighth Rank</td><td>501</td><td>999999</td><td>35</td></tr>
            </tbody>
        </table>
    </div>
    <?php
}

// 8. Main function to create/update all rewards
function cer_create_all_rewards() {
    $levels = array(
        array('title' => 'First Rank',   'from' => 1,    'to' => 10,   'discount' => 0),
        array('title' => 'Second Rank',  'from' => 11,   'to' => 20,   'discount' => 5),
        array('title' => 'Third Rank',   'from' => 21,   'to' => 40,   'discount' => 10),
        array('title' => 'Fourth Rank',  'from' => 41,   'to' => 80,   'discount' => 15),
        array('title' => 'Fifth Rank',   'from' => 81,   'to' => 150,  'discount' => 20),
        array('title' => 'Sixth Rank',   'from' => 151,  'to' => 300,  'discount' => 25),
        array('title' => 'Seventh Rank', 'from' => 301,  'to' => 500,  'discount' => 30),
        array('title' => 'Eighth Rank',  'from' => 501,  'to' => 999999, 'discount' => 35),
    );

    foreach ($levels as $level) {
        // Check if reward with this title already exists
        $existing = get_page_by_title($level['title'], OBJECT, 'rewards');

        $post_data = array(
            'post_title'   => $level['title'],
            'post_status'  => 'publish',
            'post_type'    => 'rewards',
        );

        if ($existing) {
            // Update existing
            $post_data['ID'] = $existing->ID;
            wp_update_post($post_data);
            $post_id = $existing->ID;
        } else {
            // Create new
            $post_id = wp_insert_post($post_data);
        }

        // Save meta fields (only numbers, no %)
        if ($post_id && !is_wp_error($post_id)) {
            update_post_meta($post_id, '_from_hours', $level['from']);
            update_post_meta($post_id, '_to_hours',   $level['to']);
            update_post_meta($post_id, '_discount',   $level['discount']);
        }
    }
}
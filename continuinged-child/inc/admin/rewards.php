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
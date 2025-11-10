<?php

add_action( 'init', function() {
    register_term_meta( 'course_cat', 'order', array(
        'type'         => 'integer',
        'description'  => __( 'Order for course category', 'text-domain' ),
        'single'       => true,
        'default'      => 0,
        'show_in_rest' => true, 
        'sanitize_callback' => 'absint',
    ) );

    // Register icon meta field
    register_term_meta( 'course_cat', 'icon', array(
        'type'         => 'string',
        'description'  => __( 'Bootstrap icon class for course category', 'text-domain' ),
        'single'       => true,
        'default'      => '',
        'show_in_rest' => true, 
        'sanitize_callback' => 'sanitize_text_field',
    ) );
} );


add_action( 'course_cat_add_form_fields', function() {
    ?>
    <div class="form-field term-order-wrap">
        <label for="term_order"><?php _e( 'Order', 'text-domain' ); ?></label>
        <input type="number" name="term_order" id="term_order" value="0" min="0">
        <p><?php _e( 'Enter the order for this course category (default is 0).', 'text-domain' ); ?></p>
    </div>
    
    <div class="form-field term-icon-wrap">
        <label for="term_icon"><?php _e( 'Bootstrap Icon', 'text-domain' ); ?></label>
        <input type="text" name="term_icon" id="term_icon" value="" placeholder="bi-house">
        <p>
            <?php _e( 'Enter Bootstrap icon class (e.g., bi-house, bi-book, bi-person). ', 'text-domain' ); ?>
            <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e( 'Browse Bootstrap Icons', 'text-domain' ); ?></a>
        </p>
        <div id="icon-preview" style="margin-top: 10px;">
            <i class="bi" style="font-size: 24px;"></i>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('term_icon');
        const iconPreview = document.querySelector('#icon-preview i');
        
        iconInput.addEventListener('input', function() {
            const iconClass = this.value.trim();
            iconPreview.className = 'bi ' + iconClass;
        });
    });
    </script>
    <?php
} );


add_action( 'course_cat_edit_form_fields', function( $term ) {
    $order = get_term_meta( $term->term_id, 'order', true );
    $order = ! empty( $order ) ? absint( $order ) : 0;
    
    $icon = get_term_meta( $term->term_id, 'icon', true );
    $icon = ! empty( $icon ) ? esc_attr( $icon ) : '';
    ?>
    <tr class="form-field">
        <th scope="row"><label for="term_order"><?php _e( 'Order', 'text-domain' ); ?></label></th>
        <td>
            <input type="number" name="term_order" id="term_order" value="<?php echo esc_attr( $order ); ?>" min="0">
            <p class="description"><?php _e( 'Enter the order for this course category (default is 0).', 'text-domain' ); ?></p>
        </td>
    </tr>
    
    <tr class="form-field">
        <th scope="row"><label for="term_icon"><?php _e( 'Bootstrap Icon', 'text-domain' ); ?></label></th>
        <td>
            <input type="text" name="term_icon" id="term_icon" value="<?php echo $icon; ?>" placeholder="bi-house">
            <p class="description">
                <?php _e( 'Enter Bootstrap icon class (e.g., bi-house, bi-book, bi-person). ', 'text-domain' ); ?>
                <a href="https://icons.getbootstrap.com/" target="_blank"><?php _e( 'Browse Bootstrap Icons', 'text-domain' ); ?></a>
            </p>
            <div id="icon-preview" style="margin-top: 10px;">
                <i class="bi <?php echo $icon; ?>" style="font-size: 24px;"></i>
            </div>
        </td>
    </tr>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('term_icon');
        const iconPreview = document.querySelector('#icon-preview i');
        
        iconInput.addEventListener('input', function() {
            const iconClass = this.value.trim();
            iconPreview.className = 'bi ' + iconClass;
        });
    });
    </script>
    <?php
} );


add_action( 'created_course_cat', 'save_course_cat_order_meta' );
add_action( 'edited_course_cat', 'save_course_cat_order_meta' );
function save_course_cat_order_meta( $term_id ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Save order
    if ( isset( $_POST['term_order'] ) ) {
        $order = absint( $_POST['term_order'] ); 
        update_term_meta( $term_id, 'order', $order );
    } else {
        update_term_meta( $term_id, 'order', 0 );
    }
    
    // Save icon
    if ( isset( $_POST['term_icon'] ) ) {
        $icon = sanitize_text_field( $_POST['term_icon'] );
        update_term_meta( $term_id, 'icon', $icon );
    } else {
        delete_term_meta( $term_id, 'icon' );
    }
}


add_filter( 'manage_edit-course_cat_columns', 'add_course_cat_order_column' );
function add_course_cat_order_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'name' ) {
            $new_columns['icon'] = __( 'Icon', 'text-domain' );
            $new_columns['order'] = __( 'Order', 'text-domain' );
        }
    }
    return $new_columns;
}

add_filter( 'manage_course_cat_custom_column', 'show_course_cat_order_column', 10, 3 );
function show_course_cat_order_column( $content, $column_name, $term_id ) {
    if ( $column_name === 'order' ) {
        $order = get_term_meta( $term_id, 'order', true );
        $content = ( $order !== '' ) ? absint( $order ) : 0;
    }
    
    if ( $column_name === 'icon' ) {
        $icon = get_term_meta( $term_id, 'icon', true );
        if ( ! empty( $icon ) ) {
            $content = '<i class="bi ' . esc_attr( $icon ) . '" style="font-size: 20px;"></i> <code>' . esc_html( $icon ) . '</code>';
        } else {
            $content = '—';
        }
    }
    
    return $content;
}


add_filter( 'manage_edit-course_cat_sortable_columns', 'make_course_cat_order_sortable' );
function make_course_cat_order_sortable( $sortable ) {
    $sortable['order'] = 'order';
    return $sortable;
}


add_filter( 'get_terms_args', 'sort_course_cat_by_order', 10, 2 );
function sort_course_cat_by_order( $args, $taxonomies ) {
    if ( ! is_admin() ) {
        return $args;
    }
    
    if ( ! is_array( $taxonomies ) ) {
        $taxonomies = array( $taxonomies );
    }
    
    if ( ! in_array( 'course_cat', $taxonomies ) ) {
        return $args;
    }
    
    global $pagenow;
    if ( $pagenow !== 'edit-tags.php' ) {
        return $args;
    }
    
    if ( isset( $_GET['orderby'] ) && $_GET['orderby'] === 'order' ) {
        $args['meta_key'] = 'order';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = isset( $_GET['order'] ) ? strtoupper( $_GET['order'] ) : 'ASC';
    } 
    elseif ( ! isset( $_GET['orderby'] ) ) {
        $args['meta_key'] = 'order';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
    }
    
    return $args;
}

// Load Bootstrap Icons CSS in admin
add_action( 'admin_enqueue_scripts', function() {
    $screen = get_current_screen();
    if ( $screen && $screen->taxonomy === 'course_cat' ) {
        wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css' );
    }
} );
?>
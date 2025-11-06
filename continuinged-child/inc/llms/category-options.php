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
} );


add_action( 'course_cat_add_form_fields', function() {
    ?>
    <div class="form-field term-order-wrap">
        <label for="term_order"><?php _e( 'Order', 'text-domain' ); ?></label>
        <input type="number" name="term_order" id="term_order" value="0" min="0">
        <p><?php _e( 'Enter the order for this course category (default is 0).', 'text-domain' ); ?></p>
    </div>
    <?php
} );


add_action( 'course_cat_edit_form_fields', function( $term ) {
    $order = get_term_meta( $term->term_id, 'order', true );
    $order = ! empty( $order ) ? absint( $order ) : 0;
    ?>
    <tr class="form-field">
        <th scope="row"><label for="term_order"><?php _e( 'Order', 'text-domain' ); ?></label></th>
        <td>
            <input type="number" name="term_order" id="term_order" value="<?php echo esc_attr( $order ); ?>" min="0">
            <p class="description"><?php _e( 'Enter the order for this course category (default is 0).', 'text-domain' ); ?></p>
        </td>
    </tr>
    <?php
} );


add_action( 'created_course_cat', 'save_course_cat_order_meta' );
add_action( 'edited_course_cat', 'save_course_cat_order_meta' );
function save_course_cat_order_meta( $term_id ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( isset( $_POST['term_order'] ) ) {
        $order = absint( $_POST['term_order'] ); 
        update_term_meta( $term_id, 'order', $order );
    } else {
        update_term_meta( $term_id, 'order', 0 );
    }
}


add_filter( 'manage_edit-course_cat_columns', 'add_course_cat_order_column' );
function add_course_cat_order_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[$key] = $value;
        if ( $key === 'name' ) {
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
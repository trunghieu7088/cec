<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class MyLifterLMS_Courses
 *
 * A Singleton class to retrieve and manage LifterLMS course data.
 */
class MyLifterLMS_Courses {

    /**
     * The single instance of the class.
     *
     * @var MyLifterLMS_Courses|null
     */
    protected static $instance = null;

    /**
     * MyLifterLMS_Courses constructor.
     *
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {
        // Prevent direct instantiation
    }

    /**
     * Get the single instance of the class.
     *
     * @return MyLifterLMS_Courses
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {
        // Prevent cloning
    }

    /**
     * Prevent unserializing of the instance.
     */
    private function __wakeup() {
        // Prevent unserializing
    }

    /**
     * Main function to retrieve courses based on parameters.
     *
     * @param array $args An array of arguments for the query.
     *                    - 'post_status' (string): Post status (default: 'publish').
     *                    - 'category_id' (int|array): ID(s) of course categories (course_cat).
     *                                                  If not provided, all categories are included.
     *                    - Any other WP_Query arguments.
     * @return array An array of course objects, each containing relevant data.
     */
    public function get_courses( $args = array() ) {
        $default_args = array(
            'post_type'      => 'course',
            'post_status'    => 'publish',
            'posts_per_page' => -1, // Get all courses by default
            'fields'         => 'ids', // Get only IDs first for better performance, then fetch full data
        );

        $args = wp_parse_args( $args, $default_args );

        // Handle category_id for course_cat taxonomy
        if ( isset( $args['category_id'] ) && ! empty( $args['category_id'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'course_cat',
                    'field'    => 'term_id',
                    'terms'    => (array) $args['category_id'],
                ),
            );
            unset( $args['category_id'] ); // Remove from main args to avoid conflict
        }

        $course_ids = get_posts( $args );
        $courses    = array();

        if ( ! empty( $course_ids ) ) {
            foreach ( $course_ids as $course_id ) {
                $course_data = $this->get_single_course_data( $course_id );
                if ( $course_data ) {
                    $courses[] = $course_data;
                }
            }
        }

        return $courses;
    }

    /**
     * Retrieves all data for a single course.
     *
     * @param int $course_id The ID of the course.
     * @return array|false An array of course data, or false if the course is not found.
     */
    public function get_single_course_data( $course_id ) {
        $course_post = get_post( $course_id );

        if ( ! $course_post || 'course' !== $course_post->post_type ) {
            return false;
        }

        $data = array(
            'ID'                   => $course_post->ID,
            'post_title'           => $course_post->post_title,
            'post_status'          => $course_post->post_status,
            'post_content'         => $course_post->post_content, // Add content if needed
            'post_excerpt'         => $course_post->post_excerpt, // Add excerpt if needed
            'course_link'          => get_permalink( $course_post->ID ),
        );

        // Get custom post meta
        $meta_keys = array(
            '_llms_ce_hours',
            '_course_introduction',
            '_course_outline',
            '_course_objectives',
            '_course_last_revised',
            '_course_copyright',
            '_status_update_label',
            '_course_main_content',
        );

        foreach ( $meta_keys as $key ) {
            $data[ str_replace( '_', '', $key ) ] = get_post_meta( $course_post->ID, $key, true );
        }

        // Get course_cat terms
        $course_categories = wp_get_post_terms( $course_post->ID, 'course_cat', array( 'fields' => 'all' ) );
        $data['course_categories'] = array();
        foreach ( $course_categories as $term ) {
            $data['course_categories'][] = array(
                'term_id'   => $term->term_id,
                'name'      => $term->name,
                'slug'      => $term->slug,
                'link'      => get_term_link( $term ),
            );
        }

        // Get course_difficulty terms
        $course_difficulties = wp_get_post_terms( $course_post->ID, 'course_difficulty', array( 'fields' => 'all' ) );
        $data['course_difficulties'] = array();
        foreach ( $course_difficulties as $term ) {
            $data['course_difficulties'][] = array(
                'term_id'   => $term->term_id,
                'name'      => $term->name,
                'slug'      => $term->slug,
                'link'      => get_term_link( $term ),
            );
        }

        // Handle _llms_instructors meta
        $instructors_meta = get_post_meta( $course_post->ID, '_llms_instructors', true );
        $data['instructors'] = $this->parse_llms_instructors( $instructors_meta );

        // Get Access Plans data (specifically price)
        $data['access_plans'] = $this->get_course_access_plans_price( $course_post->ID );

        return $data;
    }

    /**
     * Parses the _llms_instructors meta data to get instructor details.
     *
     * @param array|string $instructors_meta The raw instructors meta data.
     * @return array An array of instructor objects.
     */
    protected function parse_llms_instructors( $instructors_meta ) {
        $instructors = array();
        if ( ! empty( $instructors_meta ) && is_array( $instructors_meta ) ) {
            foreach ( $instructors_meta as $instructor_data ) {
                // only get instructor has visibility ()'visible' )
                if ( isset( $instructor_data['id'] ) && isset( $instructor_data['visibility'] ) && 'visible' === $instructor_data['visibility'] ) {
                    $user_id = (int) $instructor_data['id'];
                    $user    = get_user_by( 'id', $user_id );
                    if ( $user ) {
                        $instructor_info = array(
                            'id'           => $user_id,
                            'display_name' => $user->display_name,
                            'user_login'   => $user->user_login,
                            'user_email'   => $user->user_email,
                            'profile_url'  => get_author_posts_url( $user_id ),
                            'visibility'   => $instructor_data['visibility'], 
                        );

                        
                        $instructor_info['avatar']                   = get_avatar_url( $user_id ); 
                        $instructor_info['llms_instructor_website']  = get_user_meta( $user_id, 'llms_instructor_website', true );
                        $instructor_info['llms_instructor_bio']      = get_user_meta( $user_id, 'llms_instructor_bio', true );
                        $instructor_info['llms_degrees_certs']       = get_user_meta( $user_id, 'llms_degrees_certs', true );

                        $instructors[] = $instructor_info;
                    }
                }
            }
        }
        return $instructors;
    }

    /**
     * Retrieves the price from visible access plans for a given course.
     *
     * @param int $course_id The ID of the course.
     * @return array An array of access plan prices.
     */
    protected function get_course_access_plans_price( $course_id ) {
      //  $prices = array();        

        $course_instance = llms_get_product( $course_id );    

        $plans = $course_instance->get_access_plans( $free_only = false, $visible_only = true );
          
        if ( ! empty( $plans ) )
        {
            return $plans[0];
        }
        return array();
    }

    public function get_instructors_list() {
  
        $args = array(
            'role'    => 'instructor',
            'orderby' => 'display_name', 
            'order'   => 'ASC',
        );
        $user_query = new WP_User_Query( $args );

        if ( ! empty( $user_query->get_results() ) ) {
            $instructors = array();
        
            foreach ( $user_query->get_results() as $user ) {
                $instructor_data = array(
                    'user_id'         => $user->ID,
                    'user_login'       =>$user->user_login,
                    'display_name'    => $user->display_name,
                    'email'           => $user->user_email,
                    'avatar_url'      => get_avatar_url( $user->ID ), 
                    'website'         => get_user_meta( $user->ID, 'llms_instructor_website', true ),
                    'degrees_certs'   => get_user_meta( $user->ID, 'llms_degrees_certs', true ), 
                    'bio'             => get_user_meta( $user->ID, 'llms_instructor_bio', true ),
                );

                $instructors[] = $instructor_data;
            }

            return $instructors;
        } else {
            return false; 
        }
    }
}

// Global function to get the instance of the class for easier access.
function my_lifterlms_courses() {
    return MyLifterLMS_Courses::get_instance();
}


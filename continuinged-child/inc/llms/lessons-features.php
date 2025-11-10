<?php

class CourseLessonData {

    private static $instance = null;

    private function __construct() {
      
    }

   
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

 
    public function get_first_lesson_id_from_course( $course_id ) {
        if ( ! is_numeric( $course_id ) || $course_id <= 0 ) {
            return false;
        }

        $args = array(
            'post_type'      => 'lesson',
            'posts_per_page' => 1, 
            'meta_query'     => array(
                array(
                    'key'   => '_llms_parent_course',
                    'value' => $course_id,
                    'compare' => '=',
                ),
            ),
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'ASC',
        );

        $lessons = new WP_Query( $args );

        if ( $lessons->have_posts() ) {
            return $lessons->posts[0]; 
        }

        return false;
    }


    public function get_quiz_by_lesson_id( $lesson_id ) {
   
            $lesson_id = absint( $lesson_id );

            if ( ! $lesson_id ) {
                return null;
            }
        
            $args = array(
                'post_type'      => 'llms_quiz',  
                'numberposts' => 1,               
                'post_status'    => 'publish',    
                'meta_query'     => array(
                    array(
                        'key'     => '_llms_lesson_id', 
                        'value'   => $lesson_id,        
                        'compare' => '=',                
                        'type'    => 'NUMERIC',          
                    ),
                ),
            );

            $quizzes = get_posts( $args );

            if ( ! empty( $quizzes ) && is_array( $quizzes ) ) {      
                return $quizzes[0];
            }
                return null;
    }

   
    public function get_course_structured_data( $course_id ) {
        $first_lesson_id = $this->get_first_lesson_id_from_course( $course_id );
        if($first_lesson_id)
        {
            $quiz=$this->get_quiz_by_lesson_id($first_lesson_id);
            if($quiz)
            {
                 if ( $quiz ) {
                $quiz_id = $quiz->ID;
                $questions_data = array();

                // Lấy tất cả các câu hỏi có post type là llms_question và meta key _llms_parent_id = $quiz_id
                $question_args = array(
                    'post_type'      => 'llms_question',
                    'posts_per_page' => -1, // Lấy tất cả câu hỏi
                    'meta_query'     => array(
                        array(
                            'key'     => '_llms_parent_id',
                            'value'   => $quiz_id,
                            'compare' => '=',
                            'type'    => 'NUMERIC',
                        ),
                    ),
                    'orderby'        => 'menu_order', // Sắp xếp theo thứ tự hiển thị nếu có
                    'order'          => 'ASC',
                );

                $questions = get_posts( $question_args );

                if ( ! empty( $questions ) ) {
                    foreach ( $questions as $question ) {
                        $question_id = $question->ID;
                        $question_title = $question->post_title;
                        $question_content = $question->post_content;
                        
                        $choices = array();
                        
                        // Lấy tất cả các meta data của câu hỏi
                        $question_meta = get_post_meta( $question_id );

                        foreach ( $question_meta as $meta_key => $meta_value ) {
                            // Kiểm tra nếu meta_key có dạng _llms_choice_XXXXXX
                            if ( preg_match( '/^_llms_choice_[a-f0-9]+$/', $meta_key ) ) {
                                // meta_value là một mảng, lấy phần tử đầu tiên
                                $serialized_choice = $meta_value[0];
                                
                                // Giải mã dữ liệu
                                $unserialized_choice = unserialize( $serialized_choice );
                                
                                if ( is_array( $unserialized_choice ) ) {
                                    $choices[] = $unserialized_choice;
                                }
                            }
                        }

                        $questions_data[] = array(
                            'question_id'      => $question_id,
                            'question_title'   => $question_title,
                            'question_content' => $question_content,
                            'choices'          => $choices,
                        );
                    }
                }
                return $questions_data;
            }
            }
        }
    
        return false;
    }
}
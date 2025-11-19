<?php
/**
 * Template Name: Search Course Results Page
 * Description: Template for displaying search results for courses and categories
 */

get_header();

$search_term = '';
if ( get_query_var( 'search_term' ) ) {
    $search_term = sanitize_text_field( urldecode( get_query_var( 'search_term' ) ) );
} elseif ( isset( $_GET['s'] ) ) {
    $search_term = sanitize_text_field( $_GET['s'] );
}
$search_term = $_GET['search'];
?>

<div class="search-results-page">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="search-header mb-4">
                    <h1>Search Results</h1>
                    <?php if ($search_term): ?>
                        <p class="lead">Showing results for: <strong>"<?php echo esc_html($search_term); ?>"</strong></p>
                    <?php endif; ?>
                    
                    <!-- Search form -->
                    <form method="get" class="search-form mb-4">
                        <div class="input-group">
                            <input 
                                type="text" 
                                name="s" 
                                class="form-control" 
                                placeholder="Search courses and categories..." 
                                value="<?php echo esc_attr($search_term); ?>"
                                required
                            >
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <?php if ($search_term): ?>
                    
                    <!-- Courses Section -->
                    <div class="courses-section mb-5">
                        <h2 class="section-title mb-4">
                            <i class="bi bi-book"></i> Courses
                        </h2>
                        
                        <?php
                        // Query courses by title only
                        $course_args = array(
                            'post_type' => 'course',
                            'post_status' => 'publish',
                            'posts_per_page' => 20,
                            's' => $search_term,
                            'orderby' => 'relevance',
                        );
                        
                        $course_query = new WP_Query($course_args);
                        
                        if ($course_query->have_posts()):
                        ?>
                            <div class="row">
                                <?php while ($course_query->have_posts()): $course_query->the_post(); ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 course-card">
                                            <?php if (has_post_thumbnail()): ?>
                                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" 
                                                     class="card-img-top" 
                                                     alt="<?php echo esc_attr(get_the_title()); ?>">
                                            <?php endif; ?>
                                            
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </h5>
                                                <p class="card-text">
                                                    <?php 
                                                    $main_content = get_post_meta(get_the_ID(), '_course_main_content', true);
                                                    echo wp_trim_words(wp_strip_all_tags($main_content), 20, '...'); 
                                                    ?>
                                                </p>
                                                <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                                                    View Course
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <?php
                            // Pagination
                            if ($course_query->max_num_pages > 1):
                            ?>
                                <nav aria-label="Courses pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php
                                        echo paginate_links(array(
                                            'total' => $course_query->max_num_pages,
                                            'current' => max(1, get_query_var('paged')),
                                            'format' => '?s=' . urlencode($search_term) . '&paged=%#%',
                                            'prev_text' => '&laquo; Previous',
                                            'next_text' => 'Next &raquo;',
                                            'type' => 'list',
                                        ));
                                        ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No courses found matching your search.
                            </div>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>

                    <!-- Categories Section -->
                    <div class="categories-section mb-5">
                        <h2 class="section-title mb-4">
                            <i class="bi bi-folder"></i> Categories
                        </h2>
                        
                        <?php
                        // Search categories by name only
                        $category_args = array(
                            'taxonomy' => 'course_cat',
                            'hide_empty' => false,
                            'name__like' => $search_term,
                        );
                        
                        $categories = get_terms($category_args);
                        
                        if (!is_wp_error($categories) && !empty($categories)):
                        ?>
                            <div class="row">
                                <?php foreach ($categories as $category): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card h-100 category-card">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="<?php echo get_term_link($category); ?>">
                                                        <?php echo esc_html($category->name); ?>
                                                    </a>
                                                </h5>
                                                
                                                <?php if ($category->description): ?>
                                                    <p class="card-text">
                                                        <?php echo wp_trim_words($category->description, 20, '...'); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <p class="text-muted mb-2">
                                                    <i class="bi bi-book"></i> 
                                                    <?php echo $category->count; ?> 
                                                    <?php echo $category->count === 1 ? 'course' : 'courses'; ?>
                                                </p>
                                                
                                                <a href="<?php echo get_term_link($category); ?>" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    View Category
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No categories found matching your search.
                            </div>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Please enter a search term.
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<style>
.search-results-page {
    min-height: 60vh;
}

.search-results-page .search-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 20px;
}

.search-results-page .section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    border-left: 4px solid #0d6efd;
    padding-left: 15px;
}

.search-results-page .course-card,
.search-results-page .category-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.search-results-page .course-card:hover,
.search-results-page .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.search-results-page .card-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.2s;
}

.search-results-page .card-title a:hover {
    color: #0d6efd;
}

.search-results-page .card-img-top {
    height: 200px;
    object-fit: cover;
}

.search-results-page .search-form .input-group {
    max-width: 600px;
}

.search-results-page .pagination {
    margin-top: 30px;
}
</style>

<?php get_footer(); ?>
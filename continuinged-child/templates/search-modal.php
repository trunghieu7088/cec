<!-- Search Modal -->
<div class="modal fade search-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Search Courses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="searchForm" method="POST">
                    <div class="input-group">
                        <input 
                            type="text" 
                            class="form-control search-input" 
                            id="courseSearch" 
                            name="s"
                            placeholder="Please enter the keyword..."
                            autocomplete="off"
                            required
                        >
                        <button class="btn btn-primary" type="submit" id="searchButton">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                <div class="search-results mt-3" id="searchResults"></div>
            </div>
        </div>
    </div>
</div>

<style>
.search-modal .search-results {
    max-height: 400px;
    overflow-y: auto;
}

.search-modal .result-section {
    margin-bottom: 20px;
}

.search-modal .result-section h6 {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.search-modal .result-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.search-modal .result-item:hover {
    background-color: #f8f9fa;
}

.search-modal .result-item h5 {
    font-size: 16px;
    margin-bottom: 5px;
    color: #333;
}

.search-modal .result-item p {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.search-modal .result-item .badge {
    font-size: 12px;
}

.search-modal .no-results {
    text-align: center;
    padding: 20px;
    color: #999;
}

.search-modal .loading {
    text-align: center;
    padding: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    let searchTimeout;
    
    // Real-time search as user types
    $('#courseSearch').on('input', function() {
        const searchTerm = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length < 2) {
            $('#searchResults').html('');
            return;
        }
        
        searchTimeout = setTimeout(function() {
            performSearch(searchTerm, false);
        }, 300);
    });
    
    // Handle form submission
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#courseSearch').val().trim();
        
        if (searchTerm.length > 0) {
            // Redirect to search results page
            window.location.href = '<?php echo get_custom_page_url_by_template('page-search-results.php'); ?>?search=' + encodeURIComponent(searchTerm);
        }
    });
    
    // Perform AJAX search for preview
    function performSearch(searchTerm, isFullSearch) {
        $('#searchResults').html('<div class="loading"><div class="spinner-border spinner-border-sm" role="status"></div> Searching...</div>');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'search_courses',
                nonce: '<?php echo wp_create_nonce('search_courses_nonce'); ?>',
                search_term: searchTerm
            },
            success: function(response) {
                if (response.success) {
                    displayResults(response.data);
                } else {
                    $('#searchResults').html('<div class="no-results">No results found</div>');
                }
            },
            error: function() {
                $('#searchResults').html('<div class="no-results">Error occurred while searching</div>');
            }
        });
    }
    
    // Display search results
    function displayResults(data) {
        let html = '';
        
        // Display courses
        if (data.courses && data.courses.length > 0) {
            html += '<div class="result-section">';
            html += '<h6>Courses (' + data.courses.length + ')</h6>';
            data.courses.forEach(function(course) {
                html += '<div class="result-item" onclick="window.location.href=\'' + course.url + '\'">';
                html += '<h5>' + course.title + '</h5>';
                if (course.excerpt) {
                    html += '<p>' + course.excerpt + '</p>';
                }
                html += '</div>';
            });
            html += '</div>';
        }
        
        // Display categories
        if (data.categories && data.categories.length > 0) {
            html += '<div class="result-section">';
            html += '<h6>Categories (' + data.categories.length + ')</h6>';
            data.categories.forEach(function(category) {
                html += '<div class="result-item" onclick="window.location.href=\'' + category.url + '\'">';
                html += '<h5>' + category.name + ' <span class="badge bg-secondary">' + category.count + ' courses</span></h5>';
                html += '</div>';
            });
            html += '</div>';
        }
        
        if (html === '') {
            html = '<div class="no-results">No results found</div>';
        }
        
        $('#searchResults').html(html);
    }
});
</script>
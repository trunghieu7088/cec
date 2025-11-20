jQuery(document).ready(function($) {
	let searchTimeout;
	
	$('#courseSearch').on('input', function() {
		const searchTerm = $(this).val().trim();
		
		clearTimeout(searchTimeout);
		
		if (searchTerm.length < 3) {
			$('#searchResults').html('<p>The search term must be at least 3 characters long.</p>');
			return;
		}
		
		$('#searchResults').html('<div class="search-loading">Searching...</div>');
		
		searchTimeout = setTimeout(function() {
			// Gọi standalone API thay vì admin-ajax
			$.ajax({
				url: '/wp-content/course-search-api.php',
				type: 'GET', // ← Đổi từ POST sang GET
				data: {
					q: searchTerm // ← Đổi từ search_term sang q
				},
				dataType: 'json',
				success: function(response) {
					// Kiểm tra response từ standalone API
					if (response.success && response.results && response.results.length > 0) {
						let html = '';
						
						// Hiển thị execution time (optional)
						html += '<div class="search-stats" style="font-size: 12px; color: #999; margin-bottom: 10px;">';
						html += 'Found ' + response.total + ' results in ' + response.execution_time;
						html += '</div>';
						
						// Hiển thị kết quả
						response.results.forEach(function(course) {
							html += '<div class="search-result-item" onclick="window.location.href=\'' + course.url + '\'">';
							html += '<div class="search-result-title">' + course.title + '</div>';
							if (course.excerpt) {
								html += '<p class="search-result-excerpt">' + course.excerpt + '</p>';
							}
							html += '</div>';
						});
						
						$('#searchResults').html(html);
					} else {
						$('#searchResults').html('<div class="search-no-results">No courses found.</div>');
					}
				},
				error: function(xhr, status, error) {
					console.error('Search error:', error);
					
					// Xử lý các loại lỗi khác nhau
					let errorMessage = 'Something went wrong, Please try again.';
					
					if (xhr.responseJSON && xhr.responseJSON.error) {
						errorMessage = xhr.responseJSON.error;
						
						if (xhr.responseJSON.message) {
							errorMessage += ': ' + xhr.responseJSON.message;
						}
					}
					
					$('#searchResults').html('<div class="search-no-results">' + errorMessage + '</div>');
				}
			});
		}, 300); // ← Giảm từ 500ms xuống 300ms vì API nhanh hơn
	});
	
	// Clear search when modal is hidden
	$('#searchModal').on('hidden.bs.modal', function() {
		$('#courseSearch').val('');
		$('#searchResults').html('');
	});
	
	// Focus on input when modal is shown
	$('#searchModal').on('shown.bs.modal', function() {
		$('#courseSearch').focus();
	});
});
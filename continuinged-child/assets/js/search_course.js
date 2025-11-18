jQuery(document).ready(function($) {
		let searchTimeout;
		
		$('#courseSearch').on('input', function() {
			const searchTerm = $(this).val().trim();
			
			clearTimeout(searchTimeout);
			
			if (searchTerm.length < 2) {
				$('#searchResults').html('');
				return;
			}
			
			$('#searchResults').html('<div class="search-loading">Searching...</div>');
			
			searchTimeout = setTimeout(function() {
				$.ajax({
					url: ajaxurl_global,
					type: 'POST',
					data: {
						action: 'search_courses',
						search_term: searchTerm,
						nonce: cecAjax.nonce
					},
					success: function(response) {
						if (response.success && response.data.length > 0) {
							let html = '';
							response.data.forEach(function(course) {
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
					error: function() {
						$('#searchResults').html('<div class="search-no-results">Something went wrong, Please try again.</div>');
					}
				});
			}, 500);
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
jQuery(document).ready(function($) {
	let searchTimeout;
	let fuse = null;
	let coursesData = [];
	let isLoading = false;
	let isDataLoaded = false;
	
	// Load tất cả courses khi mở modal lần đầu
	function loadAllCourses() {
		if (isLoading || isDataLoaded) {
			return; // Đã load rồi hoặc đang load
		}
		
		isLoading = true;
		$('#searchResults').html('<div class="search-loading">Loading courses...</div>');
		
		$.ajax({
			url: cecAjax.ajaxurl || '/wp-admin/admin-ajax.php',
			type: 'POST',
			data: {
				action: 'load_courses_search'
			},
			dataType: 'json',
			success: function(response) {
				console.log('AJAX Response:', response);
				console.log('Response data:', response.data);
				
				if (response.success && response.data && response.data.courses) {
					coursesData = response.data.courses;
					
					console.log('Courses loaded:', coursesData.length);
					console.log('First course:', coursesData[0]);
					
					// Khởi tạo Fuse.js với options tối ưu
					// THÊM categories vào keys để search
					fuse = new Fuse(coursesData, {
						keys: [
							{
								name: 'title',
								weight: 0.7  // Title quan trọng nhất
							},
							{
								name: 'categories',  // Search trong array categories
								weight: 0.3
							}
						],
						threshold: 0.3,
						distance: 100,
						minMatchCharLength: 2,
						includeScore: true,
						ignoreLocation: true
					});
					
					$('#searchResults').html('<p class="text-muted">Enter at least 3 characters to search...</p>');
					isLoading = false;
					isDataLoaded = true;
					
					console.log('Fuse initialized:', fuse !== null);
				} else {
					$('#searchResults').html('<div class="search-no-results">Failed to load courses data.</div>');
					isLoading = false;
					isDataLoaded = false;
				}
			},
			error: function(xhr, status, error) {
				console.error('Load courses error:', error);
				console.error('XHR:', xhr);
				$('#searchResults').html('<div class="search-no-results">Failed to load courses. Please refresh the page.</div>');
				isLoading = false;
				isDataLoaded = false;
			}
		});
	}

	loadAllCourses();

	// Search với Fuse.js
	$('#courseSearch').on('input', function() {
		const searchTerm = $(this).val().trim();
		
		clearTimeout(searchTimeout);
		
		// Yêu cầu tối thiểu 3 ký tự
		if (searchTerm.length < 3) {
			$('#searchResults').html('<p class="text-muted">The search term must be at least 3 characters long.</p>');
			return;
		}
		
		// Nếu đang load courses, đợi
		if (isLoading) {
			$('#searchResults').html('<div class="search-loading">Loading courses...</div>');
			return;
		}
		
		// Kiểm tra fuse đã khởi tạo chưa
		if (!fuse) {
			$('#searchResults').html('<div class="search-loading">Initializing search...</div>');
			loadAllCourses();
			return;
		}
		
		$('#searchResults').html('<div class="search-loading">Searching...</div>');
		
		// Debounce search
		searchTimeout = setTimeout(function() {
			const startTime = performance.now();
			
			// Search với Fuse.js
			const results = fuse.search(searchTerm);
			
			const endTime = performance.now();
			const executionTime = ((endTime - startTime) / 1000).toFixed(4) + 's';
			
			if (results.length > 0) {
				let html = '';
				
				// Hiển thị execution time
				html += '<div class="search-stats" style="font-size: 12px; color: #999; margin-bottom: 10px;">';
				html += 'Found ' + results.length + ' results in ' + executionTime;
				html += '</div>';
				
				// Hiển thị kết quả
				results.forEach(function(result) {
					const course = result.item;
					html += '<div class="search-result-item" onclick="window.location.href=\'' + course.url + '\'" style="cursor: pointer;">';
					html += '<div class="search-result-title">' + course.title + '</div>';
					
					// THÊM: Hiển thị categories nếu có
					if (course.categories_str && course.categories_str.length > 0) {
						html += '<div class="search-result-categories" style="font-size: 12px; color: #666; margin-top: 4px;">';
						html += '<i class="bi bi-archive-fill" style="margin-right:5px;"></i>';						
						html += course.categories_str;
						html += '</div>';
					}
					
					html += '</div>';
				});
				
				$('#searchResults').html(html);
			} else {
				$('#searchResults').html('<div class="search-no-results">No courses found for "' + searchTerm + '".</div>');
			}
		}, 200);
	});
	
	// Load courses khi modal được mở
	$('#searchModal').on('shown.bs.modal', function() {
		$('#courseSearch').focus();
		
		if (!isDataLoaded && !isLoading) {
			loadAllCourses();
		}
	});
	
	// Clear search khi đóng modal
	$('#searchModal').on('hidden.bs.modal', function() {
		$('#courseSearch').val('');
		if (isDataLoaded) {
			$('#searchResults').html('<p class="text-muted">Enter at least 3 characters to search...</p>');
		}
	});
});
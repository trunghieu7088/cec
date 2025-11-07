(function($) {
    $(document).ready(function() {
        $('a[href^="#"]').on('click', function(e) {
		e.preventDefault();
		var target = $(this.getAttribute('href'));
		if (target.length) {
			$('html, body').stop().animate({
				scrollTop: target.offset().top - 70
			}, 1000);
		}
	});

	// Show/hide scroll to top button
	$(window).scroll(function() {
		if ($(this).scrollTop() > 300) {
			$('#scrollTop').addClass('show');
		} else {
			$('#scrollTop').removeClass('show');
		}
	});

	// Scroll to top functionality
	$('#scrollTop').on('click', function() {
		$('html, body').animate({scrollTop: 0}, 800);
	});

	// Navbar background change on scroll
	$(window).scroll(function() {
		if ($(this).scrollTop() > 50) {
			$('.navbar').css('box-shadow', '0 4px 20px rgba(0,0,0,0.15)');
		} else {
			$('.navbar').css('box-shadow', '0 2px 10px rgba(0,0,0,0.1)');
		}
	});

	// Course item hover effect
	$('.course-item').hover(
		function() {
			$(this).find('.course-title').css('color', 'var(--secondary-color)');
		},
		function() {
			$(this).find('.course-title').css('color', 'var(--primary-color)');
		}
	);
      
    });
})(jQuery);
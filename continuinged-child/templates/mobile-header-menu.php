<!-- Custom Mobile Menu Styles -->
<style>
/* Mobile Header Menu Bar - Only visible on mobile */
.mobile-header-menu-bar {
	display: none;
	background: #336666;
	padding: 15px 0;
    	position: sticky;
	top: 0;
	z-index: 1000;
	box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.mobile-header-content {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 0 15px;
}

.mobile-menu-toggle {
	background: none;
	border: none;
	color: white;
	font-size: 24px;
	cursor: pointer;
	padding: 0;
	display: flex;
	align-items: center;
}

.mobile-site-title {
	color: white;
	font-size: 18px;
	font-weight: 600;
	margin: 0;
	flex-grow: 1;
	text-align: center;
}

.mobile-search-icon {
	color: white;
	font-size: 20px;
	cursor: pointer;
	padding: 5px;
}

/* Sidebar Menu Overlay */
.mobile-sidebar-overlay {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.5);
	z-index: 9998;
	opacity: 0;
	transition: opacity 0.3s ease;
}

.mobile-sidebar-overlay.active {
	display: block;
	opacity: 1;
}

/* Sidebar Menu */
.mobile-sidebar-menu {
	position: fixed;
	top: 0;
	left: -300px;
	width: 300px;
	height: 100%;
	background: white;
	z-index: 9999;
	overflow-y: auto;
	transition: left 0.3s ease;
	box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.mobile-sidebar-menu.active {
	left: 0;
}

.mobile-sidebar-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 20px;
	border-bottom: 1px solid #e0e0e0;
	background: #f8f9fa;
}

.mobile-sidebar-title {
	font-size: 18px;
	font-weight: 600;
	color: #336666;
	margin: 0;
}

.mobile-sidebar-close {
	background: none;
	border: none;
	font-size: 28px;
	color: #336666;
	cursor: pointer;
	padding: 0;
	line-height: 1;
}

.mobile-sidebar-nav {
	list-style: none;
	padding: 0;
	margin: 0;
}

.mobile-sidebar-nav li {
	border-bottom: 1px solid #e0e0e0;
}

.mobile-sidebar-nav a {
	display: block;
	padding: 15px 20px;
	color: #336666;
	text-decoration: none;
	font-size: 16px;
	transition: background 0.2s ease;
}

.mobile-sidebar-nav a:hover {
	background: #f0f0f0;
}

/* Hamburger Icon Animation */
.hamburger-icon {
	width: 25px;
	height: 20px;
	position: relative;
	display: inline-block;
}

.hamburger-icon span {
	display: block;
	position: absolute;
	height: 3px;
	width: 100%;
	background: white;
	border-radius: 2px;
	opacity: 1;
	left: 0;
	transition: 0.25s ease-in-out;
}

.hamburger-icon span:nth-child(1) {
	top: 0;
}

.hamburger-icon span:nth-child(2) {
	top: 8px;
}

.hamburger-icon span:nth-child(3) {
	top: 16px;
}

/* Show mobile menu bar only on mobile devices */
@media (max-width: 991px) {
	.mobile-header-menu-bar {
		display: block;
	}
    .top-navbar {
        display: none;
    }
}

/* Prevent body scroll when sidebar is open */
body.mobile-menu-open {
	overflow: hidden;
}
</style>
<!-- Mobile Header Menu Bar - Only visible on mobile -->
	<div class="mobile-header-menu-bar">
		<div class="mobile-header-content">
			<button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle Menu">
				<span class="hamburger-icon">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</button>
			<h1 class="mobile-site-title">ContinuingEdCourses</h1>
			<span class="mobile-search-icon" data-bs-toggle="modal" data-bs-target="#searchModal">
				<i class="bi bi-search"></i>
			</span>
		</div>
	</div>

    <!-- Mobile Sidebar Menu -->
	<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>
	<div class="mobile-sidebar-menu" id="mobileSidebarMenu">
		<div class="mobile-sidebar-header">
			<span class="mobile-sidebar-title">Menu</span>
			<button class="mobile-sidebar-close" id="mobileSidebarClose" aria-label="Close Menu">
				&times;
			</button>
		</div>
		<ul class="mobile-sidebar-nav">
			<?php
			// Get menu items
            $current_main_menu_name=get_primary_menu_name_cec('primary') ?: 'main-header-menu';
			$menu_items = wp_get_nav_menu_items($current_main_menu_name);
			if ($menu_items) {
				foreach ($menu_items as $item) {
					if ($item->menu_item_parent == 0) { // Only top-level items
						echo '<li>';
						echo '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
						echo '</li>';
					}
				}
			}
			?>
		</ul>
	</div>
    <!-- JavaScript for Mobile Menu -->
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const menuToggle = document.getElementById('mobileMenuToggle');
		const sidebarMenu = document.getElementById('mobileSidebarMenu');
		const sidebarOverlay = document.getElementById('mobileSidebarOverlay');
		const sidebarClose = document.getElementById('mobileSidebarClose');
		
		// Open sidebar
		if (menuToggle) {
			menuToggle.addEventListener('click', function() {
				sidebarMenu.classList.add('active');
				sidebarOverlay.classList.add('active');
				document.body.classList.add('mobile-menu-open');
			});
		}
		
		// Close sidebar
		function closeSidebar() {
			sidebarMenu.classList.remove('active');
			sidebarOverlay.classList.remove('active');
			document.body.classList.remove('mobile-menu-open');
		}
		
		if (sidebarClose) {
			sidebarClose.addEventListener('click', closeSidebar);
		}
		
		if (sidebarOverlay) {
			sidebarOverlay.addEventListener('click', closeSidebar);
		}
		
		// Close sidebar when clicking on a menu item
		const menuLinks = document.querySelectorAll('.mobile-sidebar-nav a');
		menuLinks.forEach(function(link) {
			link.addEventListener('click', function() {
				closeSidebar();
			});
		});
	});
	</script>
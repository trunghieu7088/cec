
	<!-- Search Modal -->
	<div class="modal fade search-modal" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="searchModalLabel">Search Courses</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="searchForm" onsubmit="return false;">
						<input 
							type="text" 
							class="form-control search-input" 
							id="courseSearch" 
							placeholder="Please enter the keyword..."
							autocomplete="off"
						>
					</form>
					<div class="search-results" id="searchResults"></div>
				</div>
			</div>
		</div>
	</div>

jQuery(document).ready(function($) {
    
    // Format function for row details
    function formatDetails(data) {
        // Parse references if it's JSON
        let references = [];
        try {
            // First try to parse as JSON
            let parsed = JSON.parse(data.references_text);
            // If parsed is an array, use it directly
            if (Array.isArray(parsed)) {
                references = parsed;
            } else {
                // If it's a string, try to parse again or split
                references = [parsed];
            }
        } catch (e) {
            // If not JSON, try to split by newlines
            references = data.references_text.split('\n').filter(ref => ref.trim() !== '');
        }
        
        // Format references HTML
        let referencesHtml = '';
        if (references.length > 0) {
            references.forEach((ref, index) => {
                // Clean up the reference text (remove quotes and extra slashes)
                let cleanRef = String(ref).replace(/^["']|["']$/g, '').replace(/\\"/g, '"');
                referencesHtml += `<div class="reference-item">${index + 1}. ${cleanRef}</div>`;
            });
        } else {
            referencesHtml = '<p class="text-muted">No references provided</p>';
        }
        
        // Get upload directory URL
        const uploadUrl = proposalsAdmin.ajax_url.replace('/wp-admin/admin-ajax.php', '/wp-content/uploads');
        const cvUrl = data.cv_file ? uploadUrl + data.cv_file : null;
        
        return `
            <div class="details-row">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Personal Information -->
                        <div class="details-section">
                            <h4><i class="fas fa-user"></i> Personal Information</h4>
                            <p><span class="detail-label">Phone:</span> <span class="detail-value">${data.phone || 'N/A'}</span></p>
                            <p><span class="detail-label">Address:</span> <span class="detail-value">${data.address || 'N/A'}</span></p>
                        </div>
                        
                        <!-- Course Information -->
                        <div class="details-section">
                            <h4><i class="fas fa-book"></i> Course Information</h4>
                            <p><span class="detail-label">Course Name:</span> <span class="detail-value">${data.course_name}</span></p>
                            <p><span class="detail-label">Course Level:</span> <span class="detail-value badge bg-info">${data.course_level}</span></p>
                            <p><span class="detail-label">CE Hours:</span> <span class="detail-value badge bg-primary">${data.hours}</span></p>
                        </div>
                        
                        <!-- Description -->
                        <div class="details-section">
                            <h4><i class="fas fa-align-left"></i> Course Description</h4>
                            <div class="detail-text-block">${data.description}</div>
                        </div>
                        
                        <!-- Learning Objectives -->
                        <div class="details-section">
                            <h4><i class="fas fa-bullseye"></i> Learning Objectives</h4>
                            <div class="detail-text-block">${data.objectives}</div>
                        </div>
                        
                        <!-- Cultural Diversity -->
                        <div class="details-section">
                            <h4><i class="fas fa-globe"></i> Cultural Diversity</h4>
                            <div class="detail-text-block">${data.diversity}</div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Course Outline -->
                        <div class="details-section">
                            <h4><i class="fas fa-list-ol"></i> Course Outline</h4>
                            <div class="detail-text-block">${data.outline}</div>
                        </div>
                        
                        <!-- References -->
                        <div class="details-section">
                            <h4><i class="fas fa-book-open"></i> References (${references.length})</h4>
                            ${referencesHtml}
                        </div>
                        
                        <!-- CV File -->
                        <div class="details-section">
                            <h4><i class="fas fa-file-pdf"></i> Curriculum Vitae</h4>
                            ${cvUrl ? `
                                <a href="${proposalsAdmin.ajax_url}?action=download_cv_file&proposal_id=${data.id}&nonce=${proposalsAdmin.nonce}" 
                                   class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Download CV
                                </a>
                            ` : '<p class="text-muted">No CV uploaded</p>'}
                        </div>
                        
                        <!-- Conflict of Interest -->
                        <div class="details-section">
                            <h4><i class="fas fa-exclamation-triangle"></i> Conflict of Interest</h4>
                            ${data.has_conflict ? `
                                <div class="alert alert-warning">
                                    <strong><i class="fas fa-info-circle"></i> Has Conflict:</strong>
                                    <div class="mt-2 detail-text-block">${data.conflict_explanation || 'N/A'}</div>
                                </div>
                            ` : '<p class="text-success"><i class="fas fa-check-circle"></i> No conflict of interest declared</p>'}
                        </div>
                        
                        <!-- APA Statement -->
                        <div class="details-section">
                            <h4><i class="fas fa-shield-alt"></i> APA Required Statement</h4>
                            <div class="detail-text-block">${data.apa_statement}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Initialize DataTable
    var table = $('#proposalsTable').DataTable({
        responsive: false,
        order: [[1, 'desc']], // Order by ID descending
        pageLength: 25,
        columnDefs: [
            {
                orderable: false,
                className: 'dt-control',
                targets: 0
            },
            {
                targets: -1, // Actions column
                orderable: false
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search proposals...",
            lengthMenu: "Show _MENU_ proposals per page",
            info: "Showing _START_ to _END_ of _TOTAL_ proposals",
            infoEmpty: "No proposals found",
            infoFiltered: "(filtered from _MAX_ total proposals)",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
    });
    
    // Add event listener for opening and closing details
    $('#proposalsTable tbody').on('click', 'td.dt-control', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var rowId = tr.data('id');
        
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Find the data for this row
            var rowData = null;
            for (var i = 0; i < proposalsData.length; i++) {
                if (proposalsData[i].id == rowId) {
                    rowData = proposalsData[i];
                    break;
                }
            }
            
            if (rowData) {
                // Open this row
                row.child(formatDetails(rowData)).show();
                tr.addClass('shown');
            }
        }
    });
    
    // Export to CSV function
    window.exportToCSV = function() {
        var csv = [];
        var rows = document.querySelectorAll("#proposalsTable tr");
        
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("td, th");
            
            for (var j = 1; j < cols.length - 1; j++) { // Skip first (control) and last (actions) column
                var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""');
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(","));
        }
        
        var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        var downloadLink = document.createElement("a");
        downloadLink.download = "proposals_" + new Date().toISOString().slice(0,10) + ".csv";
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };
});

// Update proposal status function
function updateProposalStatus(proposalId, status) {
    if (!confirm('Are you sure you want to ' + status + ' this proposal?')) {
        return;
    }
    
    jQuery.ajax({
        url: proposalsAdmin.ajax_url,
        type: 'POST',
        data: {
            action: 'update_proposal_status',
            proposal_id: proposalId,
            status: status,
            nonce: proposalsAdmin.nonce
        },
        beforeSend: function() {
            jQuery('body').css('cursor', 'wait');
        },
        success: function(response) {
            if (response.success) {
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.data.message);
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        },
        complete: function() {
            jQuery('body').css('cursor', 'default');
        }
    });
}
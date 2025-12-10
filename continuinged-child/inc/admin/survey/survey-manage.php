<div class="wrap">
    <h1 class="wp-heading-inline">Survey Responses</h1>
    
    <div class="survey-filters" style="margin: 20px 0;">
        <select id="filter-survey-type">
            <option value="">All Survey Types</option>
            <option value="general">General</option>
            <option value="course_feedback">Course Feedback</option>
        </select>
        
        <!-- <button type="button" class="button" id="export-csv">Export CSV</button> -->
        <!-- <button type="button" class="button" id="export-excel">Export Excel</button> -->
    </div>
    
    <table id="survey-table" class="wp-list-table widefat fixed striped" style="width:100%">
        <thead>
            <tr>
               <!-- <th></th> -->
                <th>ID</th>
                <th>Survey Type</th>
                <th>Survey Date</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<style>
.expand-buttons {
 text-align: center;
}
.expand-buttons i {
    font-size:18px;
}

.survey-detail-row {
    background-color: #f9f9f9;
    padding: 20px;
}

.survey-detail-section {
    margin-bottom: 25px;
}

.survey-detail-section h3 {
    color: #23282d;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.survey-questions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
}

.survey-question-item {
    background: white;
    padding: 12px;
    border-left: 3px solid #0073aa;
    border-radius: 3px;
}

.survey-question-label {
    font-weight: 600;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

.survey-question-value {
    color: #333;
}

.badge {
    display: inline-block;
    padding: 3px 8px;
    margin: 2px;
    background: #0073aa;
    color: white;
    border-radius: 3px;
    font-size: 12px;
}

.metadata-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.metadata-item {
    background: #e8f5e9;
    padding: 10px;
    border-radius: 3px;
}

.metadata-label {
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    color: #666;
}

.metadata-value {
    font-size: 14px;
    color: #333;
    margin-top: 5px;
}

</style>

<script>
jQuery(document).ready(function($) {
    
    // Question labels mapping
    const questionLabels = {
        'r10': 'Home Study courses taken in past year',
        'r20': 'Online courses taken',
        'r30': 'Courses plan to take next year',
        'r40': 'Online courses expected',
        'r41': 'Prefer 1 hour',
        'r42': 'Prefer 2 hours',
        'r43': 'Prefer 3 hours',
        'r44': 'Prefer 4 hours',
        'r45': 'Prefer 5+ hours',
        'r50': 'Price opinion',
        'r60': 'How did you find out about our website?',
        'r60v3text': 'Search engine details',
        'r60v4text': 'Newsletter details',
        'r60v5text': 'Magazine details',
        'r60v6text': 'Article details',
        'r60v7text': 'Other source details',
        'r70': 'Psychologist',
        'r71': 'Social Worker',
        'r72': 'Marriage and Family Therapist',
        'r73': 'Other profession',
        'r73text': 'Other profession details',
        'r74': 'Counselor',
        'r81': 'ADHD',
        'r82': 'Addiction',
        'r83': 'Aging and Long Term Care',
        'r84': 'Asperger\'s',
        'r85': 'Behavioral Assessment',
        'r86': 'Biofeedback',
        'r87': 'Brief Psychotherapy',
        'r88': 'Couples Therapy',
        'r89': 'Crisis Intervention',
        'r90': 'Death and Dying',
        'r91': 'Depression',
        'r92': 'Diagnosis/DSM-IV',
        'r93': 'Difficult Clients',
        'r94': 'Divorce',
        'r95': 'Domestic Violence',
        'r96': 'Drug Abuse',
        'r97': 'Eating Disorders',
        'r98': 'Ethics',
        'r99': 'Family Therapy',
        'r100': 'Forensic Psychology',
        'r101': 'Gay/Lesbian Issues',
        'r102': 'Group Therapy',
        'r103': 'Health Psychology',
        'r104': 'Hypnosis',
        'r105': 'Neuropsychology',
        'r106': 'Organic Mental Disorders',
        'r107': 'Pain Management',
        'r108': 'Parenting Skills',
        'r109': 'Personality Disorders',
        'r110': 'Play Therapy',
        'r111': 'Post-Traumatic Stress Disorder',
        'r112': 'Professional Burnout',
        'r113': 'Psychopharmacology',
        'r114': 'Schizophrenia',
        'r115': 'Social Skills Training',
        'r116': 'Supervision',
        'r117': 'Other topics',
        'r130': 'Additional topics interested',
        'r140': 'Authors suggestions',
        'r150': 'Other comments',
        'r160': 'Name',
        'r170': 'Email',
        'r180': 'Notify new courses',
        'r190': 'Phone'
    };
    
    const valueLabels = {
        'r10': ['0', '1', '2', '3-4', '5+'],
        'r20': ['0', '1', '2', '3-4', '5+'],
        'r30': ['0', '1', '2', '3-4', '5+'],
        'r40': ['0', '1', '2', '3-4', '5+'],
        'r50': ['', 'Too high', 'A little high', 'Reasonable', 'A little low', 'A bargain'],
        'r60': ['', 'Friend/colleague', 'Mailed advertisement', 'Internet/search', 'Local newsletter', 'Magazine', 'Article', 'Other']
    };
    
    // Format detail row
    function format(data) {
        let surveyData = {};
        try {
            surveyData = JSON.parse(data.survey_data);
        } catch(e) {
            return '<div class="error">Error parsing survey data</div>';
        }
        
        let html = '<div class="survey-detail-row">';
        
        // Basic Questions Section
        html += '<div class="survey-detail-section">';
        html += '<h3>📝 Survey Questions</h3>';
        html += '<div class="survey-questions-grid">';
        
        if (surveyData.questions) {
            for (let [key, value] of Object.entries(surveyData.questions)) {
                if (value && value !== '') {
                    let label = questionLabels[key] || key;
                    let displayValue = value;
                    
                    // Convert numeric values to labels
                    if (valueLabels[key] && !isNaN(value)) {
                        displayValue = valueLabels[key][parseInt(value)] || value;
                    }
                    
                    // Handle checkbox values
                    if (value === '1' && (key.startsWith('r7') || key.startsWith('r8') || key.startsWith('r9') || key.startsWith('r10') || key.startsWith('r11'))) {
                        displayValue = '✓ Selected';
                    }
                    
                    html += '<div class="survey-question-item">';
                    html += '<span class="survey-question-label">' + label + ':</span> ';
                    html += '<span class="survey-question-value">' + displayValue + '</span>';
                    html += '</div>';
                }
            }
        }
        
        html += '</div></div>';
        
        // Calculated Data Section
        if (surveyData.calculated) {
            html += '<div class="survey-detail-section">';
            html += '<h3>📊 Calculated Information</h3>';
            
            if (surveyData.calculated.profession_types && surveyData.calculated.profession_types.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<strong>Professional Status:</strong><br>';
                surveyData.calculated.profession_types.forEach(function(prof) {
                    html += '<span class="badge">' + prof + '</span>';
                });
                html += '</div>';
            }
            
            if (surveyData.calculated.topics_interested && surveyData.calculated.topics_interested.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<strong>Topics Interested:</strong><br>';
                surveyData.calculated.topics_interested.forEach(function(topic) {
                    html += '<span class="badge">' + topic + '</span>';
                });
                html += '</div>';
            }
            
            if (surveyData.calculated.course_lengths_preferred && surveyData.calculated.course_lengths_preferred.length > 0) {
                html += '<div style="margin-bottom: 15px;">';
                html += '<strong>Preferred Course Lengths:</strong><br>';
                surveyData.calculated.course_lengths_preferred.forEach(function(length) {
                    html += '<span class="badge">' + length + '</span>';
                });
                html += '</div>';
            }
            
            if (surveyData.calculated.total_questions_answered) {
                html += '<div>';
                html += '<strong>Total Questions Answered:</strong> ' + surveyData.calculated.total_questions_answered;
                html += '</div>';
            }
            
            html += '</div>';
        }
        
        // Metadata Section
        if (surveyData.metadata) {
            html += '<div class="survey-detail-section">';
            html += '<h3>ℹ️ Metadata</h3>';
            html += '<div class="metadata-grid">';
            
            if (surveyData.metadata.browser) {
                html += '<div class="metadata-item">';
                html += '<div class="metadata-label">Browser</div>';
                html += '<div class="metadata-value">' + surveyData.metadata.browser + '</div>';
                html += '</div>';
            }
            
            if (surveyData.metadata.submission_timestamp) {
                let date = new Date(surveyData.metadata.submission_timestamp * 1000);
                html += '<div class="metadata-item">';
                html += '<div class="metadata-label">Submission Time</div>';
                html += '<div class="metadata-value">' + date.toLocaleString() + '</div>';
                html += '</div>';
            }
            
            if (data.ip_address) {
                html += '<div class="metadata-item">';
                html += '<div class="metadata-label">IP Address</div>';
                html += '<div class="metadata-value">' + data.ip_address + '</div>';
                html += '</div>';
            }
            
            html += '</div></div>';
        }
        
        html += '</div>';
        
        return html;
    }
    
    // Initialize DataTable
    var table = $('#survey-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: function(d) {
                d.action = 'get_survey_data';
                d.nonce = '<?php echo wp_create_nonce('survey_data_nonce'); ?>';
                d.survey_type = $('#filter-survey-type').val();
            }
        },
        columns: [
            /*{
                className: 'expand-buttons',
                orderable: false,
                data: null,
                defaultContent: '<i class="fa-solid fa-circle-chevron-right"></i>'
            }, */
            { data: 'id',  orderable: false, },
            { data: 'survey_type',  orderable: true },
            { data: 'survey_date',  orderable: false },
            { data: 'user_name', orderable: false },
            { data: 'user_email' ,  orderable: false,},
            { data: 'user_phone' ,  orderable: false,},
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return '<button class="button button-small delete-survey" data-id="' + row.id + '">Delete</button>';
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        language: {
            processing: "Loading...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries to show",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
    
    // Add event listener for opening and closing details
    $('#survey-table tbody').on('click', 'tr', function() {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        
        if (row.child.isShown()) {
            // Close this row
            row.child.hide();
            tr.removeClass('shown');        
             //$(this).html('<i class="fa-solid fa-circle-chevron-right"></i>');    
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
          //  $(this).html('<i class="fa-solid fa-circle-chevron-down"></i>');
        }
    });
    
    // Filter change
    $('#filter-survey-type').on('change', function() {
        table.ajax.reload();
    });
    
    // Delete survey
    $('#survey-table').on('click', '.delete-survey', function() {
        if (!confirm('Are you sure you want to delete this survey?')) {
            return;
        }
        
        var surveyId = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_survey',
                nonce: '<?php echo wp_create_nonce('delete_survey_nonce'); ?>',
                survey_id: surveyId
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload();
                    alert('Survey deleted successfully');
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Export CSV
    $('#export-csv').on('click', function() {
        window.location.href = ajaxurl + '?action=export_surveys&format=csv&nonce=<?php echo wp_create_nonce('export_surveys_nonce'); ?>';
    });
    
    // Export Excel
    $('#export-excel').on('click', function() {
        window.location.href = ajaxurl + '?action=export_surveys&format=excel&nonce=<?php echo wp_create_nonce('export_surveys_nonce'); ?>';
    });
});
</script>
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
        'r190': 'Phone',
        // Thêm vào sau dòng 'r190': 'Phone'
        // Course Feedback Questions
        'r10': 'Professional Status - Psychologist',
        'r11': 'Professional Status - Social Worker',
        'r12': 'Professional Status - Marriage and Family Therapist',
        'r13': 'Professional Status - Other',
        'r13text': 'Professional Status - Other Details',
        'r14': 'Professional Status - Counselor',
        'r20': 'Reason - Subject was of interest',
        'r21': 'Reason - Reputation of author',
        'r22': 'Reason - Important to job activities',
        'r23': 'Reason - Needed CE Hours',
        'r24': 'Reason - Other',
        'r24text': 'Reason - Other Details',
        'r30': 'Overall Quality Rating',
        // Learning Objectives
        'r12753': 'Learning Objective 1 - Addressing unethical acts',
        'r12752': 'Learning Objective 2 - Ethical decisions under crisis',
        'r12751': 'Learning Objective 3 - Step-by-step strategy',
        'r12750': 'Learning Objective 4 - Types of unethical professionals',
        'r12748': 'Learning Objective 5 - Core values',
        'r12749': 'Learning Objective 6 - Cultural differences',
        // Learning Experience
        'r80': 'Course taught at promised level',
        'r200': 'Course material clear and organized',
        'r210': 'Present current developments',
        'r240': 'How much learned',
        'r100': 'Take another course by author',
        'r360': 'Disability accommodations satisfaction',
        'r370': 'Time matches CE hours',
        'r380': 'Content useful for practice',
        // Service
        'r230': 'Website user friendly',
        'r110': 'Take another course from site',
        // Decision Factors
        'r300': 'Factor - Reputation of authors',
        'r310': 'Factor - Quality of courses',
        'r320': 'Factor - Ease of use website',
        'r330': 'Factor - Reminders/ads',
        'r340': 'Factor - CERewards program',
        'r350': 'Factor - Other',
        'r350text': 'Factor - Other Details',
        // Comments
        'r340text': 'CERewards feedback',
        'r120text': 'Additional comments/complaints',
        'r130text': 'Suggestions for future courses'
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
    
    // Check survey type
    if (data.survey_type === 'course_feedback') {
        html += formatCourseFeedback(surveyData, data);
    } else {
        html += formatGeneralSurvey(surveyData, data);
    }
    
    html += '</div>';
    return html;
}

// Format Course Feedback Survey
function formatCourseFeedback(surveyData, data) {
    let html = '';
    
    // Course Info
    if (data.course_id) {
        html += '<div class="survey-detail-section">';
        html += '<h3>📚 Course Information</h3>';
        html += '<div class="metadata-grid">';
        html += '<div class="metadata-item">';
        html += '<div class="metadata-label">Course ID</div>';
        html += '<div class="metadata-value">' + data.course_id + '</div>';
        html += '</div>';
        html += '</div></div>';
    }
    
    // Professional Status
    if (surveyData.professional_status) {
        html += '<div class="survey-detail-section">';
        html += '<h3>👤 Professional Status</h3>';
        html += '<div style="margin-bottom: 10px;">';
        
        if (surveyData.professional_status.psychologist) {
            html += '<span class="badge">Psychologist</span>';
        }
        if (surveyData.professional_status.social_worker) {
            html += '<span class="badge">Social Worker</span>';
        }
        if (surveyData.professional_status.mft) {
            html += '<span class="badge">Marriage & Family Therapist</span>';
        }
        if (surveyData.professional_status.counselor) {
            html += '<span class="badge">Counselor</span>';
        }
        if (surveyData.professional_status.other && surveyData.professional_status.other_text) {
            html += '<span class="badge">Other: ' + surveyData.professional_status.other_text + '</span>';
        }
        
        html += '</div></div>';
    }
    
    // Reasons for Taking Course
    if (surveyData.reasons) {
        html += '<div class="survey-detail-section">';
        html += '<h3>🎯 Reasons for Taking Course</h3>';
        html += '<div style="margin-bottom: 10px;">';
        
        if (surveyData.reasons.interest) {
            html += '<span class="badge">Subject of Interest</span>';
        }
        if (surveyData.reasons.author_reputation) {
            html += '<span class="badge">Author Reputation</span>';
        }
        if (surveyData.reasons.job_activities) {
            html += '<span class="badge">Important to Job</span>';
        }
        if (surveyData.reasons.ce_hours) {
            html += '<span class="badge">Needed CE Hours</span>';
        }
        if (surveyData.reasons.other && surveyData.reasons.other_text) {
            html += '<span class="badge">Other: ' + surveyData.reasons.other_text + '</span>';
        }
        
        html += '</div></div>';
    }
    
    // Overall Quality
    if (surveyData.overall_quality) {
        html += '<div class="survey-detail-section">';
        html += '<h3>⭐ Overall Quality Rating</h3>';
        html += '<div style="font-size: 24px; margin-bottom: 10px;">';
        
        const qualityLabels = {1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Excellent'};
        const qualityColors = {1: '#d32f2f', 2: '#f57c00', 3: '#388e3c', 4: '#1976d2'};
        
        html += '<span style="color: ' + qualityColors[surveyData.overall_quality] + '; font-weight: bold;">';
        html += qualityLabels[surveyData.overall_quality] + ' (' + surveyData.overall_quality + '/4)';
        html += '</span>';
        
        html += '</div></div>';
    }
    
    // Learning Objectives
    if (surveyData.learning_objectives) {
        html += '<div class="survey-detail-section">';
        html += '<h3>📖 Learning Objectives</h3>';
        html += '<div class="survey-questions-grid">';
        
        const objectives = {
            'objective_1': 'Addressing unethical acts',
            'objective_2': 'Ethical decisions under crisis',
            'objective_3': 'Step-by-step strategy',
            'objective_4': 'Types of unethical professionals',
            'objective_5': 'Core values',
            'objective_6': 'Cultural differences'
        };
        
        for (let [key, label] of Object.entries(objectives)) {
            if (surveyData.learning_objectives[key]) {
                html += '<div class="survey-question-item">';
                html += '<span class="survey-question-label">' + label + ':</span> ';
                html += '<span class="survey-question-value">';
                html += getRatingStars(surveyData.learning_objectives[key], 5);
                html += ' (' + surveyData.learning_objectives[key] + '/5)';
                html += '</span>';
                html += '</div>';
            }
        }
        
        // Calculate average
        let sum = 0, count = 0;
        for (let val of Object.values(surveyData.learning_objectives)) {
            if (val > 0) { sum += val; count++; }
        }
        if (count > 0) {
            html += '<div class="survey-question-item" style="background: #e3f2fd; border-left-color: #1976d2;">';
            html += '<span class="survey-question-label">Average Rating:</span> ';
            html += '<span class="survey-question-value" style="font-weight: bold;">';
            html += (sum/count).toFixed(2) + '/5';
            html += '</span>';
            html += '</div>';
        }
        
        html += '</div></div>';
    }
    
    // Learning Experience
    if (surveyData.learning_experience) {
        html += '<div class="survey-detail-section">';
        html += '<h3>🎓 Learning Experience</h3>';
        html += '<div class="survey-questions-grid">';
        
        const experience = {
            'taught_at_level': 'Taught at promised level',
            'clear_organized': 'Clear and organized',
            'current_developments': 'Current developments',
            'how_much_learned': 'How much learned',
            'take_another_course': 'Take another course by author',
            'disability_accommodations': 'Disability accommodations',
            'time_match_hours': 'Time matches CE hours',
            'content_useful': 'Content useful for practice'
        };
        
        for (let [key, label] of Object.entries(experience)) {
            if (surveyData.learning_experience[key]) {
                html += '<div class="survey-question-item">';
                html += '<span class="survey-question-label">' + label + ':</span> ';
                html += '<span class="survey-question-value">';
                html += getRatingStars(surveyData.learning_experience[key], 5);
                html += ' (' + surveyData.learning_experience[key] + '/5)';
                html += '</span>';
                html += '</div>';
            }
        }
        
        // Calculate average
        let sum = 0, count = 0;
        for (let val of Object.values(surveyData.learning_experience)) {
            if (val > 0) { sum += val; count++; }
        }
        if (count > 0) {
            html += '<div class="survey-question-item" style="background: #e8f5e9; border-left-color: #388e3c;">';
            html += '<span class="survey-question-label">Average Rating:</span> ';
            html += '<span class="survey-question-value" style="font-weight: bold;">';
            html += (sum/count).toFixed(2) + '/5';
            html += '</span>';
            html += '</div>';
        }
        
        html += '</div></div>';
    }
    
    // Service Ratings
    if (surveyData.service) {
        html += '<div class="survey-detail-section">';
        html += '<h3>💼 Service</h3>';
        html += '<div class="survey-questions-grid">';
        
        if (surveyData.service.user_friendly) {
            html += '<div class="survey-question-item">';
            html += '<span class="survey-question-label">Website user friendly:</span> ';
            html += '<span class="survey-question-value">';
            html += getRatingStars(surveyData.service.user_friendly, 5);
            html += ' (' + surveyData.service.user_friendly + '/5)';
            html += '</span>';
            html += '</div>';
        }
        
        if (surveyData.service.take_another) {
            html += '<div class="survey-question-item">';
            html += '<span class="survey-question-label">Take another course:</span> ';
            html += '<span class="survey-question-value">';
            html += getRatingStars(surveyData.service.take_another, 5);
            html += ' (' + surveyData.service.take_another + '/5)';
            html += '</span>';
            html += '</div>';
        }
        
        html += '</div></div>';
    }
    
    // Decision Factors
    if (surveyData.decision_factors) {
        html += '<div class="survey-detail-section">';
        html += '<h3>🎯 Decision Factors</h3>';
        html += '<div class="survey-questions-grid">';
        
        const factors = {
            'author_reputation': 'Author reputation',
            'course_quality': 'Course quality',
            'ease_of_use': 'Ease of use',
            'reminders': 'Reminders/ads',
            'rewards_program': 'CERewards program'
        };
        
        for (let [key, label] of Object.entries(factors)) {
            if (surveyData.decision_factors[key]) {
                html += '<div class="survey-question-item">';
                html += '<span class="survey-question-label">' + label + ':</span> ';
                html += '<span class="survey-question-value">';
                html += getRatingStars(surveyData.decision_factors[key], 5);
                html += ' (' + surveyData.decision_factors[key] + '/5)';
                html += '</span>';
                html += '</div>';
            }
        }
        
        if (surveyData.decision_factors.other && surveyData.decision_factors.other_text) {
            html += '<div class="survey-question-item">';
            html += '<span class="survey-question-label">Other factor:</span> ';
            html += '<span class="survey-question-value">';
            html += surveyData.decision_factors.other_text + ' ';
            html += getRatingStars(surveyData.decision_factors.other, 5);
            html += ' (' + surveyData.decision_factors.other + '/5)';
            html += '</span>';
            html += '</div>';
        }
        
        html += '</div></div>';
    }
    
    // Comments
    if (surveyData.comments) {
        html += '<div class="survey-detail-section">';
        html += '<h3>💬 Comments</h3>';
        
        if (surveyData.comments.rewards_feedback) {
            html += '<div class="survey-question-item" style="margin-bottom: 15px;">';
            html += '<span class="survey-question-label">CERewards Feedback:</span><br>';
            html += '<span class="survey-question-value">' + surveyData.comments.rewards_feedback + '</span>';
            html += '</div>';
        }
        
        if (surveyData.comments.additional_comments) {
            html += '<div class="survey-question-item" style="margin-bottom: 15px;">';
            html += '<span class="survey-question-label">Additional Comments:</span><br>';
            html += '<span class="survey-question-value">' + surveyData.comments.additional_comments + '</span>';
            html += '</div>';
        }
        
        if (surveyData.comments.course_suggestions) {
            html += '<div class="survey-question-item" style="margin-bottom: 15px;">';
            html += '<span class="survey-question-label">Course Suggestions:</span><br>';
            html += '<span class="survey-question-value">' + surveyData.comments.course_suggestions + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
    }
    
    // Metadata
    html += formatMetadata(surveyData, data);
    
    return html;
}

// Format General Survey (existing code)
function formatGeneralSurvey(surveyData, data) {
    let html = '';
    
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
    
    // Metadata
    html += formatMetadata(surveyData, data);
    
    return html;
}

// Format Metadata (shared function)
function formatMetadata(surveyData, data) {
    let html = '';
    
    if (surveyData.metadata || data.ip_address) {
        html += '<div class="survey-detail-section">';
        html += '<h3>ℹ️ Metadata</h3>';
        html += '<div class="metadata-grid">';
        
        if (surveyData.metadata && surveyData.metadata.browser) {
            html += '<div class="metadata-item">';
            html += '<div class="metadata-label">Browser</div>';
            html += '<div class="metadata-value">' + surveyData.metadata.browser + '</div>';
            html += '</div>';
        }
        
        if (surveyData.metadata && surveyData.metadata.submission_timestamp) {
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
        
        if (data.user_agent) {
            html += '<div class="metadata-item">';
            html += '<div class="metadata-label">User Agent</div>';
            html += '<div class="metadata-value" style="font-size: 11px;">' + data.user_agent + '</div>';
            html += '</div>';
        }
        
        html += '</div></div>';
    }
    
    return html;
}

// Helper function to generate rating stars
function getRatingStars(rating, maxRating) {
    let stars = '';
    for (let i = 1; i <= maxRating; i++) {
        if (i <= rating) {
            stars += '⭐';
        } else {
            stars += '☆';
        }
    }
    return stars;
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
jQuery(document).ready(function($) {
    // Form validation function
    function validateForm() {
        let isValid = true;
        let errorMessage = '';

        // Check if at least one professional status is selected
        const professionalStatus = $('input[name^="r1"]:checked').length;
        if (professionalStatus === 0) {
            errorMessage += '- Please select your professional status\n';
            isValid = false;
        }

        // Check if at least one reason for taking course is selected
        const reasons = $('input[name^="r2"]:checked').length;
        if (reasons === 0) {
            errorMessage += '- Please select at least one reason for taking this course\n';
            isValid = false;
        }

        // Check if overall quality is selected
        if (!$('input[name="r30"]:checked').val()) {
            errorMessage += '- Please rate the overall quality of the course\n';
            isValid = false;
        }

        // Check all learning objectives are answered
        const learningObjectives = ['r12753', 'r12752', 'r12751', 'r12750', 'r12748', 'r12749'];
        learningObjectives.forEach(function(objective) {
            if (!$('input[name="' + objective + '"]:checked').val()) {
                errorMessage += '- Please rate all learning objectives\n';
                isValid = false;
                return false;
            }
        });

        // Check all learning experience questions are answered
        const experienceQuestions = ['r80', 'r200', 'r210', 'r240', 'r100', 'r360', 'r370', 'r380'];
        experienceQuestions.forEach(function(question) {
            if (!$('input[name="' + question + '"]:checked').val()) {
                errorMessage += '- Please answer all learning experience questions\n';
                isValid = false;
                return false;
            }
        });

        // Check service questions
        const serviceQuestions = ['r230', 'r110'];
        serviceQuestions.forEach(function(question) {
            if (!$('input[name="' + question + '"]:checked').val()) {
                errorMessage += '- Please answer all service questions\n';
                isValid = false;
                return false;
            }
        });

        // Check decision factors
        const decisionFactors = ['r300', 'r310', 'r320', 'r330', 'r340'];
        decisionFactors.forEach(function(factor) {
            if (!$('input[name="' + factor + '"]:checked').val()) {
                errorMessage += '- Please rate all decision factors\n';
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            alert('Please complete all required fields:\n\n' + errorMessage);
        }

        return isValid;
    }

    // Form submission
    $('#course-evaluation-form').on('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!validateForm()) {
            return false;
        }

        const submitBtn = $('#submitBtn');
        const originalBtnText = submitBtn.html();
        
        // Disable submit button and show loading state
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...');

        // Collect form data
        const formData = new FormData(this);
        
        // Add nonce for security
        formData.append('nonce', ceAjax.nonce);

        // AJAX request
        $.ajax({
            url: ceAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert('Thank you for your feedback! Your evaluation has been submitted successfully.');
                    
                    // Redirect to certificate page or reload
                    if (response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        // Reset form
                        $('#course-evaluation-form')[0].reset();
                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                } else {
                    alert('Error: ' + (response.data.message || 'Something went wrong. Please try again.'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while submitting your evaluation. Please try again.');
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalBtnText);
            }
        });
    });

    // Handle "Other" checkbox - show/hide text input
    $('#r13').on('change', function() {
        if ($(this).is(':checked')) {
            $('input[name="r13text"]').slideDown();
        } else {
            $('input[name="r13text"]').slideUp().val('');
        }
    });

    $('#r24').on('change', function() {
        if ($(this).is(':checked')) {
            $('input[name="r24text"]').slideDown();
        } else {
            $('input[name="r24text"]').slideUp().val('');
        }
    });
});
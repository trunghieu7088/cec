jQuery(document).ready(function($) {
    
    // Character counter for course description
    $('#courseDescription').on('input', function() {
        const maxLength = 1200;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        $('#descCharCount').text(remaining);
    });

    // Toggle conflict of interest explanation
    $('#conflictOfInterest').change(function() {
        if ($(this).is(':checked')) {
            $('#conflictExplanation').slideDown();
            $('#conflictDetails').prop('required', true);
        } else {
            $('#conflictExplanation').slideUp();
            $('#conflictDetails').prop('required', false);
        }
    });

    // Add more references dynamically
    let referenceCount = 5;
    $('#addReferenceBtn').click(function() {
        referenceCount++;
        const newReference = `
            <div class="mb-3 reference-item">
                <label for="reference${referenceCount}" class="form-label">Reference ${referenceCount}</label>
                <div class="input-group">
                    <textarea class="form-control" id="reference${referenceCount}" name="references[]" rows="3" placeholder="Author, A. A., & Author, B. B. (Year). Title of article. Journal Name, Volume(Issue), pages. https://doi.org/xxx"></textarea>
                    <button type="button" class="btn btn-outline-danger remove-reference" title="Remove">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        `;
        $('#referencesContainer').append(newReference);
    });

    // Remove reference
    $(document).on('click', '.remove-reference', function() {
        $(this).closest('.reference-item').remove();
    });

    // Custom validation method for APA format
    $.validator.addMethod("apaFormat", function(value, element) {
        // Basic check for APA format (not perfect but helps)
        return this.optional(element) || /\(\d{4}\)/.test(value);
    }, "Please use APA 7th Edition format (include publication year in parentheses)");

    // Custom validation for file size
    $.validator.addMethod("filesize", function(value, element, param) {
        if (element.files.length === 0) return true;
        return element.files[0].size <= param;
    }, "File size must not exceed 5MB");

    // Initialize form validation
    $('#authorProposalForm').validate({
        rules: {
            fullName: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                minlength: 10
            },
            courseName: {
                required: true,
                minlength: 5
            },
            courseLevel: {
                required: true
            },
            creditHours: {
                required: true,
                min: 1,
                max: 10
            },
            courseDescription: {
                required: true,
                minlength: 50,
                maxlength: 1200
            },
            learningObjectives: {
                required: true,
                minlength: 100
            },
            courseOutline: {
                required: true,
                minlength: 100
            },
            culturalDiversity: {
                required: true,
                minlength: 100
            },
            reference1: {
                required: true,
                apaFormat: true
            },
            reference2: {
                required: true,
                apaFormat: true
            },
            reference3: {
                required: true,
                apaFormat: true
            },
            reference4: {
                required: true,
                apaFormat: true
            },
            reference5: {
                required: true,
                apaFormat: true
            },
            cvFile: {
                required: true,
                extension: "pdf|doc|docx",
                filesize: 5242880 // 5MB in bytes
            },
            conflictDetails: {
                required: function() {
                    return $('#conflictOfInterest').is(':checked');
                }
            },
            apaStatement: {
                required: true,
                minlength: 100
            },
            agreementCheck: {
                required: true
            }
        },
        messages: {
            fullName: {
                required: "Please enter your full name",
                minlength: "Name must be at least 3 characters"
            },
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            phone: {
                required: "Please enter your phone number",
                minlength: "Please enter a valid phone number"
            },
            courseName: {
                required: "Please enter the course name",
                minlength: "Course name must be at least 5 characters"
            },
            courseLevel: "Please select a course level",
            creditHours: {
                required: "Please specify credit hours",
                min: "Credit hours must be at least 1",
                max: "Credit hours cannot exceed 10"
            },
            courseDescription: {
                required: "Please provide a course description",
                minlength: "Description must be at least 50 characters",
                maxlength: "Description cannot exceed 1200 characters"
            },
            learningObjectives: {
                required: "Please provide learning objectives",
                minlength: "Learning objectives must be at least 100 characters"
            },
            courseOutline: {
                required: "Please provide a course outline",
                minlength: "Course outline must be at least 100 characters"
            },
            culturalDiversity: {
                required: "Please describe how your course addresses cultural diversity",
                minlength: "Cultural diversity section must be at least 100 characters"
            },
            reference1: "Please provide the first reference in APA format",
            reference2: "Please provide the second reference in APA format",
            reference3: "Please provide the third reference in APA format",
            reference4: "Please provide the fourth reference in APA format",
            reference5: "Please provide the fifth reference in APA format",
            cvFile: {
                required: "Please upload your CV",
                extension: "Only PDF or Word documents are allowed",
                filesize: "File size must not exceed 5MB"
            },
            conflictDetails: "Please explain your conflict of interest",
            apaStatement: {
                required: "Please provide an APA statement",
                minlength: "APA statement must be at least 100 characters"
            },
            agreementCheck: "You must agree to submit the proposal"
        },
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") == "checkbox") {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            // Create FormData object to handle file uploads
            var formData = new FormData();

            // Collect all text inputs
            formData.append('action', 'submit_author_proposal');
            formData.append('security', ajax_object.nonce);
            formData.append('fullName', $('#fullName').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
            formData.append('address', $('#address').val());
            formData.append('courseName', $('#courseName').val());
            formData.append('courseLevel', $('#courseLevel').val());
            formData.append('creditHours', $('#creditHours').val());
            formData.append('courseDescription', $('#courseDescription').val());
            formData.append('learningObjectives', $('#learningObjectives').val());
            formData.append('courseOutline', $('#courseOutline').val());
            formData.append('culturalDiversity', $('#culturalDiversity').val());
            
            // Collect all references
            var references = [];
            $('textarea[id^="reference"]').each(function() {
                if ($(this).val().trim() !== '') {
                    references.push($(this).val().trim());
                }
            });
            formData.append('references', JSON.stringify(references));

            // CV file
            var cvFile = $('#cvFile')[0].files[0];
            if (cvFile) {
                formData.append('cvFile', cvFile);
            }

            // Conflict of interest
            formData.append('hasConflict', $('#conflictOfInterest').is(':checked') ? 1 : 0);
            formData.append('conflictDetails', $('#conflictDetails').val());

            // APA statement
            formData.append('apaStatement', $('#apaStatement').val());

            // Show loading state
            var submitBtn = $(form).find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Submitting...');

            // AJAX request
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#formMessage').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<i class="bi bi-check-circle-fill me-2"></i>' +
                            '<strong>Success!</strong> ' + response.data.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>'
                        );
                        
                        // Reset form
                        form.reset();
                        $(form).find('.is-valid').removeClass('is-valid');
                        $('#conflictExplanation').hide();
                        $('#descCharCount').text('1200');
                        
                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: $('#formMessage').offset().top - 100
                        }, 500);
                    } else {
                        $('#formMessage').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                            '<strong>Error!</strong> ' + response.data.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    $('#formMessage').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="bi bi-exclamation-triangle-fill me-2"></i>' +
                        '<strong>Error!</strong> An unexpected error occurred. Please try again.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    );
                    console.error('AJAX Error:', error);
                },
                complete: function() {
                    // Restore button
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: $('#formMessage').offset().top - 100
                    }, 500);
                }
            });
        }
    });
});
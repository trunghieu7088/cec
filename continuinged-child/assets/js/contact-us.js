jQuery(document).ready(function($) {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        var submitButton = $(this).find('button[type="submit"]');
        var formMessage = $('#formMessage');
        
        // Disable submit button
        submitButton.prop('disabled', true);
        submitButton.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...');
        
        // Clear previous messages
        formMessage.html('');
        
        // Prepare form data
        var formData = {
            action: 'submit_contact_form',
            nonce: contactAjax.nonce,
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            subject: $('#subject').val(),
            message: $('#message').val()
        };
        
        // Send AJAX request
        $.ajax({
            url: contactAjax.ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    formMessage.html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="bi bi-check-circle-fill me-2"></i>' + response.data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>'
                    );
                    
                    // Clear form
                    $('#contactForm')[0].reset();
                    
                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: formMessage.offset().top - 100
                    }, 500);
                } else {
                    // Show error message
                    formMessage.html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + response.data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                // Show error message
                formMessage.html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>An error occurred. Please try again later.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                    '</div>'
                );
            },
            complete: function() {
                // Re-enable submit button
                submitButton.prop('disabled', false);
                submitButton.html('<i class="bi bi-send-fill me-2"></i>Send Message');
            }
        });
    });
    
    // Client-side validation feedback
    $('#contactForm input[required], #contactForm textarea[required]').on('blur', function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        }
    });
    
    // Email validation
    $('#email').on('blur', function() {
        var email = $(this).val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
        } else if (email) {
            $(this).removeClass('is-invalid');
            $(this).addClass('is-valid');
        }
    });
});
/**
 * Purchase Certificate Page JavaScript
 * Handles AJAX login, signup, and payment processing with jQuery Validation
 */

jQuery(document).ready(function($) {
    'use strict';
    var ajaxurl=my_ajax_object.ajax_url;
    // Configuration
    const config = {
        basePrice: parseFloat($('.total-amount').text().replace('$', '')) || 74.00,
        mailFee: 9.00
    };
    
    /**
     * Price Calculation
     */
    function updatePrice() {
        let total = config.basePrice;
        
        if ($('#mail-certificate').is(':checked')) {
            total += config.mailFee;
            $('#mail-fee').text('$' + config.mailFee.toFixed(2));
        } else {
            $('#mail-fee').text('$0.00');
        }
        
        $('.total-amount, .final-amount').text('$' + total.toFixed(2));
    }
    
    // Event listeners for price updates
    $('#mail-certificate').on('change', updatePrice);
    $('#update-price-btn').on('click', function(e) {
        e.preventDefault();
        updatePrice();
        
        // Show feedback
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.html('<i class="bi bi-check-circle-fill me-1"></i>Updated!');
        setTimeout(function() {
            $btn.html(originalHtml);
        }, 1500);
    });
    
    /**
     * Card Number Formatting
     */
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s/g, '').replace(/\D/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        $(this).val(formattedValue);
    });
    
    /**
     * Custom Validation Methods
     */
    
    // Phone number validation
    $.validator.addMethod("phoneUS", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^[\d\s\-\(\)]+$/);
    }, "Please enter a valid phone number.");
    
    // Card number validation
    $.validator.addMethod("creditcard", function(value, element) {
        // Remove spaces and dashes
        value = value.replace(/[\s\-]/g, '');
        
        // Check if it's numeric and between 13-19 digits
        if (!/^\d{13,19}$/.test(value)) {
            return false;
        }
        
        // Luhn algorithm check
        let sum = 0;
        let shouldDouble = false;
        
        for (let i = value.length - 1; i >= 0; i--) {
            let digit = parseInt(value.charAt(i));
            
            if (shouldDouble) {
                digit *= 2;
                if (digit > 9) digit -= 9;
            }
            
            sum += digit;
            shouldDouble = !shouldDouble;
        }
        
        return (sum % 10) === 0;
    }, "Please enter a valid card number.");
    
    // Card expiration validation
    $.validator.addMethod("cardExpiry", function(value, element) {
        if (!value) return false;
        
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1;
        const expYear = parseInt($('#card_year').val());
        const expMonth = parseInt(value);
        
        if (!expYear) return true; // Let required validation handle empty year
        
        return expYear > currentYear || (expYear === currentYear && expMonth >= currentMonth);
    }, "Card has expired.");
    
    /**
     * Helper Functions
     */
    function showFormError($form, message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $form.find('.alert').remove();
        $form.prepend(alertHtml);
        
        // Scroll to error
        $('html, body').animate({
            scrollTop: $form.offset().top - 100
        }, 300);
    }
    
    /**
     * jQuery Validation Setup for Login Form
     */
    if ($('#purchaseLoginForm').length) {
        $('#purchaseLoginForm').validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                username: {
                    required: "Please enter your username.",
                    minlength: "Username must be at least 3 characters long."
                },
                password: {
                    required: "Please enter your password.",
                    minlength: "Password must be at least 6 characters long."
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                const $form = $(form);
                const $btn = $form.find('button[type="submit"]');
                const originalHtml = $btn.html();
                
                $form.find('.alert').remove();
                $btn.addClass('loading').prop('disabled', true);
                
                $.ajax({
                    url: ajaxurl || wp.ajax_settings?.url,
                    type: 'POST',
                    data: {
                        action: 'purchase_login',
                        username: $('#login_username').val().trim(),
                        password: $('#login_password').val(),
                        nonce: $form.data('nonce')
                    },
                    success: function(response) {
                        if (response.success) {
                            $form.html(`
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Login successful! Reloading page...
                                </div>
                            `);
                            
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                            showFormError($form, response.data?.message || 'Login failed. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                        showFormError($form, 'Connection error. Please check your internet and try again.');
                        console.error('Login error:', xhr);
                    }
                });
            }
        });
    }
    
    /**
     * jQuery Validation Setup for Signup Form
     */
    if ($('#purchaseSignupForm').length) {
        $('#purchaseSignupForm').validate({
            rules: {
                fullname: {
                    required: true,
                    minlength: 2
                },
                license: {
                    required: true,
                    minlength: 2
                },
                license_state:
                {
                    required:true,
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,                    
                },
                username: {
                    required: true,
                    minlength: 3
                },
                password: {
                    required: true,
                    minlength: 6
                },
                address:
                {
                    required: true,
                    minlength: 3,
                },
                zip: {
                    required: true,
                    minlength: 5,
                    maxlength: 10
                },
                 city: {
                     required: true,
                    minlength: 3,                    
                },
                 state: {
                    required: true,                    
                }
            },
            messages: {
                fullname: {
                    required: "Please enter your full name.",
                    minlength: "Name must be at least 2 characters long."
                },
                license: {
                    minlength: "License number must be at least 2 characters."
                },
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address."
                },
                username: {
                    required: "Please choose a username.",
                    minlength: "Username must be at least 3 characters long."
                },
                password: {
                    required: "Please create a password.",
                    minlength: "Password must be at least 6 characters long."
                },
                zip: {
                    minlength: "ZIP code must be at least 5 characters.",
                    maxlength: "ZIP code must be no more than 10 characters."
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                const $form = $(form);
                const $btn = $form.find('button[type="submit"]');
                const originalHtml = $btn.html();
                
                $form.find('.alert').remove();
                $btn.addClass('loading').prop('disabled', true);
                
                const formData = {
                    action: 'purchase_signup',
                    fullname: $('#signup_fullname').val().trim(),
                    license: $('#signup_license').val().trim(),
                    license_state: $('#signup_license_state').val(),
                    email: $('#signup_email').val().trim(),
                    phone: $('#signup_phone').val().trim(),
                    address: $('#signup_address').val().trim(),
                    city: $('#signup_city').val().trim(),
                    state: $('#signup_state').val(),
                    zip: $('#signup_zip').val().trim(),
                    username: $('#signup_username').val().trim(),
                    password: $('#signup_password').val(),
                    newsletter: $('#signup_newsletter').is(':checked') ? 1 : 0,
                     nonce: $form.data('nonce') || my_ajax_object.signup_nonce
                };
                
                $.ajax({
                    url: ajaxurl || wp.ajax_settings?.url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $form.html(`
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    Account created successfully! Welcome, ${response.data.user.name}!<br>
                                    Reloading page...
                                </div>
                            `);
                            
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                            showFormError($form, response.data?.message || 'Registration failed. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                        showFormError($form, 'Connection error. Please check your internet and try again.');
                        console.error('Signup error:', xhr);
                    }
                });
            }
        });
    }
    
    /**
     * jQuery Validation Setup for Payment Form
     */
    if ($('#paymentForm').length) {
        $('#paymentForm').validate({
            rules: {
                card_number: {
                    required: true,
                   // creditcard: true
                },
                card_month: {
                    required: true,
                    cardExpiry: true
                },
                card_year: {
                    required: true
                }
            },
            messages: {
                card_number: {
                    required: "Please enter your card number."
                },
                card_month: {
                    required: "Please select expiration month."
                },
                card_year: {
                    required: "Please select expiration year."
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
    const $form = $(form);
    const $btn = $('#purchase-btn');
    const originalHtml = $btn.html();
    
    $form.find('.alert').remove();
    $btn.addClass('loading').prop('disabled', true);
    
    // Get completion_code from form data attribute or URL
    const completionCode = $form.data('completion-code') || 
                          new URLSearchParams(window.location.search).get('completion_code');
    
    if (!completionCode) {
        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
        showFormError($form, 'Missing completion code. Please try again.');
        return;
    }
    
    const courseData = {
        action: 'process_certificate_payment',
        course_id: $form.data('course-id') || '',
        completion_code: completionCode,
        card_number: $('#card_number').val().replace(/\s/g, ''),
        card_month: $('#card_month').val(),
        card_year: $('#card_year').val(),
        mail_certificate: $('#mail-certificate').is(':checked') ? 1 : 0,
        discount_code: $('#discount-code').val().trim(),
        total_amount: parseFloat($('.total-amount').text().replace('$', '')),
        nonce: $form.data('nonce')
    };
    
    console.log('Submitting payment with data:', {
        action: courseData.action,
        completion_code: courseData.completion_code,
        course_id: courseData.course_id,
        total_amount: courseData.total_amount
    });
    
    $.ajax({
        url: ajaxurl || my_ajax_object.ajax_url,
        type: 'POST',
        data: courseData,
        success: function(response) {
            console.log('Payment response:', response);
            
            if (response.success) {
                let successMessage = '<div class="alert alert-success text-center">' +
                    '<i class="bi bi-check-circle-fill me-2" style="font-size: 2rem;"></i>' +
                    '<h4>Payment Successful!</h4>' +
                    '<p>' + (response.data.message || 'Your certificate has been awarded!') + '</p>';
                
                if (response.data.certificate_url) {
                    successMessage += '<p class="mt-3">' +
                        '<a href="' + response.data.certificate_url + '" class="btn btn-primary btn-lg">' +
                        '<i class="bi bi-download me-2"></i>View Your Certificate' +
                        '</a>' +
                        '</p>';
                }
                
                successMessage += '</div>';
                
                $form.html(successMessage);
                
                if (response.data.redirect_url) {
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 3000);
                }
            } else {
                $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                showFormError($form, response.data?.message || 'Payment processing failed. Please try again.');
            }
        },
        error: function(xhr) {
            console.error('Payment error:', xhr);
            $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
            showFormError($form, 'Payment processing error. Please try again or contact support.');
        }
    });
}
        });
        
        // Revalidate month when year changes
        $('#card_year').on('change', function() {
            $('#card_month').valid();
        });
    }
    
    /**
     * Real-time validation on blur
     */
    $('#login_username, #login_password').on('blur', function() {
        $(this).valid();
    });
    
    $('#purchaseSignupForm input, #purchaseSignupForm select').on('blur', function() {
        if ($(this).val()) {
            $(this).valid();
        }
    });
    
    /**
     * Clear validation on input
     */
    $('input, select').on('input change', function() {
        const $field = $(this);
        if ($field.hasClass('is-invalid') && $field.val().trim()) {
            $field.removeClass('is-invalid');
            $field.siblings('.invalid-feedback').remove();
        }
    });
    
    /**
     * Discount Code Handler
     */
    $('#discount-code').on('change', function() {
        const code = $(this).val().trim();
        if (code) {
            // TODO: Add AJAX call to validate discount code
            console.log('Discount code entered:', code);
            
            // Example implementation:
            /*
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'validate_discount_code',
                    code: code,
                    nonce: $('#paymentForm').data('nonce')
                },
                success: function(response) {
                    if (response.success) {
                        // Update price with discount
                        const discountAmount = response.data.discount;
                        // Update UI to show discount
                    } else {
                        alert('Invalid discount code');
                    }
                }
            });
            */
        }
    });
    
    /**
     * Initialize
     */
    updatePrice();        
    
    console.log('Purchase Certificate page initialized with jQuery Validation');

    /**
 * jQuery Validation Setup for Update User Form
 */
if ($('#updateUserForm').length) {
    $('#updateUserForm').validate({
        rules: {
            fullname: {
                required: true,
                minlength: 2
            },
            license: {
                required: true,
                minlength: 2
            },
            license_state: {
                required: true,
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
            },
            address: {
                required: true,
                minlength: 3,
            },
            zip: {
                required: true,
                minlength: 5,
                maxlength: 10
            },
            city: {
                required: true,
                minlength: 3,
            },
            state: {
                required: true,
            }
        },
        messages: {
            fullname: {
                required: "Please enter your full name.",
                minlength: "Name must be at least 2 characters long."
            },
            license: {
                required: "Please enter your license number.",
                minlength: "License number must be at least 2 characters."
            },
            license_state: {
                required: "Please select license state."
            },
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            phone: {
                required: "Please enter your phone number."
            },
            address: {
                required: "Please enter your address.",
                minlength: "Address must be at least 3 characters."
            },
            zip: {
                required: "Please enter your ZIP code.",
                minlength: "ZIP code must be at least 5 characters.",
                maxlength: "ZIP code must be no more than 10 characters."
            },
            city: {
                required: "Please enter your city.",
                minlength: "City must be at least 3 characters."
            },
            state: {
                required: "Please select your state."
            }
        },
        errorElement: 'div',
        errorClass: 'invalid-feedback d-block',
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        submitHandler: function(form) {
            const $form = $(form);
            const $btn = $form.find('button[type="submit"]');
            const originalHtml = $btn.html();
            
            // Remove any existing alerts
            $form.find('.alert').remove();
            
            // Show loading state
            $btn.addClass('loading').prop('disabled', true);
            
            const formData = {
                action: 'update_user_profile',
                fullname: $('#signup_fullname').val().trim(),
                license: $('#signup_license').val().trim(),
                license_state: $('#signup_license_state').val(),
                email: $('#signup_email').val().trim(),
                phone: $('#signup_phone').val().trim(),
                address: $('#signup_address').val().trim(),
                city: $('#signup_city').val().trim(),
                state: $('#signup_state').val(),
                zip: $('#signup_zip').val().trim(),
                nonce: my_ajax_object.update_user_nonce || wp_create_nonce('update_user_nonce')
            };
            
            $.ajax({
                url: ajaxurl_global,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                    
                    if (response.success) {
                        // Show success message
                        const successAlert = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>${response.data.message || 'Profile updated successfully!'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $form.prepend(successAlert);
                        setTimeout(function() {
                          window.location.reload();
                        }, 500);
                        // Mark all fields as valid
                        $form.find('.form-control, .form-select').addClass('is-valid').removeClass('is-invalid');
                        
                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: $form.offset().top - 100
                        }, 300);
                        
                        // Auto-hide success message after 5 seconds
                        setTimeout(function() {
                            $('.alert-success').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 5000);
                    } else {
                        showFormError($form, response.data?.message || 'Update failed. Please try again.');
                    }                    
                },
                error: function(xhr) {
                    $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                    showFormError($form, 'Connection error. Please check your internet and try again.');
                    console.error('Update user error:', xhr);
                }
            });
        }
    });
    
    // Real-time validation on blur
    $('#updateUserForm input, #updateUserForm select').on('blur', function() {
        if ($(this).val()) {
            $(this).valid();
        }
    });
}
});
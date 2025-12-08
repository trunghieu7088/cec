/**
 * Purchase Certificate Page JavaScript
 * Handles AJAX login, signup, and payment processing with Authorize.Net Accept.js
 */

jQuery(document).ready(function($) {
    'use strict';
    var ajaxurl = my_ajax_object.ajax_url;
    var ajaxurl_global = my_ajax_object.ajax_url;
    
    // Configuration
    const config = {
        basePrice: 0.00,
        mailFee: 9.00
    };
    
    // Get Authorize.Net credentials from localized script
    const AUTHNET_CREDENTIALS = {
        apiLoginId: my_ajax_object.authnet_api_login_id || '',
        clientKey: my_ajax_object.authnet_client_key || '',
        mode: my_ajax_object.authnet_mode || 'sandbox'
    };
    
    console.log('🔧 Authorize.Net Mode:', AUTHNET_CREDENTIALS.mode);
    console.log('🔧 API Login ID:', AUTHNET_CREDENTIALS.apiLoginId ? 'Set ✓' : 'Missing ✗');
    console.log('🔧 Client Key:', AUTHNET_CREDENTIALS.clientKey ? 'Set ✓' : 'Missing ✗');
    
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
        
        $('.total-amount, .final-amount').text(total.toFixed(2));
    }
    
    // Event listeners for price updates
    //$('#mail-certificate').on('change', updatePrice);

    // Auto-update when mail certificate checkbox changes
    $('#mail-certificate').on('change', function() {
        const mailFee = $(this).is(':checked') ? parseFloat($('#mail-fee-value').val()) : 0;
        $('#mail-fee').text('$' + mailFee.toFixed(2));
        
        // You can trigger auto-update here if desired
         //$('#update-price-btn').click();
    });

    $('#update-price-btn').on('click', function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        const originalHtml = $btn.html();
        
        // Get values
        const courseId = $('#course-id').val();
        const discountCode = $('#discount-code').val().trim();
        const mailCertificate = $('#mail-certificate').is(':checked') ? 1 : 0;
        
        if (!courseId) {
            showFormError($('#paymentForm'), 'Invalid course information.');
            return;
        }
        
        // Show loading
        $btn.addClass('loading').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Updating...');
        
        // Clear previous validation messages
        $('#discount-validation-msg').html('').removeClass('text-success text-danger');
        
        $.ajax({
            url: ajaxurl_global,
            type: 'POST',
            data: {
                action: 'update_purchase_price',
                course_id: courseId,
                discount_code: discountCode,
                mail_certificate: mailCertificate,
                nonce: my_ajax_object.update_price_nonce || '<?php echo wp_create_nonce("update_price_nonce"); ?>'
            },
            success: function(response) {
                 $btn.removeClass('loading').prop('disabled', false)
                        .html('<i class="bi bi-check-circle-fill me-1"></i>Updated!');
                    
                    setTimeout(function() {
                        $btn.html(originalHtml);
                    }, 1000);
                    
                    if (response.success) {
                        const data = response.data;
                        
                        // Decode HTML entities nếu cần
                        const currencySign = $('<div/>').html(data.currency_sign).text();
                        
                        // Update discount code amount
                        $('#discount-code-amount').text(data.formatted.coupon_discount);
                        
                        // Update mail fee
                        $('#mail-fee').text(data.formatted.mail_fee);
                        
                        // Update total - CHỈ LẤY SỐ, bỏ ký hiệu $
                        const finalPriceNumber = data.final_price.toFixed(2);
                         //update for hidden
                        $('#final-price-amount').val(finalPriceNumber);
                        $('#final-total-amount').text(currencySign + finalPriceNumber);
                        $('.final-amount').text(finalPriceNumber); // ← Chỉ số, không có $
                        
                        // Store coupon ID if valid
                        if (data.coupon_valid) {
                            $('#applied-coupon-id').val(data.coupon_id);
                            $('#discount-validation-msg')
                                .html('<i class="bi bi-check-circle-fill me-1"></i>' + data.coupon_message)
                                .removeClass('text-danger').addClass('text-success');
                        } else {
                            $('#applied-coupon-id').val('');
                            if (discountCode !== '') {
                                $('#discount-validation-msg')
                                    .html('<i class="bi bi-x-circle-fill me-1"></i>' + data.coupon_message)
                                    .removeClass('text-success').addClass('text-danger');
                            } else {
                                $('#discount-validation-msg').html('').removeClass('text-success text-danger');
                            }
                        }
                        
                        // Update purchase button text
                        //$('#purchase-btn .final-amount').text(finalPriceNumber);
                       
                        
                    } else {
                        showFormError($('#paymentForm'), response.data?.message || 'Failed to update price.');
                    }
            },
            error: function(xhr) {
                $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                showFormError($('#paymentForm'), 'Connection error. Please try again.');
                console.error('Update price error:', xhr);
            }
        });
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
     * CVV Formatting - Only numbers
     */
    $('#card_cvv').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
    
    /**
     * Custom Validation Methods
     */
    $.validator.addMethod("phoneUS", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^[\d\s\-\(\)]+$/);
    }, "Please enter a valid phone number.");
    
    $.validator.addMethod("creditcard", function(value, element) {
        value = value.replace(/[\s\-]/g, '');
        if (!/^\d{13,19}$/.test(value)) {
            return false;
        }
        
        // Luhn algorithm
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
    
    $.validator.addMethod("cardExpiry", function(value, element) {
        if (!value) return false;
        
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.getMonth() + 1;
        const expYear = parseInt($('#card_year').val());
        const expMonth = parseInt(value);
        
        if (!expYear) return true;
        
        return expYear > currentYear || (expYear === currentYear && expMonth >= currentMonth);
    }, "Card has expired.");
    
    $.validator.addMethod("cvvValid", function(value, element) {
        return /^\d{3,4}$/.test(value);
    }, "Please enter a valid CVV (3-4 digits).");
    
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
                username: { required: true, minlength: 3 },
                password: { required: true, minlength: 6 }
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
                    url: ajaxurl,
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
                            setTimeout(function() { location.reload(); }, 1000);
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
                fullname: { required: true, minlength: 2 },
                license: { required: true, minlength: 2 },
                license_state: { required: true },
                email: { required: true, email: true },
                phone: { required: true },
                username: { required: true, minlength: 3 },
                password: { required: true, minlength: 6 },
                address: { required: true, minlength: 3 },
                zip: { required: true, minlength: 5, maxlength: 10 },
                city: { required: true, minlength: 3 },
                state: { required: true }
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
                    url: ajaxurl,
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
                           // setTimeout(function() { location.reload(); }, 1500);
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
     * jQuery Validation Setup for Payment Form with Accept.js
     */
    if ($('#paymentForm').length) {

        $('#paymentForm').validate({
            rules: {
                card_number: { required: true, creditcard: true },
                card_month: { required: true, cardExpiry: true },
                card_year: { required: true },
                card_cvv: { required: true, cvvValid: true }
            },
            messages: {
                card_number: { required: "Please enter your card number." },
                card_month: { required: "Please select expiration month." },
                card_year: { required: "Please select expiration year." },
                card_cvv: { required: "Please enter CVV." }
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
                processPaymentWithAcceptJs(form);
            }
        });
        
        // Revalidate month when year changes
        $('#card_year').on('change', function() {
            $('#card_month').valid();
        });
    }
    
    /**
     * Process Payment with Authorize.Net Accept.js
     */
    function processPaymentWithAcceptJs(form) {
        const $form = $(form);
        const $btn = $('#purchase-btn');
        const originalHtml = $btn.html();
        
        // Check if credentials are available
        if (!AUTHNET_CREDENTIALS.apiLoginId || !AUTHNET_CREDENTIALS.clientKey) {
            showFormError($form, 'Payment gateway is not properly configured. Please contact support.');
            console.error('❌ Authorize.Net credentials are missing');
            return;
        }
        
        $form.find('.alert').remove();
        $btn.addClass('loading').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
        
        console.log('🔵 Step 1: Getting payment nonce from Accept.js...');
        
        // Prepare secure data for Accept.js
        const secureData = {
            authData: {
                clientKey: AUTHNET_CREDENTIALS.clientKey,
                apiLoginID: AUTHNET_CREDENTIALS.apiLoginId
            },
            cardData: {
                cardNumber: $('#card_number').val().replace(/\s/g, ''),
                month: $('#card_month').val(),
                year: $('#card_year').val(),
                cardCode: $('#card_cvv').val()
            }
        };
        
        // Check if Accept.js is loaded
        if (typeof Accept === 'undefined') {
            $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
            showFormError($form, 'Payment system not loaded. Please refresh the page and try again.');
            console.error('❌ Accept.js not loaded');
            return;
        }
        
        // Call Accept.js to get payment nonce
        Accept.dispatchData(secureData, function(response) {
            if (response.messages.resultCode === 'Error') {
                // Handle Accept.js error
                let errorMsg = '';
                for (let i = 0; i < response.messages.message.length; i++) {
                    errorMsg += response.messages.message[i].text;
                    if (i < response.messages.message.length - 1) errorMsg += '<br>';
                }
                
                $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                showFormError($form, 'Card validation error: ' + errorMsg);
                console.error('❌ Accept.js Error:', response.messages);
            } else {
                // Success - Got payment nonce
                const paymentNonce = response.opaqueData.dataDescriptor;
                const paymentValue = response.opaqueData.dataValue;
                
                console.log('✅ Step 2: Got payment nonce:', paymentNonce);
                console.log('🔵 Step 3: Sending to WordPress backend...');
                
                // Send to WordPress backend
                sendPaymentToBackend(paymentNonce, paymentValue, $form, $btn, originalHtml);
            }
        });
    }
    
    /**
     * Send payment data to WordPress backend
     */
    function sendPaymentToBackend(paymentNonce, paymentValue, $form, $btn, originalHtml) {
        const completionCode = $form.data('completion-code') || 
                              new URLSearchParams(window.location.search).get('completion_code');
        
        if (!completionCode) {
            $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
            showFormError($form, 'Missing completion code. Please try again.');
            return;
        }
        
        const paymentData = {
            action: 'process_certificate_payment',
            course_id: $form.data('course-id') || '',
            completion_code: completionCode,
            payment_nonce: paymentNonce,
            payment_value: paymentValue,
            ce_discount_amount: $("#ce-discount-amount").val(),
            mail_certificate: $('#mail-certificate').is(':checked') ? 1 : 0,
            coupon_id: $('#applied-coupon-id').val() || 0,
           // total_amount: parseFloat($('.total-amount').text()),
           total_amount: parseFloat($('#final-price-amount').val()),
            nonce: $form.data('nonce')
        };
        
        console.log('📤 Sending payment data:', {
            action: paymentData.action,
            completion_code: paymentData.completion_code,
            total_amount: paymentData.total_amount
        });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: paymentData,
            timeout: 30000,
            success: function(response) {
                console.log('📥 Backend response:', response);
                
                if (response.success) {
                    let successMessage = '<div class="alert alert-success text-center">' +
                        '<i class="bi bi-check-circle-fill me-2" style="font-size: 2rem;"></i>' +
                        '<h4>Payment Successful!</h4>' +
                        '<p>' + (response.data.message || 'Your certificate has been awarded!') + '</p>' +
                        '<p><small>Transaction ID: ' + (response.data.transaction_id || 'N/A') + '</small></p>';
                    
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
            error: function(xhr, status, error) {
                console.error('❌ Payment error:', {xhr, status, error});
                $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                
                let errorMsg = 'Payment processing error. Please try again or contact support.';
                if (status === 'timeout') {
                    errorMsg = 'Request timed out. Please check your order status before trying again.';
                }
                
                showFormError($form, errorMsg);
            }
        });
    }
    
    /**
     * Update User Form Validation
     */
    if ($('#updateUserForm').length) {
        $('#updateUserForm').validate({
            rules: {
                fullname: { required: true, minlength: 2 },
                license: { required: true, minlength: 2 },
                license_state: { required: true },
                email: { required: true, email: true },
                phone: { required: true },
                address: { required: true, minlength: 3 },
                zip: { required: true, minlength: 5, maxlength: 10 },
                city: { required: true, minlength: 3 },
                state: { required: true }
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
                    nonce: my_ajax_object.update_user_nonce
                };
                
                $.ajax({
                    url: ajaxurl_global,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            const successAlert = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>${response.data.message || 'Profile updated successfully!'}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            $form.prepend(successAlert);
                            setTimeout(function() { window.location.reload(); }, 500);
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
    }
    
    /**
     * Update Password Form Validation
     */
    if ($('#updatePasswordForm').length) {
        $.validator.addMethod("passwordMatch", function(value, element) {
            return value === $('#new_password').val();
        }, "Passwords do not match.");

        $('#updatePasswordForm').validate({
            rules: {
                new_password: { required: true, minlength: 6, maxlength: 50 },
                confirm_password: { required: true, minlength: 6, passwordMatch: true }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback d-block',
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
                $(element).closest('.input-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
                $(element).closest('.input-group').removeClass('has-error');
            },
            errorPlacement: function(error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                const $form = $(form);
                const $btn = $form.find('button[type="submit"]');
                const originalHtml = $btn.html();
                
                $form.find('.alert').remove();
                $btn.addClass('loading').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
                
                const formData = {
                    action: 'update_user_password',
                    new_password: $('#new_password').val(),
                    confirm_password: $('#confirm_password').val(),
                    nonce: $form.data('nonce')
                };
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                        
                        if (response.success) {
                            const successAlert = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>${response.data.message || 'Password updated successfully!'}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            $form.prepend(successAlert);
                            
                            // Reset form
                            $form[0].reset();
                            $form.find('.form-control').removeClass('is-valid is-invalid');
                            
                            setTimeout(function() {
                                $('.alert-success').fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }, 5000);
                        } else {
                            showFormError($form, response.data?.message || 'Password update failed. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        $btn.removeClass('loading').prop('disabled', false).html(originalHtml);
                        showFormError($form, 'Connection error. Please check your internet and try again.');
                        console.error('Update password error:', xhr);
                    }
                });
            }
        });
        
        // Revalidate confirm password when new password changes
        $('#new_password').on('input', function() {
            if ($('#confirm_password').val()) {
                $('#confirm_password').valid();
            }
        });
    }
    
    /**
     * Real-time validation on blur
     */
    $('input, select').on('blur', function() {
        // Chỉ validate nếu field thuộc form có validator
        const $form = $(this).closest('form');
        if ($form.length && $form.data('validator') && $(this).val()) {
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
     * Initialize
     */
   // updatePrice();
    
    console.log('✅ Purchase Certificate page initialized with Authorize.Net Accept.js');
    console.log('🔧 Mode:', AUTHNET_CREDENTIALS.mode);
    console.log('🔧 Accept.js loaded:', typeof Accept !== 'undefined' ? 'Yes ✓' : 'No ✗');
});
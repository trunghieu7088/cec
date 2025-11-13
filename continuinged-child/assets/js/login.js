
jQuery(document).ready(function($) {
    // Thiết lập jQuery Validation
    $("#customLoginForm").validate({
        rules: {
            log: {
                required: true,
                minlength: 3
            },
            pwd: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            log: {
                required: "Please enter your username.",
                minlength: "Username must be at least 3 characters long."
            },
            pwd: {
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
            // Hiển thị loading state
            var submitBtn = $(form).find('button[type="submit"]');
            var originalText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Signing in...').prop('disabled', true);
            
            // Submit form
            form.submit();
        }
    });

    // Real-time validation khi người dùng nhập
    $('#user_login, #user_pass').on('blur', function() {
        $(this).valid();
    });

    // Xóa error khi người dùng bắt đầu nhập lại
    $('#user_login, #user_pass').on('input', function() {
        if ($(this).hasClass('is-invalid') && $(this).val().length > 0) {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
        }
    });
});

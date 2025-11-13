jQuery(document).ready(function($) {
    $('#quiz-form').on('submit', function(e) {
        e.preventDefault();
        
        // Kiểm tra xem tất cả câu hỏi đã được trả lời chưa
        let allQuestionsAnswered = true;
        let totalQuestions = $('.quiz-question').length;
        let correctCount = 0;
        let incorrectCount = 0;
        
        $('.quiz-question').each(function() {
            let $question = $(this);
            let questionId = $question.data('question-id');
            let $selectedRadio = $question.find('input[type="radio"]:checked');
            
            if ($selectedRadio.length === 0) {
                allQuestionsAnswered = false;
                // Highlight câu hỏi chưa trả lời
                $question.css('border-color', '#dc3545');
                setTimeout(function() {
                    $question.css('border-color', '');
                }, 2000);
            }
        });
        
        // Nếu chưa trả lời hết thì thông báo
        if (!allQuestionsAnswered) {
            alert('Please answer all questions before submitting!');
            // Scroll đến câu hỏi đầu tiên chưa trả lời
            $('.quiz-question').each(function() {
                let $question = $(this);
                let $selectedRadio = $question.find('input[type="radio"]:checked');
                if ($selectedRadio.length === 0) {
                    $('html, body').animate({
                        scrollTop: $question.offset().top - 100
                    }, 500);
                    return false;
                }
            });
            return false;
        }
        
        // Disable tất cả radio buttons và nút submit
        $('input[type="radio"]').prop('disabled', true);
        $(this).find('button[type="submit"]').prop('disabled', true).text('Test Submitted');
        
        // Kiểm tra từng câu hỏi
        $('.quiz-question').each(function() {
            let $question = $(this);
            let $selectedRadio = $question.find('input[type="radio"]:checked');
            let isCorrect = $selectedRadio.data('correct') == 1;
            let $feedback = $question.find('.question-feedback');
            
            // Chỉ hiển thị border cho khung câu hỏi
            if (isCorrect) {
                correctCount++;
                // Border xanh cho câu đúng
                $question.css({
                    'border': '3px solid #28a745',
                    'border-radius': '8px'
                });
                // Hiển thị thông báo đúng
                $feedback.removeClass('incorrect').addClass('correct')
                    .html('<i class="bi bi-check-circle-fill" style="margin-right: 0.5rem;"></i>Correct! Well done.')
                    .slideDown();
            } else {
                incorrectCount++;
                // Border đỏ cho câu sai - KHÔNG hiển thị đáp án đúng
                $question.css({
                    'border': '3px solid #dc3545',
                    'border-radius': '8px'
                });
                // Chỉ thông báo sai, không cho biết đáp án
                $feedback.removeClass('correct').addClass('incorrect')
                    .html('<i class="bi bi-x-circle-fill" style="margin-right: 0.5rem;"></i>Incorrect. Please review the course material.')
                    .slideDown();
            }
        });
        
        // Tính điểm phần trăm
        let scorePercentage = Math.round((correctCount / totalQuestions) * 100);
        let isPassed = scorePercentage >= 75;
        
        // Cập nhật kết quả
        $('#correct-count').text(correctCount);
        $('#incorrect-count').text(incorrectCount);
        $('#score-value').text(scorePercentage + '%');
        
        // Thêm trạng thái Pass/Fail
        let $resultsCard = $('.results-card');
        let statusHtml = '';
        
        if (isPassed) {
            statusHtml = '<div class="pass-status" style="background: rgba(40, 167, 69, 0.2); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem;">' +
                '<i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #28a745; margin-bottom: 1rem; display: block;"></i>' +
                '<h4 style="color: #28a745; margin: 0;">PASSED!</h4>' +
                '<p style="margin: 0.5rem 0 0 0;">Congratulations! You have successfully passed this test.</p>' +
                '</div>';
                create_completion_code(scorePercentage);
        } else {
            statusHtml = '<div class="fail-status" style="background: rgba(220, 53, 69, 0.2); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem;">' +
                '<i class="bi bi-x-circle-fill" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem; display: block;"></i>' +
                '<h4 style="color: #dc3545; margin: 0;">NOT PASSED</h4>' +
                '<p style="margin: 0.5rem 0 0 0;">You need at least 75% to pass. Please review the material and try again.</p>' +
                '</div>';
        }
        
        // Xóa status cũ nếu có
        $resultsCard.find('.pass-status, .fail-status').remove();
        
        // Thêm status mới
        $resultsCard.find('.score-details').after(statusHtml);
        
        // Hiển thị kết quả
        $('#quiz-results').slideDown(500);
        
        // Scroll đến kết quả
        $('html, body').animate({
            scrollTop: $('#quiz-results').offset().top - 100
        }, 800);
        
        //hide submit test ( chỉ hiển thị lại khi retake test)
        $("#submit-test-btn").hide();

        // Thêm nút Retake Test
        if (!$('.retake-button').length) {
            let retakeButton = '<button type="button" class="btn-enroll retake-button" style="margin-top: 2rem; max-width: 300px;">' +
                '<i class="bi bi-arrow-clockwise" style="margin-right: 0.5rem;"></i>Retake Test' +
                '</button>';
            $('.quiz-actions').append(retakeButton);
        }
    });
    
    // Xử lý nút Retake Test
    $(document).on('click', '.retake-button', function() {
        // Enable lại tất cả radio buttons
        $('input[type="radio"]').prop('disabled', false).prop('checked', false);
        
        // Enable lại nút submit
        $('#quiz-form button[type="submit"]')
            .prop('disabled', false)
            .html('<i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i>Submit Test');
        
        // Ẩn kết quả
        $('#quiz-results').slideUp(500);
        
        // Ẩn feedback và reset style
        $('.question-feedback').slideUp().empty();
        
        // Reset border của câu hỏi
        $('.quiz-question').css({
            'border': '',
            'border-radius': ''
        });
        
        // Xóa nút Retake
        $(this).remove();

        //hiện lại nút submit test
        $("#submit-test-btn").show();
        
        // Scroll lên đầu form
        $('html, body').animate({
            scrollTop: $('#quiz-form').offset().top - 100
        }, 500);
    });
    
    // Hiệu ứng khi chọn đáp án (trước khi submit)
    $('input[type="radio"]').on('change', function() {
        let $question = $(this).closest('.quiz-question');
        // Reset border color khi đã chọn đáp án
        $question.css('border-color', '');
    });

    function create_completion_code(scorePercentage)
    {
        // Call AJAX to create completion record
        $.ajax({
            url: ajaxurl_global,
            type: 'POST',
            data: {
                action: 'create_completion_record',
                course_id: $('#course-id').val(),
                score: scorePercentage,
                nonce: $('#quiz-nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    console.log('Completion code created: ' + response.data.completion_code);
                    
                    // Optional: Display completion code to user
                    /* let completionCodeHtml = '<div class="completion-code-display" style="background: #d4edda; padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 2px solid #28a745;">' +
                        '<strong style="color: #155724;">Your Completion Code:</strong> ' +
                        '<span style="font-size: 1.5rem; color: #155724; font-weight: bold; letter-spacing: 2px;">' + response.data.completion_code + '</span>' +
                        '<p style="margin: 0.5rem 0 0 0; font-size: 0.875rem; color: #155724;text-align:center;">Save this code for your records.</p>' +
                        '</div>'; */

                         let completionCodeHtml = '<div class="completion-code-display" style="background: #d4edda; padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 2px solid #28a745;">' +
                       
                                '<a style="text-decoration:none;color:#295c79;font-size:16px;" href="'+ response.data.print_certificate_url +'">Print Certificate'+'</a>'+
                        '</div>';

                    $('.pass-status').append(completionCodeHtml);
                } else {
                    console.error('Failed to create completion record: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error: ' + error);
            }
        });
    }
});
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
            let $choiceLabel = $selectedRadio.closest('.choice-label');
            
            // Hiển thị kết quả cho từng câu
            if (isCorrect) {
                correctCount++;
                $feedback.removeClass('incorrect').addClass('correct')
                    .html('<i class="bi bi-check-circle-fill" style="margin-right: 0.5rem;"></i>Correct! Well done.')
                    .slideDown();
                $choiceLabel.css({
                    'background': '#d4edda',
                    'border-color': '#28a745'
                });
            } else {
                incorrectCount++;
                // Tìm đáp án đúng
                let $correctRadio = $question.find('input[data-correct="1"]');
                let correctMarker = $correctRadio.siblings('.choice-marker').text();
                
                $feedback.removeClass('correct').addClass('incorrect')
                    .html('<i class="bi bi-x-circle-fill" style="margin-right: 0.5rem;"></i>Incorrect. The correct answer is ' + correctMarker + '.')
                    .slideDown();
                
                // Đánh dấu đáp án sai màu đỏ
                $choiceLabel.css({
                    'background': '#f8d7da',
                    'border-color': '#dc3545'
                });
                
                // Đánh dấu đáp án đúng màu xanh
                $correctRadio.closest('.choice-label').css({
                    'background': '#d4edda',
                    'border-color': '#28a745'
                });
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
        $('.choice-label').css({
            'background': '',
            'border-color': ''
        });
        $('.quiz-question').css('border-color', '');
        
        // Xóa nút Retake
        $(this).remove();
        
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
});
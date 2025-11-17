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
$(this).find('button[type="submit"]').prop('disabled', true).text('Submitting...');
        
// Collect answers
let answers = {};
$('.quiz-question').each(function() {
    let $question = $(this);
    let questionId = $question.data('question-id');
    let $selectedRadio = $question.find('input[type="radio"]:checked');
    
    if ($selectedRadio.length > 0) {
        answers[questionId] = $selectedRadio.val();
    }
});

// Submit to server for grading
$.ajax({
    url: ajaxurl_global,
    type: 'POST',
    data: {
        action: 'grade_quiz_submission',
        course_id: $('#course-id').val(),
        answers: answers,
        nonce: $('#quiz-nonce').val()
    },
    success: function(response) {
        if (response.success) {
            let data = response.data;
            
            // Display results for each question
            $('.quiz-question').each(function() {
                let $question = $(this);
                let questionId = $question.data('question-id');
                let result = data.results[questionId];
                let $feedback = $question.find('.question-feedback');
                
                if (!result.submitted) {
                    return; // Skip unanswered questions
                }
                
                if (result.is_correct) {
                    $question.css({
                        'border': '3px solid #28a745',
                        'border-radius': '8px'
                    });
                    $feedback.removeClass('incorrect').addClass('correct')
                        .html('<i class="bi bi-check-circle-fill" style="margin-right: 0.5rem;"></i>Correct! Well done.')
                        .slideDown();
                } else {
                    $question.css({
                        'border': '3px solid #dc3545',
                        'border-radius': '8px'
                    });
                    $feedback.removeClass('correct').addClass('incorrect')
                        .html('<i class="bi bi-x-circle-fill" style="margin-right: 0.5rem;"></i>Incorrect. Please review the course material.')
                        .slideDown();
                }
            });
            
            // Update results display
            $('#correct-count').text(data.correct_count);
            $('#incorrect-count').text(data.incorrect_count);
            $('#score-value').text(data.score_percentage + '%');
            
            // Add Pass/Fail status
            let $resultsCard = $('.results-card');
            let statusHtml = '';
            
            if (data.is_passed) {
                statusHtml = '<div class="pass-status" style="background: rgba(40, 167, 69, 0.2); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem;">' +
                    '<i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #28a745; margin-bottom: 1rem; display: block;"></i>' +
                    '<h4 style="color: #28a745; margin: 0;">PASSED!</h4>' +
                    '<p style="margin: 0.5rem 0 0 0;">Congratulations! You have successfully passed this test.</p>';
                
                if (data.completion_code && data.print_certificate_url) {
                    statusHtml += '<div class="completion-code-display" style="background: #d4edda; padding: 1rem; border-radius: 8px; margin-top: 1rem; border: 2px solid #28a745;">' +
                        '<a style="text-decoration:none;color:#295c79;font-size:16px;" href="' + data.print_certificate_url + '">Print Certificate</a>' +
                        '</div>';
                }
                
                statusHtml += '</div>';
            } else {
                statusHtml = '<div class="fail-status" style="background: rgba(220, 53, 69, 0.2); padding: 1.5rem; border-radius: 10px; margin-top: 1.5rem;">' +
                    '<i class="bi bi-x-circle-fill" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem; display: block;"></i>' +
                    '<h4 style="color: #dc3545; margin: 0;">NOT PASSED</h4>' +
                    '<p style="margin: 0.5rem 0 0 0;">You need at least 75% to pass. Please review the material and try again.</p>' +
                    '</div>';
            }
            
            // Remove old status
            $resultsCard.find('.pass-status, .fail-status').remove();
            
            // Add new status
            $resultsCard.find('.score-details').after(statusHtml);
            
            // Show results
            $('#quiz-results').slideDown(500);
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#quiz-results').offset().top - 100
            }, 800);
            
            // Hide submit button
            $("#submit-test-btn").hide();
            
            // Add Retake button
            if (!$('.retake-button').length) {
                let retakeButton = '<button type="button" class="btn-enroll retake-button" style="margin-top: 2rem; max-width: 300px;">' +
                    '<i class="bi bi-arrow-clockwise" style="margin-right: 0.5rem;"></i>Retake Test' +
                    '</button>';
                $('.quiz-actions').append(retakeButton);
            }
            
        } else {
            alert('Error: ' + response.data.message);
            // Re-enable form
            $('input[type="radio"]').prop('disabled', false);
            $('#quiz-form button[type="submit"]').prop('disabled', false).text('Submit Test');
        }
    },
    error: function(xhr, status, error) {
        console.error('AJAX error: ' + error);
        alert('An error occurred while submitting the quiz. Please try again.');
        // Re-enable form
        $('input[type="radio"]').prop('disabled', false);
        $('#quiz-form button[type="submit"]').prop('disabled', false).text('Submit Test');
    }
});
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

});
<?php
use yii\helpers\Url;

$sessionIdJson = json_encode($_GET['session_id']);
$answerUrl = Url::to(['mcq/save-answer']);
$skipUrl = Url::to(['mcq/skip-question']);
$toggleBookmarkUrl = Url::to(['exam/toggle-bookmark']);
$reportUrl = Url::to(['default/report-mcq']);
$examMode = json_encode($mode);
$allBookmarkedMcqIdsJson = json_encode($allBookmarkedMcqIds ?? []);
$js = <<<JS
const mode = $examMode;
const sessionId = $sessionIdJson;
let bookmarkedMcqIds = $allBookmarkedMcqIdsJson;

$(document).on('click', '.btn.btn-outline-primary[title="Calculator"]', function () {
    $('#calculator-box').toggleClass('d-none');
});

$(document).on('click', '.calc-btn', function () {
    let val = $(this).text();
    let display = $('#calc-display');

    if (val === '=') {
        try {
            display.val(eval(display.val()));
        } catch (e) {
            showToast('Invalid expression', 'danger');
        }
    } else {
        display.val(display.val() + val);
    }
});


function updateBookmarkButtonState(mcqId) {
    const button = $('.btn-bookmark');
    button.prop('disabled', false).removeClass('disabled');

    if (bookmarkedMcqIds.includes(parseInt(mcqId))) {
        button.html('<i class="fas fa-bookmark me-1"></i> Bookmarked').addClass('active btn-info');
    } else {
        button.html('<i class="far fa-bookmark me-1"></i> Bookmark').removeClass('active btn-info');
    }
    button.data('mcq-id', mcqId);
}

function updateExamUI(res) {
    if (res.redirectUrl) {
        window.location.href = res.redirectUrl;
        return;
    }
    if (res.success) {
        $('#question-container').html(res.questionHtml);
        $('#progress-bar').css('width', res.progress.percent + '%').text(res.progress.percent + '%');
        $('.attempted').text(res.progress.attempted);
        $('.total-questions-in-exam').text(res.progress.total_questions_in_exam);
   
        const skippedCountElement = $('#skipped-count');
        if (res.progress.skipped > 0) {
            if (skippedCountElement.length === 0) {
                $('#skipped-count').text(res.progress.skipped + ' Skipped');
            } else {
                skippedCountElement.text(res.progress.skipped);
            }
        } else {
            skippedCountElement.closest('small.text-warning').remove();
        }

        if (mode === 'practice') {
            $('.btn-submit-answer').removeClass('d-none');
            $('.btn-next').addClass('d-none');
            $('.answer-options input[type="radio"]').prop('disabled', false);
            $('.list-group-item').removeClass('bg-success bg-danger text-white');
            $('.explanation-card').addClass('d-none');
            $('#collapseOne, #collapseTwo').collapse('hide');
        } else { 
             $('.answer-options input[type="radio"]').prop('disabled', false).prop('checked', false);
             $('.list-group-item').removeClass('bg-success bg-danger text-white');
        }
        const newMcqId = $('#question-id').val();
        updateBookmarkButtonState(newMcqId);
        hideloader();
    } else {
        showToast(res.message || 'An error occurred.', 'danger');
        if (res.redirectUrl) {
            setTimeout(() => window.location.href = res.redirectUrl, 2000);
        }
    }
}

$(document).on('click', '.btn-submit-answer', function () {
    const selected = $('input[name="answer"]:checked').val();
    if (!selected) {
        showToast('Please select an answer first.', 'warning');
        return;
    }


    $('input[name="answer"]').prop('disabled', true);

    $('.answer-options input[type="radio"]').each(function () {
        const label = $(this).closest('label');
        const val = $(this).val();
        const correct = $('#correct-answer').val();
        label.removeClass('bg-success bg-danger text-white');
        if (val === correct) {
            label.addClass('bg-success text-white');
        }
        if (val === selected && val !== correct) {
            label.addClass('bg-danger text-white');
        }
    });

    $('.explanation-card').removeClass('d-none');

    $('.btn-submit-answer').addClass('d-none');
    $('.btn-next').removeClass('d-none');
});

$(document).on('click', '.btn-next', function () {
    let selectedOption = $('.answer-options input[type="radio"]:checked').val();
    let questionId = $('#question-id').val();

    if (!selectedOption) {
        showToast('Please select an answer or skip to proceed.', 'warning');
        return;
    }

    $.post('$answerUrl', {
        question_id: questionId,
        answer: selectedOption,
        session_id: sessionId,
        _csrf: yii.getCsrfToken()
    }, updateExamUI).fail(function(jqXHR, textStatus, errorThrown) {
        showToast('AJAX Error: ' + textStatus + ' - ' + errorThrown, 'danger');
    });
});

$(document).on('click', '.btn-submit-answer-and-next', function () {
    let selectedOption = $('.answer-options input[type="radio"]:checked').val(); 
    let questionId = $('#question-id').val();

    if (!selectedOption) {
        showToast('Please select an answer or skip to proceed.', 'warning');
        return;
    }

    $.post('$answerUrl', {
        question_id: questionId,
        answer: selectedOption,
        session_id: sessionId,
        _csrf: yii.getCsrfToken()
    }, updateExamUI).fail(function(jqXHR, textStatus, errorThrown) {
        showToast('AJAX Error: ' + textStatus + ' - ' + errorThrown, 'danger');
    });
});

$(document).on('click', '.btn-skip-question', function () {
    let questionId = $('#question-id').val();

    $.post('$skipUrl', {
        question_id: questionId,
        session_id: sessionId,
        _csrf: yii.getCsrfToken()
    }, updateExamUI).fail(function(jqXHR, textStatus, errorThrown) {
        showToast('Error: ' + textStatus + ' - ' + errorThrown, 'danger');
    });
});

$(function () {
    var timeLeft = parseInt($timeLeft) || 0;
    var display = $('#time-display');

    if (!display.length) {
        return;
    }

    function updateTimer() {
        if (!parseInt($timeLeft)) {
            clearInterval(timerInterval);
            return;
        }
        if (parseInt($timeLeft) && timeLeft <= 0) {
            showToast("⏰ Time's up! Submitting your final answer...", "danger");
            clearInterval(timerInterval);
            return;
        }

        if (timeLeft === 600) showToast("⚠️ Less than 10 minutes remaining", "warning");
        if (timeLeft === 300) showToast("⚠️ Only 5 minutes left", "warning");
        if (timeLeft === 60) showToast("⏳ 1 minute remaining!", "warning");

        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;

        display.text(
            (minutes < 100 ? ("0" + minutes).slice(-2) : minutes) + ":" + ("0" + seconds).slice(-2)
        );

        timeLeft--;
    }

    updateTimer();
    var timerInterval = setInterval(updateTimer, 1000);
});

$(document).on('click', '.btn-bookmark', function() {
    const button = $(this);
    const mcqId = $('#question-id').val();
    const csrfToken = yii.getCsrfToken();

    button.prop('disabled', true).addClass('disabled');

    $.post('$toggleBookmarkUrl', { mcq_id: mcqId, _csrf: csrfToken })
        .done(function(res) {
            if (res.success) {
                const mcqIdInt = parseInt(mcqId);
                if (res.action === 'added') {
                    if (!bookmarkedMcqIds.includes(mcqIdInt)) {
                        bookmarkedMcqIds.push(mcqIdInt);
                    }
                    showToast(res.message, 'success');
                } else if (res.action === 'removed') {
                    bookmarkedMcqIds = bookmarkedMcqIds.filter(id => id !== mcqIdInt);
                    showToast(res.message, 'info');
                }
                updateBookmarkButtonState(mcqId);
            } else {
                showToast(res.message, 'danger');
                if (res.code === 403) {
                }
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            showToast('AJAX Error: ' + (jqXHR.responseJSON ? jqXHR.responseJSON.message : errorThrown), 'danger');
        })
        .always(function() {
        });
});

$(document).on("submit", "#reportForm", function(e) {
    e.preventDefault();

    let mcqId = $('#report-id').val();
    let message = $("#report-message").val().trim();

    if (!message) {
        showToast("Please enter a message before submitting.", "warning");
        return;
    }

    $.ajax({
        url: "$reportUrl",
        type: "POST",
        data: {
            mcq_id: mcqId,
            message: message,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            if (response.success) {
                $("#reportModal").modal("hide");
                $("#report-message").val(""); 
                showToast("Report submitted successfully!", "success");
            } else {
                showToast(response.error || "Something went wrong.", 'danger');
            }
        },
        error: function() {
            showToast("Server error. Please try again later.", "danger");
        }
    });
});


$(document).ready(function() {
    const initialMcqId = $('#question-id').val();
    updateBookmarkButtonState(initialMcqId);
});

$(document).on('click', '.answer-options .list-group-item', function() {
    if (mode === 'practice' && !$('.btn-next').hasClass('d-none')) {
        return;
    }
    $('.answer-options .list-group-item').removeClass('active');
    $(this).addClass('active');
    $(this).find('input[type="radio"]').prop('checked', true);
});

JS;

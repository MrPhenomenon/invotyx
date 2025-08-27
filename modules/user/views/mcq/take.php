<?php
use yii\helpers\Url;

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

if ($isRevisitingSkipped) {
    $hideSkip = 'd-none';
} else {
    $hideSkip = '';
}
?>

<style>
    /* Make the layout full-height */
    body,
    html {
        height: 100%;
        background-color: #f8f9fa;
        transition: all 0.3s ease-in-out;
        /* Light grey background */
    }

    #question-container {
        transition: all 0.3s ease-in-out;
    }

    @media (min-width: 1600px) {
        .container {
            max-width: 1200px;
        }
    }

    .progress {
        background-color: #e9ecef;
    }

    .exam-wrapper {
        display: flex;
        flex-direction: column;
        height: 100vh;
        /* Full viewport height */
    }

    /* Main content area should be scrollable */
    .main-content {
        flex-grow: 1;
        overflow-y: auto;
    }

    /* Custom styling for options */
    .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s ease-in-out;
    }

    /* Custom style for the selected option */
    .list-group-item.active {
        background-color: #e7f1ff;
        border-color: #a9c7f5;
        color: #000;
    }

    /* Change the color of the radio button itself when active */
    .list-group-item.active .form-check-input {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Style for the accordion button when not collapsed */
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0c63e4;
    }

    .timer {
        min-width: 100px;
        /* Prevents layout shift as timer changes */
    }
</style>

<div class="exam-wrapper d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <div class="p-3 bg-light border-bottom shadow-sm">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="fw-bold text-nowrap">
                    <span class="attempted"><?= $progress['attempted'] ?></span> / <span
                        class="total-questions-in-exam"><?= $progress['total_questions_in_exam'] ?></span> Questions
                    <?php if ($progress['skipped'] > 0): ?>
                        <br><small class="text-warning"><i class="fas fa-forward me-1"></i> <span
                                id="skipped-count"><?= $progress['skipped'] ?></span> Skipped</small>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1 mx-3 mx-md-5">
                    <div class="progress" style="height: 15px;">
                        <div class="progress-bar bg-primary" style="width: <?= $progress['percent'] ?>%;"
                            id="progress-bar">
                            <?= $progress['percent'] ?>%
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-primary rounded-circle me-3" title="Calculator"
                        style="padding-left: 12px;padding-right: 12px;">
                        <i class="fas fa-calculator"></i>
                    </button>
                    <div class="timer fw-bold text-nowrap">
                        <i class="far fa-clock me-2"></i>
                        <span id="time-display"><?= gmdate('i:s', $timeLeft ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content py-4 py-md-5 flex-grow-1 overflow-auto">
        <div class="container">
            <div id="exam-view">
                <div id="question-container" class="mb-4">
                    <?= $this->render('partials/_question', [
                        'mode' => $mode ?? 'practice',
                        'mcq' => $mcq,
                        'currentPhaseIndex' => $currentPhaseIndex, // Pass to partial
                        'totalQuestionsInPhase' => $totalQuestionsInPhase, // Pass to partial
                        'isRevisitingSkipped' => $isRevisitingSkipped, // Pass to partial
                        'overallQuestionNumber' => $overallQuestionNumber, // Pass to partial
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-3 bg-light border-top shadow-sm">
        <div class="container">
            <?php if ($mode === 'practice'): ?>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger" id="report-btn" type="button">
                            <i class="fas fa-exclamation-triangle me-1"></i> Report
                        </button>
                       <button class="btn btn-outline-info btn-bookmark" type="button" data-mcq-id="">
                            <i class="far fa-bookmark me-1"></i> Bookmark
                        </button>
                        <button class="btn btn-outline-secondary btn-skip-question <?= $hideSkip ?>" type="button"
                            title="Skip this question for now">
                            <i class="fas fa-forward me-1"></i> Skip
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary btn-submit-answer" type="button">
                            <i class="bi bi-check2-circle me-1"></i> Submit Answer
                        </button>
                        <button class="btn btn-primary btn-next d-none" type="button">
                            <i class="bi bi-arrow-right me-1"></i> Next
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger" id="report-btn" type="button">
                            <i class="fas fa-exclamation-triangle me-1"></i> Report
                        </button>
                        <button class="btn btn-outline-info btn-bookmark" type="button" data-mcq-id="">
                            <i class="far fa-bookmark me-1"></i> Bookmark
                        </button>
                        <button class="btn btn-outline-secondary btn-skip-question <?= $hideSkip ?>" type="button"
                            title="Skip this question for now">
                            <i class="fas fa-forward me-1"></i> Skip
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-primary btn-submit-answer-and-next" type="button">
                            Submit and Next
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<?php
$sessionIdJson = json_encode($_GET['session_id']);
$answerUrl = Url::to(['mcq/save-answer']);
$skipUrl = Url::to(['mcq/skip-question']);
$toggleBookmarkUrl = Url::to(['exam/toggle-bookmark']);
$examMode = json_encode($mode);
$allBookmarkedMcqIdsJson = json_encode($allBookmarkedMcqIds ?? []);
$js = <<<JS
const mode = $examMode;
const sessionId = $sessionIdJson;
let bookmarkedMcqIds = $allBookmarkedMcqIdsJson;

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
t
                $('.fw-bold.text-nowrap').append('<br><small class="text-warning"><i class="fas fa-forward me-1"></i> <span id="skipped-count">' + res.progress.skipped + '</span> Skipped</small>');
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
        showToast('AJAX Error: ' + textStatus + ' - ' + errorThrown, 'danger');
    });
});

$(function () {
    var timeLeft = parseInt($timeLeft) || 0;
    var display = $('#time-display');

    if (!display.length) {
        return;
    }

    function updateTimer() {
        if (timeLeft <= 0) {
            showToast("⏰ Time's up! Submitting your final answer...", "danger");
            clearInterval(timerInterval);
            $.post(
                "<?= Url::to(['exam/time-up']) ?>",
                { session_id: sessionId, _csrf: yii.getCsrfToken() },
                function(res) {
                    if (res.success) {
                        window.location.href = "<?= Url::to(['results/view', 'id' => '']) ?>" + sessionId;
                    } else {
                        showToast(res.message || 'Failed to auto-submit exam.', 'danger');
                    }
                }
            ).fail(function() {
                showToast('Failed to reach server for auto-submission.', 'danger');
            });
            return;
        }

        // Time warnings
        if (timeLeft === 600) showToast("⚠️ Less than 10 minutes remaining", "warning");
        if (timeLeft === 300) showToast("⚠️ Only 5 minutes left", "warning");
        if (timeLeft === 60) showToast("⏳ 1 minute remaining!", "warning");

        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;

        display.text(
            ("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2)
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
$this->registerJS($js, yii\web\View::POS_END)
    ?>
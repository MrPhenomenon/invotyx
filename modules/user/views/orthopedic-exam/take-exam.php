<?php
use yii\helpers\Url;

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

/* @var $this yii\web\View */
/* @var $mcq app\models\PartnerMcqs */
/* @var $progress array */
/* @var $timeLeft int */
/* @var $attempt int */
/* @var $skippedCount int */
/* @var $mode string */ // 'exam' or 'practice'
/* @var $actualQuestionNumber int */ // The original question number (1-indexed, continuous across parts)
/* @var $isRevisiting bool */ // True if currently revisiting skipped questions
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
        background-color: #0e273c;
        border-color: #0e273c;
    }

    /* Style for the accordion button when not collapsed */
    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #0e273c;
    }

    .timer {
        min-width: 100px;
        /* Prevents layout shift as timer changes */
    }
</style>
<div id="calculator-box" class="position-fixed top-0 end-0 m-4 shadow rounded bg-white border p-3 d-none"
    style="width: 260px; z-index: 9999;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Calculator</strong>
        <button class="btn btn-sm btn-outline-danger" onclick="$('#calculator-box').addClass('d-none')">&times;</button>
    </div>
    <input type="text" class="form-control mb-2" id="calc-display" readonly>
    <div class="d-grid gap-1" style="grid-template-columns: repeat(4, 1fr);">
        <?php foreach (['7', '8', '9', '/', '4', '5', '6', '*', '1', '2', '3', '-', '0', '.', '=', '+'] as $btn): ?>
            <button class="btn btn-light btn-sm calc-btn"><?= $btn ?></button>
        <?php endforeach; ?>
        <button class="btn btn-warning btn-sm col-12" onclick="$('#calc-display').val('')">Clear</button>
    </div>
</div>
<div class="exam-wrapper d-flex flex-column" style="min-height: 100vh;">

    <!-- Header -->
    <div class="p-3 bg-light border-bottom shadow-sm">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="fw-bold">
                    <span class="attempted"><?= $progress['attempted'] ?></span> / <?= $progress['total'] ?> Questions
                    <br>
                    <div class="fw-bold text-danger" title="You must answer these before continuing">
                        Skipped Questions: <span class="skipped-count"><?= $skippedCount ?></span>
                    </div>
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
                        <span id="time-display">
                            <?php
                            $initialMinutes = floor(($timeLeft ?? 0) / 60);
                            $initialSeconds = ($timeLeft ?? 0) % 60;
                            echo sprintf('%02d:%02d', $initialMinutes, $initialSeconds);
                            ?>
                        </span>
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
                        'actualQuestionNumber' => $actualQuestionNumber,
                        'isRevisiting' => $isRevisiting,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="p-3 bg-light border-top shadow-sm">
        <div class="container">

            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                <div id="skip-button-container" <?= $isRevisiting ? 'class="d-none"' : '' ?>>
                    <button class="btn btn-outline-primary btn-skip" type="button">
                        Skip Question
                    </button>
                </div>
                <div>
                    <button class="btn btn-primary btn-next" type="button">
                        Submit and Next
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- The enlarged image will be loaded into this img tag -->
                <img src="" id="modal-image-content" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="skippedQuestionsModal" tabindex="-1" aria-labelledby="skippedQuestionsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title" id="skippedQuestionsModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>
                    Important Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="lead text-center">
                    You have completed all initial questions for this part.
                </p>
                <p class="text-center fs-5 text-dark fw-bold">
                    You will now attempt your <span class="text-danger">skipped questions</span>.
                </p>
                <p class="text-center text-muted">
                    Please note: Once you are in this phase, you cannot skip questions again for this part. All
                    remaining questions must be answered.
                </p>
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Understood</button>
            </div>
        </div>
    </div>
</div>

<?php
$skipUrl = Url::to(['orthopedic-exam/skip-question']);
$answerUrl = Url::to(['orthopedic-exam/save-answer']);
$sessionId = $attempt;
$csrfToken = Yii::$app->request->csrfToken;
$jsTimeLeft = $timeLeft;

$js_modal_and_calculator = <<<JS

// Calculator Logic
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

// Image Modal Logic
const imageModal = document.getElementById('imagePreviewModal');
if (imageModal) {
    imageModal.addEventListener('show.bs.modal', function (event) {
        const triggerLink = event.relatedTarget;
        const imageUrl = triggerLink.getAttribute('data-image-url');     
        const modalImage = imageModal.querySelector('#modal-image-content');
        modalImage.src = imageUrl;
    });
}
JS;

$this->registerJs($js_modal_and_calculator, \yii\web\View::POS_READY);

$jsInitialIsRevisiting = json_encode($isRevisiting);
$heartbeatUrl = Url::to(['orthopedic-exam/heartbeat']);
$js_exam_logic = <<<JS

$(function () {
    var timeLeft = parseInt($jsTimeLeft) || 0;
    var display = $('#time-display');
    var timerRunning = true;

    if (!display.length) {
        return;
    }

    function updateTimer() {
        if (!timerRunning) {
            clearInterval(timerInterval);
            return;
        }

        if (timeLeft <= 600 && timeLeft > 599) showToast("⚠️ Less than 10 minutes remaining for this part!", "warning");
        if (timeLeft <= 300 && timeLeft > 299) showToast("⚠️ Only 5 minutes left for this part!", "warning");
        if (timeLeft === 60) showToast("⏳ 1 minute remaining for this part!", "warning");

        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;

        var formattedMinutes = String(minutes).padStart(2, '0');
        var formattedSeconds = String(seconds).padStart(2, '0');

        display.text(formattedMinutes + ":" + formattedSeconds);

        if (timeLeft <= 0) {
            showToast("⏰ Time's up for this part! Please submit your current answer to proceed.", "danger");
            clearInterval(timerInterval); // Stop the visual timer
            timerRunning = false; // Mark timer as not running
            // Optionally, visually indicate time is up for the user (e.g., flash the timer)
            display.addClass('text-danger fw-bold'); // Example styling
            // DO NOT REDIRECT HERE. Let the save/skip logic handle the transition.
        }

        timeLeft--;
    }

    updateTimer();
    var timerInterval = setInterval(updateTimer, 1000);
});

$(document).ready(function() {
    var attemptId = '$sessionId';
    var passkey = '$passkey';
    var csrf = '$csrfToken';
    var heartbeatInterval;
    var heartbeatPeriod = 1000 * 10;

    function sendHeartbeat() {
        $.ajax({
        url: '{$heartbeatUrl}',
        type: 'POST',
        data: {  
            attempt_id: attemptId,
            passkey: passkey,
            _csrf: csrf
        },
        _excludeFromGlobalLoader: true, 

        success: function(res) {
            if (!res.success) {
                console.error("Heartbeat failed: " + res.message);
                if (res.redirectUrl) {
                    window.location.href = res.redirectUrl;
                }
            }
        },
    
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("Heartbeat AJAX error: " + textStatus + " - " + errorThrown);
        }
    });
    }
    heartbeatInterval = setInterval(sendHeartbeat, heartbeatPeriod);

});

var lastIsRevisitingState = false;

function showSkippedQuestionNotification(isRevisitingNow) {
    
    if (isRevisitingNow && !lastIsRevisitingState) { 
        $('#skippedQuestionsModal').modal('show');
    }
    lastIsRevisitingState = isRevisitingNow;
}

// Skip Question Logic
$('.btn-skip').on('click', function () {
    const questionId = $('#question-id').val();
    const sessionId = '$sessionId';
     const passkey = '$passkey'
    const skipUrl = '$skipUrl';
    const csrf = '$csrfToken';

    $.post(skipUrl, {
        question_id: questionId,
        session_id: sessionId,
         passkey: passkey,
        _csrf: csrf
    }, function (res) {
        if (res.success) {
            if (res.redirectUrl) {
                window.location.href = res.redirectUrl;
            } else {
                $('#question-container').html(res.questionHtml);
                $('.progress-bar').css('width', res.progress.percent + '%').text(res.progress.percent + '%');
                $('.attempted').text(res.progress.attempted);
                $('.skipped-count').text(res.skippedCount);

                 updateSkipButtonVisibility(res.isRevisiting);
                 showSkippedQuestionNotification(res.isRevisiting);

                $('.answer-options input[type="radio"]').prop('checked', false).parent().removeClass('active');
                
                // Hide loader if any was showing
                if (typeof hideloader === 'function') hideloader(); 
            }
        } else {
            showToast(res.message || 'Failed to skip question.', 'danger');
            if (res.redirectUrl) { // Redirect on fatal error/session expiry
                window.location.href = res.redirectUrl;
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        showToast('AJAX Error: ' + textStatus + ' - ' + errorThrown, 'danger');
        console.error(jqXHR.responseText);
    });
});

function updateSkipButtonVisibility(isRevisiting) {
    if (isRevisiting) {
        $('#skip-button-container').addClass('d-none');
    } else {
        $('#skip-button-container').removeClass('d-none');
    }
}
$(document).ready(function() {
    updateSkipButtonVisibility($jsInitialIsRevisiting);
});

// Submit and Next Question Logic
$('.btn-next').on('click', function () {
    let selectedOption = $('.answer-options input[type="radio"]:checked').val();
    let questionId = $('#question-id').val();
    let sessionId = '$sessionId';
    let passkey = '$passkey'; 
    let answerUrl = '$answerUrl';
    let csrf = '$csrfToken';

    if (!selectedOption) {
        showToast('Please select an answer before proceeding.', 'danger');
        return;
    }

    $.post(answerUrl, {
        question_id: questionId,
        answer: selectedOption,
        session_id: sessionId,
         passkey: passkey,
        _csrf: csrf
    }, function (res) {
        if (res.success) {
            if (res.redirectUrl) {
                window.location.href = res.redirectUrl;
            } else {
                $('#question-container').html(res.questionHtml);
                $('.progress-bar').css('width', res.progress.percent + '%').text(res.progress.percent + '%');
                $('.attempted').text(res.progress.attempted);
                $('.skipped-count').text(res.skippedCount);


                $('.answer-options input[type="radio"]').prop('checked', false).parent().removeClass('active');
                updateSkipButtonVisibility(res.isRevisiting);
                 showSkippedQuestionNotification(res.isRevisiting);

                if (typeof hideloader === 'function') hideloader(); 
            }
        } else {
            showToast(res.message || 'Failed to save answer.', 'danger');
            if (res.redirectUrl) {
                window.location.href = res.redirectUrl;
            }
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        showToast('AJAX Error: ' + textStatus + ' - ' + errorThrown, 'danger');
        console.error(jqXHR.responseText);
    });
});

// Dynamic 'active' class for selected option
$(document).on('change', '.answer-options input[type="radio"]', function() {
    $('.list-group-item').removeClass('active');
    $(this).closest('.list-group-item').addClass('active');
});

// Initial setup for existing question, if any
$(document).ready(function() {
    // If there's a pre-selected answer (e.g., on page refresh), activate it
    const selectedAnswer = $('#question-container').data('selected-answer'); // If you were storing answers on client-side
    if (selectedAnswer) {
        $('.answer-options input[type="radio"][value="' + selectedAnswer + '"]').prop('checked', true).trigger('change');
    }
});

JS;
$this->registerJS($js_exam_logic, yii\web\View::POS_END);
?>
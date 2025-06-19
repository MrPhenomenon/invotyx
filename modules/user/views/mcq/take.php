<?php
use yii\helpers\Url;

?>
<style>
    body {
        margin: 0;
        padding: 20px;
        background-color: #f5f7fa;
        font-family: 'Segoe UI', sans-serif;
        min-height: 100vh;
    }

    .exam-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .exam-header {
        color: #fff;
        padding: 15px 20px;
        border-radius: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .exam-header .timer {
        background-color: #fff;
        color: #0e273c;
        padding: 5px 12px;
        font-weight: 600;
        border-radius: 30px;
        margin-left: 10px;
    }

    .question-card {
        padding: 20px;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    .answer-options .list-group-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .answer-options .list-group-item:hover {
        background-color: #f1f3f5;
    }

    .question-nav-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(36px, 1fr));
        gap: 6px;
    }

    .question-nav-grid .btn {
        font-size: 0.8rem;
        padding: 0.3rem 0.5rem;
    }

    .btn-answered {
        background-color: #198754;
        color: white;
    }

    .btn-current {
        background-color: #0d6efd;
        color: white;
        border: 2px solid #0a58ca;
    }

    .btn-unanswered {
        background-color: #dee2e6;
        color: #000;
    }

    .btn-flagged {
        background-color: #ffc107;
        color: black;
    }

    .navigation-bar .btn {
        flex: 1;
        min-width: 100px;
    }

    .progress-info .progress {
        height: 1.2rem;
    }

    @media (max-width: 768px) {
        .navigation-bar {
            flex-direction: column;
        }
    }

    .summary-card {
        display: flex;
        flex-direction: column;
        height: 75%;
        max-height: 80vh;
    }

    .question-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
        gap: 6px;
        overflow-y: auto;
        max-height: 300px;
        padding-right: 4px;
    }

    .progress-footer {
        margin-top: auto;
        padding-top: 10px;
    }
</style>

<div class="exam-container container">
    <!-- Header -->
    <div class="exam-header bg-primary">
        <div><strong>CS001</strong> (VU - Computer Proficiency License)</div>
        <div>
            <span class="timer" id="time-display"><?= gmdate('i:s', $timeLeft ?? 0) ?></span>
            <span class="ms-3"><i class="fas fa-user-circle me-1"></i> <?= Yii::$app->user->identity->name ?></span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Question Area -->
        <div class="col-lg-8">
            <div class="question-card p-3 shadow-sm border rounded"
                style="height: auto; max-height: 80vh; display: flex; flex-direction: column;">
                <div id="question-container">
                    <?= $this->render('partials/_question', [
                        'mcq' => $mcq,
                        'index' => $index,
                        'total' => $total,
                    ]) ?>
                </div>
            </div>
        </div>


        <!-- Summary & Progress Area -->
        <div class="col-lg-4">
            <div class="summary-card card shadow-sm mb-4">
                <div class="card-header bg-light">Summary</div>
                <div class="card-body">
                    <div class="question-nav-grid">
                        <?= $navGridHtml ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 progress-info">
                <div class="card-header bg-light">Progress</div>
                <div class="card-body">
                    <div class="progress mb-2">
                        <div class="progress-bar bg-primary" style="width: <?= $progress['percent'] ?>%;">
                            <?= $progress['percent'] ?>%
                        </div>
                    </div>
                    <p class="mb-1">Attempted: <strong class="attempted"><?= $progress['attempted'] ?></strong></p>
                    <p class="mb-1">Flagged: <strong class="flagged"><?= $progress['flagged'] ?></strong></p>
                    <p class="mb-0">Total: <strong><?= $progress['total'] ?></strong></p>
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-outline-primary btn-back" type="button">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <button class="btn btn-primary btn-next" type="button">
                    <i class="fas fa-save me-1"></i> Submit and Next
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$sessionIdJson = json_encode($_GET['session_id']);
$answerUrl = Url::to(['mcq/save-answer']);
$js = <<<JS
// $('.answer-options input[type="radio"]').on('change', function () {
//     let selectedOption = $(this).val();
//     let questionId = $('#question-id').val();

//     $.post('/exam/save-answer', {
//         question_id: questionId,
//         answer: selectedOption,
//         _csrf: yii.getCsrfToken()
//     }, function (res) {
//         if (res.success) {
//             updateProgressUI(res);
//         }
//     });
// });
// $('.question-nav-grid button, .btn-next, .btn-back').on('click', function () {
//     let qIndex = $(this).data('index');

//     $.get('/user/mcq/load-question', { index: qIndex }, function (res) {
//         $('#question-container').html(res.questionHtml);
//         updateNavGrid(res);
//     });
// });
// $('.question-nav-grid button, .btn-next, .btn-back').on('click', function () {
//     let qIndex = $(this).data('index');

//     $.get('/exam/load-question', { index: qIndex }, function (res) {
//         $('#question-container').html(res.questionHtml);
//         updateNavGrid(res);
//     });
// });
// function updateProgressUI(data) {
//     $('.progress-bar').css('width', data.percent + '%').text(data.percent + '%');
//     $('.progress-info strong.attempted').text(data.attempted);
//     $('.progress-info strong.flagged').text(data.flagged);
// }
// function updateNavGrid(data) {
//     $('.question-nav-grid').html(data.navHtml);
// }

$('.btn-next').on('click', function () {
    let selectedOption = $('.answer-options input[type="radio"]:checked').val();
    let questionId = $('#question-id').val();
    let sessionId = $sessionIdJson;

    if (!selectedOption) {
        alert('Please select an answer before proceeding.');
        return;
    }

    $.post('$answerUrl', {
        question_id: questionId,
        answer: selectedOption,
        session_id: sessionId,
        _csrf: yii.getCsrfToken()
    }, function (res) {
        if (res.success) {
            $('#question-container').html(res.questionHtml);
            $('.question-nav-grid').html(res.navHtml);
            $('.progress-bar').css('width', res.progress.percent + '%').text(res.progress.percent + '%');
            $('.progress-info strong.attempted').text(res.progress.attempted);
            $('.progress-info strong.flagged').text(res.progress.flagged);
        } else {
            alert(res.message || 'Failed to save answer.');
        }
    });
});

$(function() {
    var timeLeft = parseInt($timeLeft) || 0;
    var \$display = $('#time-display');

    if (!\$display.length) {
        return;
    }

    function updateTimer() {
        if (timeLeft <= 0) {
            \$display.text("00:00");
            clearInterval(timerInterval);
            // Optional: trigger auto-submit or redirect here
            // window.location.href = "YOUR_REDIRECT_URL";
            return;
        }
        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;
        \$display.text(
            ("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2)
        );
        timeLeft--;
    }

    updateTimer(); // initial call
    var timerInterval = setInterval(updateTimer, 1000);
});
JS;
$this->registerJS($js, yii\web\View::POS_END)
    ?>
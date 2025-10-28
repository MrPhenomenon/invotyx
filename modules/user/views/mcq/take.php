<?php
use yii\helpers\Url;
$this->title = 'Exam Inprogress - Part 1';
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
    <div class="px-md-3 py-3 bg-light border-bottom shadow-sm">
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
    <div class="px-md-3 py-3 bg-light border-top shadow-sm">
        <div class="container">
            <?php if ($mode === 'practice'): ?>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger report-btn" type="button" data-bs-toggle="modal" data-bs-target="#reportModal">
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
                           <button class="btn btn-outline-danger report-btn" type="button" data-bs-toggle="modal" data-bs-target="#reportModal">
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

<?php include 'partials/js.php';
include 'partials/report-modal.php';
$this->registerJS($js, yii\web\View::POS_END)
?>
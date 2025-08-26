<?php
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \app\models\PartnerExamAttempts $attempt
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

$originalTitle = 'Exam Review: ' . Html::encode($attempt->partnerExam->title);

$printFileName = '';

$userName = Html::encode($attempt->user_name ?? $attempt->user_email);
$examTitle = Html::encode($attempt->partnerExam->title ?? 'Exam');

$userNameSanitized = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $userName);
$examTitleSanitized = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $examTitle);

$printFileName = $userNameSanitized . '_Result' . $examTitleSanitized;

$this->title = $printFileName;


$answers = $dataProvider->getModels();
?>

<style>
    @media print {

        /* General Print Resets for the entire document */
        body,
        html {
            background-color: #fff !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            margin: 0;
            padding: 0;
            width: 100%;
            height: auto;
            font-size: 9.5pt;
            /* **SLIGHTLY LARGER FONT SIZE for readability** */
            line-height: 1.4;
            /* More comfortable line height */
        }

        /* Set standard page margins */
        @page {
            size: A4 portrait;
            /* Or 'Letter portrait' */
            margin: 30px;
        }

        /* Hide elements that should *not* appear in the printout */
        .navbar,
        .footer,
        .btn,
        .print-button-container,
        .toast-container,
        /* Specific elements from your provided HTML structure that are often part of layout/navigation */
        .container.my-5>.row>.col-12>h1:first-child,
        /* "Exam Review" title */
        .container.my-5>.row>.col-12>p.text-muted.mb-5,
        /* "A detailed review..." subtitle */
        .container.my-5>.row>.col-12>h2.mb-4:last-of-type

        /* "Question Breakdown" heading */
            {
            display: none !important;
        }

        .print-button {
            display: none !important;
        }


        /* Ensure main Bootstrap containers flow correctly for print */
        .container,
        .my-5 {
            max-width: none !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        /* Aggressively reset Bootstrap grid columns to ensure stacking for print */
        .row,
        .col-md-6,
        .col-12 {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: none !important;
            /* Disable flex properties */
        }


        /* --- THE CORE FIX: Prevent individual question cards from breaking --- */
        .card.mb-4 {
            /* This targets your exact question wrapper */
            /* Force avoid breaks within the card */
            break-inside: avoid-page !important;
            /* This is the key property */
            page-break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;

            border: 1px solid #eee !important;
            /* Keep borders visible */
            box-shadow: none !important;
            /* Remove shadows */
            margin-bottom: 15px !important;
            /* **MORE MARGIN** between questions for separation */
            background-color: #fff !important;
            /* Ensure white background */
            position: relative;
            /* Sometimes helps with break-inside on children */
            overflow: hidden;
            /* Prevent content overflow issues with breaks */
        }

        /* Ensure specific parts within the question card also avoid breaking */
        .card.mb-4>.card-header,
        .card.mb-4>.card-body,
        .card.mb-4>.card-footer {
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        .card-header {
            background-color: #f8f8f8 !important;
            border-bottom: 1px solid #eee !important;
            padding: 10px !important;
            margin-bottom: 10px !important;
        }

        .card-body {
            padding-top: 10px !important;
            /* Add space above body content */
            padding-bottom: 10px !important;
        }

        .card-footer {
            padding-top: 15px !important;
            /* Add space above footer content */
            border-top: 1px solid #eee !important;
            /* Clear separator for footer */
            margin-top: 15px !important;
        }

        .card-body p.fw-bold.fs-5 {
            /* Question text */
            font-size: 1em !important;
            /* Restore to a more readable size */
            margin-bottom: 0.8em !important;
            /* More space below question */
        }


        /* Options List Styling */
        ol.list-group {
            list-style-type: upper-alpha !important;
            margin-top: 10px !important;
            margin-bottom: 0 !important;
            border: none !important;
        }

        .list-group-item {
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
            border: 1px solid #f0f0f0 !important;
            padding: 8px 12px !important;
            /* More comfortable padding */
            margin-bottom: 4px !important;
            /* More space between options */
            background-color: transparent !important;
            color: #000 !important;
            font-size: 0.95em !important;
            /* Restore to a more readable size */
        }

        /* Colors for success/danger items */
        .list-group-item-success {
            background-color: #e6ffe6 !important;
            color: #000 !important;
        }

        .list-group-item-danger {
            background-color: #ffe6e6 !important;
            color: #000 !important;
        }

        .list-group-item::before {
            content: none !important;
        }

        img {
            max-width: 100% !important;
            height: 200px !important;
            break-inside: avoid-page;
            page-break-inside: avoid;
            margin-bottom: 10px !important;
        }

        /* Badges (Correct/Incorrect) */
        .badge {
            background-color: transparent !important;
            color: #000 !important;
            border: 1px solid #ccc;
            padding: 4px 8px !important;
            /* More comfortable padding */
            border-radius: 4px;
            font-size: 0.8em !important;
            /* More readable font */
            display: inline-block;
        }

        .badge.bg-success {
            border-color: #28a745 !important;
            color: #28a745 !important;
        }

        .badge.bg-danger {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }


        /* Headings and paragraphs - prevent breaking across pages */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        blockquote {
            orphans: 3;
            widows: 3;
            page-break-after: avoid;
            page-break-before: avoid;
            margin-top: 0.8em !important;
            /* More comfortable margins */
            margin-bottom: 0.8em !important;
        }

        /* Session Summary Card specific rules */
        .session-summary-card {
            break-inside: avoid-page !important;
            page-break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
            box-shadow: none !important;
            border: 1px solid #eee !important;
            margin-bottom: 30px !important;
            /* More margin */
            padding: 20px !important;
            /* More padding */
        }

        .session-summary-card .badge.bg-success {
            border-color: #28a745 !important;
            color: #28a745 !important;
            background-color: #e6ffe6 !important;
        }

        .session-summary-card .row .col-md-6 {
            width: 100% !important;
            float: none !important;
            margin-bottom: 8px !important;
            /* More spacing */
        }

        .session-summary-card .card-title {
            font-size: 1.15em !important;
            margin-bottom: 12px !important;
        }


        /* Fix for Blockquote in Explanation */
        blockquote.blockquote {
            background-color: #f8f8f8 !important;
            border-left: 5px solid #ccc !important;
            padding: 8px 15px !important;
            /* More padding */
            margin: 10px 0 !important;
            font-size: 0.9em !important;
            /* Slightly larger font */
            line-height: 1.4 !important;
        }

        .card-footer.bg-light-subtle {
            background-color: #f5f5f5 !important;
            padding: 10px 15px !important;
            /* More padding */
        }

        .card-footer h6 {
            margin-bottom: 8px !important;
            font-size: 1em !important;
        }

        .card-footer p.text-md {
            font-size: 0.9em !important;
        }
    }

    /* --- END NEW: CSS for Print Media --- */
</style>



<div class="exam-review-view container mt-4">

    <h3><?= Html::encode($originalTitle) ?></h3>
    <p class="text-muted">A detailed review of your exam session completed on
        <?= Yii::$app->formatter->asDatetime($attempt->completed_at) ?>.
    </p>

    <!-- 1. Session Summary Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Session Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Score:</strong> <?= (int) $attempt->correct_answers ?> /
                            <?= (int) $attempt->total_questions ?>
                        </li>
                        <li class="list-group-item"><strong>Accuracy:</strong> <?= round($attempt->score) ?>%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Time Spent:</strong>
                            <?php
                            $timeSpent = strtotime($attempt->completed_at) - strtotime($attempt->started_at);
                            echo Yii::$app->formatter->asDuration($timeSpent);
                            ?>
                        </li>
                        <li class="list-group-item"><strong>Status:</strong> <span
                                class="badge bg-success">Completed</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Question Breakdown Section -->
    <h3 class="mt-5 mb-3">Question Breakdown</h3>

    <?php foreach ($answers as $index => $answer): ?>
        <?php
        $mcq = $answer->partnerMcq;
        if (!$mcq)
            continue;

        $isCorrect = $answer->is_correct;
        ?>
        <div class="card mb-4"> <!-- This is the target for break-inside: avoid-page -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Question <?= $index + 1 ?></h5>
                <?php if ($isCorrect): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Correct</span>
                <?php else: ?>
                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Incorrect</span>
                <?php endif; ?>
            </div>

            <div class="card-body">
                <p class="fw-bold fs-5"><?= nl2br(Html::encode($mcq->question)) ?></p>

                <?php if ($mcq->image_url): ?>
                    <div class="mb-3 text-center">
                        <?= Html::img($mcq->image_url, ['class' => 'img-fluid rounded border', 'style' => 'max-height: 300px;', 'alt' => 'Question Image']) ?>
                    </div>
                <?php endif; ?>

                <!-- Options List -->
                <ol type="A" class="list-group">
                    <?php
                    $options = array_filter([
                        'A' => $mcq->option_a,
                        'B' => $mcq->option_b,
                        'C' => $mcq->option_c,
                        'D' => $mcq->option_d,
                        'E' => $mcq->option_e,
                    ]);

                    foreach ($options as $key => $text):
                        $class = 'list-group-item';
                        $feedbackHtml = '';

                        $isCorrectOption = (strtoupper($key) == strtoupper($mcq->correct_option));
                        $isSelectedOption = (strtoupper($key) == strtoupper($answer->selected_option));

                        if ($isCorrectOption) {
                            $class .= ' list-group-item-success';
                            $feedbackHtml = '<span class="fw-bold"> (Correct Answer)</span>';
                        } elseif ($isSelectedOption) {
                            $class .= ' list-group-item-danger';
                            $feedbackHtml = '<span class="fw-bold"> (Your Answer)</span>';
                        }
                        ?>
                        <li class="<?= $class ?>">
                            <?= Html::encode($text) . $feedbackHtml ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>

            <?php
            if (!empty($mcq->explanation) || !empty($mcq->reference)):
                ?>
                <div class="card-footer bg-light-subtle mt-4 p-3 rounded">
                    <?php if (!empty($mcq->explanation)): ?>
                        <div class="mb-3">
                            <h6 class="text-primary"><i class="bi bi-lightbulb-fill me-1"></i> Explanation</h6>
                            <blockquote class="blockquote text-md mb-0" style="font-size: 0.95rem; line-height: 1.6;">
                                <?= nl2br(Html::encode($mcq->explanation)) ?>
                            </blockquote>
                        </div>
                        <?php if (!empty($mcq->reference)): ?>
                            <hr class="my-3">
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($mcq->reference)): ?>
                        <div>
                            <h6 class="text-secondary"><i class="bi bi-book-fill me-1"></i> Reference</h6>
                            <p class="text-md text-muted mb-0" style="font-size: 0.9rem;">
                                <?= Html::encode($mcq->reference) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

</div>

<?php

$js = <<<JS
    $(document).ready(function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
    JS;
$this->registerJs($js, \yii\web\View::POS_END);

?>
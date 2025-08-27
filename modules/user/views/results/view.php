<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\models\ExamSessions;

/**
 * @var View $this
 * @var ExamSessions $session
 */

$this->title = 'Exam Review: ' . Html::encode(ucfirst($session->mode));
$this->params['breadcrumbs'][] = ['label' => 'My Sessions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .page-item .page-link,
    .prev .page-link,
    .prev.disabled span,
    .next .page-link,
    .page-item a {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #8392ab;
        padding: 0;
        margin: 0 3px;
        margin-left: 3px;
        border-radius: 50% !important;
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }

    .prev.disabled span {
        background-color: #fff;
        border: 1px #dee2e6 solid;
        margin-right: 8px;
    }

    .page-item.active a {
        color: #fff;
    }
</style>
<div class="exam-review-view">

    <h3><?= Html::encode($this->title) ?></h1>
    <p class="">A detailed review of your exam session completed on
        <?= Yii::$app->formatter->asDatetime($session->end_time) ?>.
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
                        <li class="list-group-item"><strong>Score:</strong> <?= (int) $session->correct_count ?> /
                            <?= (int) $session->total_questions ?>
                        </li>
                        <li class="list-group-item"><strong>Accuracy:</strong> <?= round($session->accuracy) ?>%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Time Spent:</strong>
                            <?= Yii::$app->formatter->asDuration($session->time_spent_seconds) ?></li>
                        <li class="list-group-item"><strong>Status:</strong> <span
                                class="badge bg-success"><?= Html::encode($session->status) ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Question Breakdown Section -->
    <h3 class="mt-5 mb-3">Question Breakdown</h2>

    <?php foreach ($interactions as $index => $interaction): ?>
        <?php
        $mcq = $interaction->mcq;
        $isCorrect = $interaction->is_correct;
        ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 lh-base">Question <?= $pagination->offset + $index + 1 ?>: <span class="fw-bold"><?= nl2br(Html::encode($mcq->question_text)) ?></span> </h5>
                <?php if ($isCorrect): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Correct</span>
                <?php else: ?>
                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Incorrect</span>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <?php if ($mcq->image_path): ?>
                    <div class="mb-3 text-center">
                        <?= Html::img(Url::to('@web/path/to/your/images/' . $mcq->image_path), ['class' => 'img-fluid rounded border', 'style' => 'max-height: 300px;', 'alt' => 'Question Image']) ?>
                    </div>
                <?php endif; ?>

                <!-- Options List -->
                <ol type="A" class="list-group">
                    <?php
                    // Create an array of options for easy iteration, skipping empty ones
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

                        // Logic to highlight correct and selected answers
                        $isCorrectOption = ($key == $mcq->correct_option);
                        $isSelectedOption = ($key == $interaction->selected_option);

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

            <!-- Explanation and Reference Footer -->
            <?php if (!empty($mcq->explanation) || !empty($mcq->reference)): ?>
                <div class="card-footer bg-light-subtle">
                    <?php if (!empty($mcq->explanation)): ?>
                        <div>
                            <h6><i class="bi bi-lightbulb-fill me-1"></i> Explanation</h6>
                            <blockquote class="blockquote text-md">
                                <?= nl2br(Html::encode($mcq->explanation)) ?>
                            </blockquote>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($mcq->explanation) && !empty($mcq->reference)): ?>
                        <hr>
                    <?php endif; ?>
                    <?php if (!empty($mcq->reference)): ?>
                        <div>
                            <h6 class="mt-2"><i class="bi bi-book-fill me-1"></i> Reference</h6>
                            <p class="small text-muted mb-0"><?= Html::encode($mcq->reference) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
</div>

<div class="mt-4">
    <?= \yii\widgets\LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination justify-content-center mt-4'],
        'linkOptions' => ['class' => 'page-link'],
        'pageCssClass' => 'page-item',
        'activePageCssClass' => 'active',
        'disabledPageCssClass' => 'disabled',
        'prevPageLabel' => '<i class="bi bi-chevron-left"></i>',
        'nextPageLabel' => '<i class="bi bi-chevron-right"></i>',
    ]) ?>
</div>
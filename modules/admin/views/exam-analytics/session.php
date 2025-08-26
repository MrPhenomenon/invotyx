<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var app\models\ExamSessions $session */
/** @var app\models\UserMcqInteractions[] $interactions */

$this->title = 'Session Details: #' . $session->id;
$this->params['breadcrumbs'][] = ['label' => 'Exam Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exam-analytics-session">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <span class="text-muted">For user: <?= Html::encode($session->user->name) ?></span>
        </div>
        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Back to List', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <div class="row">
        <!-- Left Column: Session Details -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Session Info</h6>
                </div>
                <?= DetailView::widget([
                    'model' => $session,
                    'options' => ['class' => 'table table-bordered detail-view mb-0'],
                    'attributes' => [
                        'id',
                        'user.name:text:User',
                        'examType.name:text:Exam Type',
                        'specialty.name:text:Specialty',
                        'mode',
                        'start_time:datetime',
                        'end_time:datetime',
                    ],
                ]) ?>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                     <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks me-2"></i>Performance Summary</h6>
                </div>
                 <?= DetailView::widget([
                    'model' => $session,
                    'options' => ['class' => 'table table-bordered detail-view mb-0'],
                    'attributes' => [
                        [
                            'label' => 'Score',
                            'format' => 'raw',
                            'value' => "<b>{$session->correct_count}</b> out of <b>{$session->total_questions}</b>"
                        ],
                        [
                            'attribute' => 'accuracy',
                            'format' => 'percent',
                        ],
                        [
                            'attribute' => 'time_spent_seconds',
                            'format' => 'duration',
                            'label' => 'Total Time Spent',
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $statusMap = ['Completed' => 'success', 'Breached' => 'danger', 'In Progress' => 'info', 'Abandoned' => 'secondary'];
                                $class = $statusMap[$model->status] ?? 'secondary';
                                return "<span class='badge bg-{$class} fs-6'>" . Html::encode($model->status) . "</span>";
                            }
                        ],
                        [
                            'attribute' => 'breaches',
                            'format' => 'raw',
                            'value' => fn($model) => $model->breaches > 0 ? "<span class='badge bg-danger fs-6'>{$model->breaches}</span>" : '<span class="text-muted">0</span>',
                        ],
                    ],
                ]) ?>
            </div>
        </div>

        <!-- Right Column: Question Breakdown -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                 <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol me-2"></i>Question Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="vertical-align: middle;">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 5%;">#</th>
                                    <th scope="col" style="width: 45%;">Question</th>
                                    <th scope="col" style="width: 25%;">User Answer</th>
                                    <th scope="col" class="text-center" style="width: 15%;">Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($interactions as $index => $interaction): ?>
                                    <tr class="<?= $interaction->is_correct ? '' : 'table-danger' ?>">
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td>
                                            <?= Html::encode(StringHelper::truncateWords($interaction->mcq->question_text ?? 'N/A', 15)) ?>
                                            <div class="small text-muted">Topic: <?= Html::encode($interaction->mcq->topic->name ?? 'Uncategorized') ?></div>
                                        </td>
                                        <td>
                                            <span class="fw-bold"><?= Html::encode($interaction->selected_option) ?></span>
                                            <?php if (!$interaction->is_correct): ?>
                                                <div class="small text-success">Correct: <?= Html::encode($interaction->mcq->correct_option) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($interaction->is_correct): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Correct</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Incorrect</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
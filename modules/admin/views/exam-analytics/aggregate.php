<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $metrics */
/** @var array $topTopics */

$this->title = 'Aggregate Exam Analytics';
$this->params['breadcrumbs'][] = ['label' => 'Exam Analytics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// --- A small CSS block for custom, sleek UI elements ---
$this->registerCss("
    .icon-circle {
        height: 3rem;
        width: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .bg-light-primary { background-color: rgba(59, 130, 246, 0.1); }
    .bg-light-success { background-color: rgba(22, 163, 74, 0.1); }
    .bg-light-warning { background-color: rgba(245, 158, 11, 0.1); }
    .bg-light-danger { background-color: rgba(220, 38, 38, 0.1); }
");
?>
<div class="exam-analytics-aggregate">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="fas fa-arrow-left me-2"></i>Back to Sessions', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <!-- Aggregate Metric Cards with Icons -->
    <div class="row mb-4 g-4">
        <!-- Total Sessions Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-primary text-primary me-3">
                        <i class="fas fa-layer-group fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Total Sessions</div>
                        <div class="h3 fw-bold mb-0"><?= Yii::$app->formatter->asInteger($metrics['total_sessions']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Accuracy Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-success text-success me-3">
                        <i class="fas fa-bullseye fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Average Accuracy</div>
                        <div class="h3 fw-bold mb-0"><?= round($metrics['avg_accuracy'], 2) ?>%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Time / Question Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-warning text-warning me-3">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Avg Time / Question</div>
                        <div class="h3 fw-bold mb-0"><?= round($metrics['avg_time_per_question'], 1) ?>s</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Breaches Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-danger text-danger me-3">
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Total Breaches</div>
                        <div class="h3 fw-bold mb-0"><?= Yii::$app->formatter->asInteger($metrics['total_breaches']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Topic Analysis Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 font-weight-bold text-primary">Weakest Topics Analysis</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" style="vertical-align: middle;">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Topic Name</th>
                            <th scope="col" class="text-center">Total Attempts</th>
                            <th scope="col" style="width: 35%;">Average Accuracy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topTopics as $topic): ?>
                            <?php
                            // Logic for dynamic progress bar color
                            $accuracy_percent = $topic['avg_accuracy'] * 100;
                            $progress_color = 'bg-danger';
                            if ($accuracy_percent >= 70) {
                                $progress_color = 'bg-success';
                            } elseif ($accuracy_percent >= 50) {
                                $progress_color = 'bg-warning';
                            }
                            ?>
                            <tr>
                                <td class="fw-bold">
                                    <?= Html::encode($topic['topic_name']) ?>
                                </td>
                                <td class="text-center"><?= Yii::$app->formatter->asInteger($topic['count']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-3"
                                            style="width: 50px;"><?= round($accuracy_percent, 1) ?>%</span>
                                        <div class="progress flex-grow-1" style="height: 10px;">
                                            <div class="progress-bar <?= $progress_color ?>" role="progressbar"
                                                style="width: <?= $accuracy_percent ?>%;"
                                                aria-valuenow="<?= $accuracy_percent ?>" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($topTopics)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No topic data available for the selected
                                    period.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-muted small">
            This table highlights topics where users perform the worst on average, helping to identify difficult
            material.
        </div>
    </div>

</div>
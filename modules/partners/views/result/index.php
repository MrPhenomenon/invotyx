<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \app\models\PartnerExams $exam
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var int $totalAttempts
 * @var float $averageScore
 * @var float $highestScore
 */

$this->title = 'Exam Results: ' . Html::encode($exam->title);
$this->params['breadcrumbs'][] = ['label' => 'My Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Results';
?>

<div class="exam-view-results">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="lead text-muted">An overview of all completed attempts for this exam.</p>

    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-1 text-primary"></i>
                    <h3 class="card-title mt-2"><?= $totalAttempts ?></h3>
                    <p class="card-text text-muted">Total Attempts</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-bar-chart-line-fill fs-1 text-warning"></i>
                    <h3 class="card-title mt-2"><?= round($averageScore, 1) ?>%</h3>
                    <p class="card-text text-muted">Average Score</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-trophy-fill fs-1 text-success"></i>
                    <h3 class="card-title mt-2"><?= round($highestScore, 1) ?>%</h3>
                    <p class="card-text text-muted">Highest Score</p>
                </div>
            </div>
        </div>
    </div>

    <!-- GridView for Detailed Attempts -->
    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'user_name',
                    'user_email:email',
                    'user_hospital',
                    'completed_at:datetime',
                    [
                        'attribute' => 'score',
                        'label' => 'Score (%)',
                        'format' => 'raw',
                        'value' => function ($model) {
                                    
                                        $percentage = 0;
                                        if ($model->total_questions > 0) {
                                            $percentage = round(($model->score / $model->total_questions) * 100);
                                        }

                                        $class = 'bg-danger';
                                        if ($percentage >= 75) {
                                            $class = 'bg-success';
                                        } elseif ($percentage >= 50) {
                                            $class = 'bg-warning';
                                        }

                                        return "<div class='progress' style='height: 20px;'>
                    <div class='progress-bar {$class}' role='progressbar' style='width: {$percentage}%;' aria-valuenow='{$percentage}' aria-valuemin='0' aria-valuemax='100'>
                        <strong>{$percentage}%</strong>
                    </div>
                </div>";
                                    }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view-details} {print-result}',
                        'header' => 'Actions',
                        'buttons' => [
                            'view-details' => function ($url, $model, $key) {
                                            return Html::a(
                                                '<i class="bi bi-search"></i> View Details',
                                                Url::to(['/user/orthopedic-exam/result', 'attempt' => $model->id]),
                                                [
                                                    'class' => 'btn btn-sm btn-outline-primary me-1',
                                                    'title' => 'View Detailed Review',
                                                    'target' => '_blank',
                                                ]
                                            );
                                        },
                            'print-result' => function ($url, $model, $key) {
                                            return Html::a(
                                                '<i class="bi bi-printer"></i> Print Result',
                                                Url::to(['print-result', 'attempt_id' => $model->id, 'access' => Yii::$app->request->get('access')]),
                                                [
                                                    'class' => 'btn btn-sm btn-outline-info',
                                                    'title' => 'Print Exam Result',
                                                    'target' => '_blank',
                                                ]
                                            );
                                        },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
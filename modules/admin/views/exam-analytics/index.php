<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $filters */
/** @var array $users */

$this->title = 'Exam Sessions Analytics';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exam-analytics-index">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="fas fa-chart-pie me-2"></i>View Aggregate Stats', ['aggregate'], ['class' => 'btn btn-primary shadow-sm']) ?>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Sessions</h6>
        </div>
        <div class="card-body">
            <?= Html::beginForm(['index'], 'get', ['data-pjax' => 0]); ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <?= Html::label('From Date', 'from', ['class' => 'form-label']) ?>
                    <?= Html::input('date', 'from', $filters['from'], ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::label('To Date', 'to', ['class' => 'form-label']) ?>
                    <?= Html::input('date', 'to', $filters['to'], ['class' => 'form-control']) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::label('User', 'user_id', ['class' => 'form-label']) ?>
                    <?= Html::dropDownList('user_id', $filters['user_id'], $users, ['prompt' => 'All Users', 'class' => 'form-control']) ?>
                    <?php // For better UX with many users, consider replacing with: \kartik\select2\Select2::widget([...]) ?>
                </div>
                <div class="col-md-3">
                    <?= Html::label('Mode', 'mode', ['class' => 'form-label']) ?>
                    <?= Html::dropDownList('mode', $filters['mode'], ['Timed' => 'Timed', 'Tutor' => 'Tutor', 'Mock' => 'Mock'], ['prompt' => 'All Modes', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-12 text-end mt-3">
                    <?= Html::submitButton('<i class="fas fa-search me-2"></i>Apply Filter', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="fas fa-undo me-2"></i>Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
            <?= Html::endForm(); ?>
        </div>
    </div>

    <!-- Data Grid Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-2"></i>Session Records</h6>
        </div>
        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'sessions-grid']); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions' => ['class' => 'table table-hover mb-0'],
                'layout' => "{items}\n<div class='card-footer bg-white'>{summary}\n{pager}</div>",
                'columns' => [
                    'id',
                    'user.name:text:User',
                    'examType.name:text:Exam Type',
                    'mode',
                    [
                        'label' => 'Score',
                        'format' => 'raw',
                        'value' => fn($model) => "<b>{$model->correct_count}</b> <small class='text-muted'>/ {$model->total_questions}</small>"
                    ],
                    [
                        'attribute' => 'accuracy',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->accuracy === null) return '<span class="text-muted">N/A</span>';
                            $percent = $model->accuracy;
                            $color = $percent < 50 ? 'danger' : ($percent >= 75 ? 'success' : 'warning');
                            return "<span class='text-{$color} fw-bold'>" . round($percent, 2) . "%</span>";
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $statusMap = [
                                'Completed' => 'success',
                                'Breached' => 'danger',
                                'In Progress' => 'info',
                                'Abandoned' => 'secondary'
                            ];
                            $class = $statusMap[$model->status] ?? 'secondary';
                            return "<span class='badge bg-{$class}'>" . Html::encode($model->status) . "</span>";
                        }
                    ],
                    [
                        'attribute' => 'breaches',
                        'format' => 'raw',
                        'value' => fn($model) => $model->breaches > 0 ? "<span class='badge bg-danger rounded-pill'>{$model->breaches}</span>" : '<span class="text-muted">0</span>',
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    'start_time:datetime',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<i class="fas fa-eye"></i>', ['session', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-secondary',
                                    'title' => 'View Session Details',
                                    'data-pjax' => '0'
                                ]);
                            },
                        ],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
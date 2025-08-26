<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var \app\models\PartnerExams $exam */
/** @var \app\models\PartnerMcqs[] $mcqs */

$this->title = 'Manage MCQs';
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($exam->title), 'url' => ['view', 'id' => $exam->id]];
$this->params['breadcrumbs'][] = $this->title;

$accessParams = ['access' => Yii::$app->request->get('access')];
?>

<div class="mcq-manage-index">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">For exam: <strong><?= Html::encode($exam->title) ?></strong></p>
        </div>
        <?= Html::a(
            '<i class="bi bi-plus-lg me-1"></i> Bulk Add MCQs',
            array_merge(['exam/add-mcqs', 'exam_id' => $exam->id], $accessParams),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-list-ol me-2"></i>
                Total Questions: <?= count($mcqs) ?>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($mcqs)): ?>
                <div class="text-center p-5">
                    <i class="bi bi-journal-x" style="font-size: 3rem; color: #6c757d;"></i>
                    <h4 class="mt-3">No MCQs Found</h4>
                    <p class="text-muted">Get started by adding questions to this exam.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 40%;">Question</th>
                                <th scope="col" style="width: 30%;">Correct Answer</th>
                                <th scope="col" style="width: 10%;">Has Image</th>
                                <th scope="col" style="width: 15%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mcqs as $index => $mcq): ?>
                                <tr id="mcq-row-<?= $mcq->id ?>">
                                    <th scope="row"><?= $index + 1 ?></th>
                                    <td><?= Html::encode($mcq->question) ?></td>

                                    <td>
                                        <?php
                                        // Dynamically get the text of the correct option
                                        $correctOptionKey = 'option_' . strtolower($mcq->correct_option);
                                        $correctAnswerText = $mcq->$correctOptionKey;
                                        ?>
                                        <span
                                            class="badge bg-primary rounded-pill me-2"><?= Html::encode(strtoupper($mcq->correct_option)) ?></span>
                                        <?= Html::encode($correctAnswerText) ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($mcq->image_url)): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?= Html::a('<i class="bi bi-pencil-square"></i> Edit', array_merge(['exam/update-mcq', 'id' => $mcq->id], $accessParams), [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'Edit MCQ'
                                        ]) ?>
                                        <?= Html::button('<i class="bi bi-trash"></i> Delete', [
                                            'class' => 'btn btn-sm btn-outline-danger ms-1 btn-delete',
                                            'data-id' => $mcq->id,
                                            'data-url' => Url::to(array_merge(['exam/delete-mcq'], $accessParams)),
                                            'data-item' => 'Question #' . ($index + 1),
                                            'title' => 'Delete MCQ'
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
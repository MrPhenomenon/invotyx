<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var \app\models\PartnerExams[] $exams */

$this->title = 'My Exams';
$this->params['breadcrumbs'][] = $this->title;

$accessParams = ['access' => Yii::$app->request->get('access')];
?>

<div class="exam-index">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        <?= Html::a(
            '<i class="bi bi-plus-lg me-1"></i> Create New Exam',
            array_merge(['exam/create'], $accessParams),
            ['class' => 'btn btn-primary shadow-sm']
        ) ?>
    </div>

    <?php if (empty($exams)): ?>
        <!-- Engaging Empty State -->
        <div class="text-center p-5 bg-light rounded border">
            <i class="bi bi-journal-plus" style="font-size: 4rem; color: #6c757d;"></i>
            <h4 class="mt-3">You haven't created any exams yet.</h4>
            <p class="text-muted">Click the "Create New Exam" button to get started.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($exams as $exam): ?>
                <div class="col-md-6 col-lg-4 mb-4" id="exam-card-<?= $exam->id ?>">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-truncate" title="<?= Html::encode($exam->title) ?>">
                                <i class="bi bi-journal-text me-2 text-primary"></i>
                                <?= Html::encode($exam->title) ?>
                            </h5>
                            <span class="badge bg-<?= $exam->is_active ? 'success' : 'secondary' ?>">
                                <?= $exam->is_active ? 'Active' : 'Inactive' ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-around text-center mb-3">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= count($exam->partnerMcqs) ?></h6>
                                    <small class="text-muted">Questions</small>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= Yii::$app->formatter->asDate($exam->created_at, 'medium') ?>
                                    </h6>
                                    <small class="text-muted">Created</small>
                                </div>
                            </div>
                            <div>
                                <label class="form-label small fw-bold">Shareable Link</label>
                                <?php
                                $shareableLink = Url::to(['/user/orthopedic-exam/start-exam', 'id' => $exam->id], true);
                                ?>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm"
                                        value="<?= Html::encode($shareableLink) ?>" readonly>
                                    <button class="btn btn-sm btn-outline-secondary btn-copy"
                                        data-copy-text="<?= Html::encode($shareableLink) ?>" title="Copy Shareable Link"
                                        <?= empty($exam->id) ? 'disabled' : '' ?>>
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- ==================== END OF NEW SECTION =================== -->
                        </div>
                        <div class="card-footer bg-light text-center">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <?= Html::a('<i class="bi bi-list-ol me-1"></i> MCQs', array_merge(['exam/manage-mcqs', 'exam_id' => $exam->id], $accessParams), ['class' => 'btn btn-sm btn-primary']) ?>

                                    <?= Html::a(
                                        '<i class="bi bi-download"></i> Student List', 
                                        array_merge(['/partners/exam/export-access-list', 'id' => $exam->id], $accessParams),
                                        ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank']) 
                                    ?>

                                    <?= Html::a(
                                        '<i class="bi bi-bar-chart-line me-1"></i> Results',
                                        array_merge(['result/index', 'exam_id' => $exam->id], $accessParams),
                                        ['class' => 'btn btn-sm btn-outline-success']
                                    ) ?>

                                </div>
                                <div>
                                    <?= Html::a('<i class="bi bi-pencil"></i>', array_merge(['exam/update-exam', 'id' => $exam->id], $accessParams), ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Edit Exam']) ?>
                                    <?= Html::button('<i class="bi bi-trash"></i>', [
                                        'class' => 'btn btn-outline-danger btn-sm btn-delete',
                                        'data-id' => $exam->id,
                                        'data-url' => Url::to(array_merge(['exam/delete-exam'], $accessParams)),
                                        'data-item' => Html::encode($exam->title) . '. This will delete all MCQs for this exam',
                                        'title' => 'Delete Exam'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php

$js = <<<'JS'

$('.btn-copy').on('click', function() {
    const button = $(this);
    const textToCopy = button.data('copy-text');
    navigator.clipboard.writeText(textToCopy).then(() => {
        const originalIcon = button.html();
        button.html('<i class="bi bi-check-lg text-success"></i>');
        setTimeout(() => {
            button.html(originalIcon);
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
});
JS;
$this->registerJs($js, View::POS_READY);
?>
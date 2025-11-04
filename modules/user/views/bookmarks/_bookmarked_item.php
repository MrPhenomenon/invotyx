<?php
use yii\helpers\Html;
use yii\helpers\Url;

$bookmarkCreatedAt = 'N/A';
if (isset($model->userBookmarks) && !empty($model->userBookmarks)) {
    $bookmarkRecords = array_filter($model->userBookmarks, fn($ubm) => $ubm->user_id == Yii::$app->user->id);
    if (!empty($bookmarkRecords)) {
        $bookmarkRecord = current($bookmarkRecords);
        $bookmarkCreatedAt = Yii::$app->formatter->asDate($bookmarkRecord->created_at, 'dd-MM-yyyy');

    }
}


$optionLetters = ['A', 'B', 'C', 'D', 'E'];
?>

<div class="card mb-4 bookmarked-item-card">

    <div class="card-header d-flex justify-content-between align-items-center flex-wrap py-3 px-4">
        <div
            class="d-flex flex-wrap justify-content-between align-items-start gap-2 w-100 mt-2 mt-sm-0 border-bottom pb-3 mb-2">
            <h5 class="fw-bold text-primary mb-0 flex-shrink-0">
                Question <?= $index + 1 ?>:
            </h5>

            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                <span class="badge bg-light text-dark border fw-normal px-3 py-2 rounded-pill shadow-sm"
                    style="font-size:12px">
                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                    Bookmarked: <span class="fw-semibold"><?= $bookmarkCreatedAt ?></span>
                </span>

                <button
                    class="btn btn-outline-primary btn-sm btn-bookmarked-status btn-unbookmark px-3 rounded-pill d-flex align-items-center shadow-sm"
                    type="button" data-mcq-id="<?= $model->id ?>">
                    <i class="fas fa-bookmark me-1"></i>
                    <span>Remove Bookmark</span>
                </button>

                <button class="btn btn-outline-success btn-sm px-3 rounded-pill d-flex align-items-center shadow-sm"
                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $index ?>"
                    aria-expanded="false" aria-controls="collapse-<?= $index ?>">
                    <i class="fas fa-chevron-down me-1"></i> Full MCQ
                </button>
            </div>
        </div>

        <h5 class="me-auto lh-base">
            <span style="font-size: 15px;"><?= nl2br(Html::encode($model->question_text)) ?></span>
        </h5>
    </div>

    <div id="collapse-<?= $index ?>" class="collapse">
        <div class="card-body">
            <?php if ($model->image_path): ?>
                <div class="mb-3 text-center">
                    <?= Html::img(Url::to('@web/path/to/your/images/' . $model->image_path), [
                        'class' => 'img-fluid rounded border',
                        'style' => 'max-height: 300px;',
                        'alt' => 'Question Image'
                    ]) ?>
                </div>
            <?php endif; ?>

            <ol type="A" class="list-group">
                <?php
                $options = array_filter([
                    'A' => $model->option_a,
                    'B' => $model->option_b,
                    'C' => $model->option_c,
                    'D' => $model->option_d,
                    'E' => $model->option_e,
                ]);
                foreach ($options as $key => $text):
                    $class = 'list-group-item';
                    $feedbackHtml = '';
                    if ($key == $model->correct_option) {
                        $class .= ' list-group-item-success';
                        $feedbackHtml = '<span class="fw-bold"> (Correct Answer)</span>';
                    }
                    ?>
                    <li class="<?= $class ?>">
                        <?= Html::encode($text) . $feedbackHtml ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>

        <?php if (!empty($model->explanation) || !empty($model->reference)): ?>
            <div class="card-footer bg-light-subtle">
                <?php if (!empty($model->explanation)): ?>
                    <div>
                        <h6><i class="bi bi-lightbulb-fill me-1"></i> Explanation</h6>
                        <blockquote class="blockquote text-md">
                            <?= nl2br(Html::encode($model->explanation)) ?>
                        </blockquote>
                    </div>
                <?php endif; ?>
                <?php if (!empty($model->explanation) && !empty($model->reference)): ?>
                    <hr>
                <?php endif; ?>
                <?php if (!empty($model->reference)): ?>
                    <div>
                        <h6 class="mt-2"><i class="bi bi-book-fill me-1"></i> Reference</h6>
                        <p class="small text-muted mb-0"><?= Html::encode($model->reference) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
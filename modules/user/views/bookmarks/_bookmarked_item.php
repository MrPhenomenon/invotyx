<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Mcqs */ // This is the full MCQ model
/* @var $key mixed */
/* @var $index int */
/* @var $widget yii\widgets\ListView */
/* @var $userDefaultExamType string */ // From parent view
/* @var $userDefaultSpecialty string */ // From parent view

// Determine bookmark creation date for display
$bookmarkCreatedAt = 'N/A';
if (isset($model->userBookmarks) && !empty($model->userBookmarks)) {
    $bookmarkRecords = array_filter($model->userBookmarks, fn($ubm) => $ubm->user_id == Yii::$app->user->id);
    if (!empty($bookmarkRecords)) {
        $bookmarkRecord = current($bookmarkRecords);
        $bookmarkCreatedAt = Yii::$app->formatter->asDate($bookmarkRecord->created_at, 'dd-MM-yyyy');

    }
}

// Option letters for display consistency
$optionLetters = ['A', 'B', 'C', 'D', 'E'];
?>

<div class="card mb-4 bookmarked-item-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap py-3 px-4">
        <div class="d-flex justify-content-between w-100 align-items-center mt-2 mt-sm-0">
            <h5>Question <?= $index + 1 ?>:</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
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
            </div>

        </div>
        <h5 class="mt-3 me-auto lh-base">
            <span class="fw-bold"><?= nl2br(Html::encode($model->question_text)) ?></span>
        </h5>
    </div>

    <div class="card-body">
        <!-- ... (rest of card-body content: image, options) ... -->

        <?php if ($model->image_path): // Assuming image_path exists in Mcqs model ?>
            <div class="mb-3 text-center">
                <?= Html::img(Url::to('@web/path/to/your/images/' . $model->image_path), ['class' => 'img-fluid rounded border', 'style' => 'max-height: 300px;', 'alt' => 'Question Image']) ?>
            </div>
        <?php endif; ?>

        <!-- Options List -->
        <ol type="A" class="list-group">
            <?php
            $options = array_filter([ // Filter out empty options
                'A' => $model->option_a,
                'B' => $model->option_b,
                'C' => $model->option_c,
                'D' => $model->option_d,
                'E' => $model->option_e,
            ]);

            foreach ($options as $key => $text):
                $class = 'list-group-item';
                $feedbackHtml = '';

                $isCorrectOption = ($key == $model->correct_option);

                if ($isCorrectOption) {
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

    <!-- Explanation and Reference Footer -->
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
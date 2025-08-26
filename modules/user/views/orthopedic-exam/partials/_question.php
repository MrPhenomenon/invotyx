<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $mcq app\models\PartnerMcqs */
/* @var $mode string */ // Keeping as per your original code
/* @var $actualQuestionNumber int */ // The original 1-based question number
/* @var $isRevisiting bool */ // To determine if "Skipped" prefix is needed

$hasImage = !empty($mcq->image_url);
?>

<input type="hidden" id="question-id" value="<?= $mcq->id ?>">
<p class="text-muted">
    <?php if ($isRevisiting): ?>
        Skipped <span id="current-question-number-skipped">Question <?= $actualQuestionNumber ?></span>
    <?php else: ?>
        Question <span id="current-question-number-regular"><?= $actualQuestionNumber ?></span>
    <?php endif; ?>
</p>
<p class="fs-4 fw-medium" id="question-text"><?= Html::encode($mcq->question) ?></p>

<div class="row mt-4  align-items-center">
    <p class="text-muted">Answer</p>
    <div class="<?= $hasImage ? 'col-lg-8' : 'col-lg-12' ?>">
        <div id="options-container" class="list-group answer-options">
            <?php foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $i => $key):
                $optionLetter = strtoupper(chr(65 + $i));
                ?>
                <?php if (!empty($mcq->$key)): // Only render if option text is not empty ?>
                    <label class="list-group-item d-flex align-items-start">
                        <input class="form-check-input me-3 mt-1" type="radio" name="answer" value="<?= $optionLetter ?>">
                        <div><?= Html::encode($mcq->$key) ?></div>
                    </label>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if ($hasImage): ?>
        <div class="col-lg-4">
            <div class="text-center">
                <?php
                // Wrap the image in a link to trigger the modal
                echo Html::a(
                    Html::img($mcq->image_url, [
                        'class' => 'img-fluid rounded border shadow-sm',
                        'style' => 'max-height: 250px;', // Keep the thumbnail size reasonable
                        'alt' => 'Question Image (Click to enlarge)'
                    ]),
                    '#', // The href is not needed
                    [
                        'class' => 'image-enlarge-trigger',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#imagePreviewModal', // This targets our modal
                        'data-image-url' => $mcq->image_url, // Pass the full image URL to the modal
                    ]
                );
                ?>
                <small class="d-block text-muted mt-2">Click image to enlarge</small>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php

use yii\helpers\Html;
/** @var \app\models\Mcqs $mcq */
/** @var int $total */
?>

<input type="hidden" id="question-id" value="<?= $mcq->id ?>">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex gap-2">
        <button class="btn btn-sm fw-bold"><i class="bi bi-flag-fill"></i> Flag (NF)</button>
        <button class="btn btn-sm fw-bold"><i class="bi bi-info-circle-fill"></i> Report (NF)</button>
    </div>
</div>

<div class="mb-3 overflow-auto px-1 bg-light rounded-2 py-3 px-3" style="height: 182px;">
    <p class="fs-5 fw-semibold"><?= $mcq->question_text ?></p>
</div>

<div class="mt-4">
    <h6 class="mb-3">Answer</h6>
    <div class="list-group answer-options">
        <?php foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $i => $key):
            $optionLetter = strtoupper(chr(65 + $i));
            ?>
            <label class="list-group-item d-flex align-items-start">
                <input class="form-check-input me-2 mt-1" type="radio" name="answer" value="<?= $optionLetter ?>">
                <?= Html::encode($mcq->$key) ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($mode === 'practice'): ?>
    <input type="hidden" id="correct-answer" value="<?= Html::encode($mcq->correct_option) ?>">
    <div class="explanation-card mt-4 p-3 bg-white border-start border-5 border-primary rounded shadow-sm d-none">
        <h6 class="text-primary mb-2"><i class="bi bi-lightbulb-fill me-1"></i> Explanation</h6>
        <p class="mb-2"><?= nl2br(Html::encode($mcq->explanation)) ?></p>


        <?php if (!empty($mcq->reference)): ?>
            <small class="text-muted">Reference: <?= Html::encode($mcq->reference) ?></small>
        <?php endif; ?>
    </div>
<?php endif; ?>
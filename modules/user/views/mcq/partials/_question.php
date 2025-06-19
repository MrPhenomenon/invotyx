<?php

use yii\helpers\Html;
/** @var \app\models\Mcqs $mcq */
/** @var int $index */
/** @var int $total */
?>

<input type="hidden" id="question-id" value="<?= $mcq->id ?>">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="mb-0">Question No: <?= $index + 1 ?> of <?= $total ?></h6>
    <div class="d-flex gap-2">
        <button class="btn btn-sm fw-bold"><i class="bi bi-flag-fill"></i> Flag</button>
        <button class="btn btn-sm fw-bold"><i class="bi bi-info-circle-fill"></i> Report</button>
    </div>
</div>

<div class="mb-3 overflow-auto px-1 bg-light rounded-2 py-3 px-3" style="height: 182px;">
    <p class="fs-5 fw-semibold"><?= $mcq->question_text ?></p>
</div>

<div class="mt-4">
    <h6 class="mb-3">Answer</h6>
    <div class="list-group answer-options">
        <?php foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $key): ?>
            <label class="list-group-item d-flex align-items-start">
                <input class="form-check-input me-2 mt-1" type="radio" name="answer" value="<?= $key ?>">
                <?= Html::encode($mcq->$key) ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>

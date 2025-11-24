<?php
use yii\helpers\Html;
?>

<input type="hidden" id="question-id" value="<?= $mcq->id ?>">
<p class="text-muted">
    <?php if ($isRevisiting): ?>
        Skipped <span id="current-question-number-skipped">Question <?= $actualQuestionNumber ?></span>
    <?php else: ?>
        Question <span id="current-question-number-regular"><?= $actualQuestionNumber ?></span>
    <?php endif; ?>
</p>
<p class="fs-5 fw-medium" id="question-text"><?= Html::encode($mcq->question_text) ?></p>

<div class="row mt-4  align-items-center">
    <p class="text-muted">Answer</p>
    <div class="col-lg-12">
        <div id="options-container" class="list-group answer-options">
            <?php foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $i => $key):
                $optionLetter = strtoupper(chr(65 + $i));
                ?>
                <?php if (!empty($mcq->$key)):?>
                    <label class="list-group-item d-flex align-items-start">
                        <input class="form-check-input me-3 mt-1" type="radio" name="answer" value="<?= $optionLetter ?>">
                        <div><?= Html::encode($mcq->$key) ?></div>
                    </label>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
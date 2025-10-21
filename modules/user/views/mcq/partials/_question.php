<?php use yii\helpers\Html; ?>

<input type="hidden" id="question-id" value="<?= $mcq->id ?>">
<input type="hidden" id="report-id" value="<?= $mcq->question_id ?>">
<input type="hidden" id="correct-answer" value="<?= Html::encode($mcq->correct_option) ?>">
<!-- Hidden field for current question index within its phase, useful for some JS logic if needed -->
<input type="hidden" id="current-phase-index" value="<?= $currentPhaseIndex ?>">
<input type="hidden" id="total-questions-in-phase" value="<?= $totalQuestionsInPhase ?>">
<input type="hidden" id="is-revisiting-skipped" value="<?= $isRevisitingSkipped ? '1' : '0' ?>">


<p class="text-muted">
    <?php if ($isRevisitingSkipped): ?>
        Skipped Question <span id="current-question-overall-number" class="fw-semibold text-primary"><?= $overallQuestionNumber ?></span>
        <span class="badge bg-warning text-dark ms-2">Revisiting (<?= $currentPhaseIndex ?> of <?= $totalQuestionsInPhase ?>)</span>
    <?php else: ?>
        Question <span id="current-question-overall-number" class="fw-semibold text-primary"><?= $overallQuestionNumber ?></span>
        <span class="badge bg-secondary ms-2"><?= $currentPhaseIndex ?> of <?= $totalQuestionsInPhase ?></span>
    <?php endif; ?>
</p>
<p class="fs-4 fw-medium" id="question-text"><?= Html::encode($mcq->question_text) ?></p>

<?php if ($mcq->tags): ?>
    <p class="text-muted">
        <span class="badge bg-primary"><?= Html::encode($mcq->tags) ?></span>
    </p>
<?php endif; ?>

<div class="mt-4">
    <p class="text-muted">Answer</p>
    <div id="options-container" class="list-group answer-options mb-5">
        <?php foreach (['option_a', 'option_b', 'option_c', 'option_d', 'option_e'] as $i => $key):
            $optionLetter = strtoupper(chr(65 + $i));
            $isSelected = (isset($selectedOption) && $selectedOption == $optionLetter) ? 'checked' : '';
            $isActive = (isset($selectedOption) && $selectedOption == $optionLetter) ? 'active' : '';
            // In practice mode, if already answered, options should be disabled initially
            $isDisabled = ($mode === 'practice' && isset($selectedOption)) ? 'disabled' : '';
            ?>
            <label class="list-group-item d-flex align-items-start <?= $isActive ?>">
                <input class="form-check-input me-3 mt-1" type="radio" name="answer" value="<?= $optionLetter ?>" <?= $isSelected ?> <?= $isDisabled ?>>
                <div><?= Html::encode($mcq->$key) ?></div>
            </label>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($mode === 'practice'): ?>
    <!-- Accordion Style Explanation/Reference -->
    <div class="accordion explanation-card <?= isset($selectedOption) ? '' : 'd-none' ?>" id="detailsAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                    <i class="fas fa-info-circle me-2"></i> Explanation
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne">
                <div class="accordion-body" id="explanation-text">
                    <?= nl2br(Html::encode($mcq->explanation)) ?>
                </div>
            </div>
        </div>
        <?php if (!empty($mcq->reference)): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                        <i class="fas fa-book-open me-2"></i> Reference
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                    <div class="accordion-body" id="reference-text">
                        <?= Html::encode($mcq->reference) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
    // If in practice mode and already selected an option, show feedback and next button
    if (isset($selectedOption)):
        $js = <<<JS
        $(document).ready(function() {
            // Disable options
            $('.answer-options input[type="radio"]').prop('disabled', true);
            // Show correct/incorrect feedback
            $('.answer-options input[type="radio"]').each(function () {
                const label = $(this).closest('label');
                const val = $(this).val();
                const correct = $('#correct-answer').val();
                if (val === correct) {
                    label.addClass('bg-success text-white');
                }
                if (val === '{$selectedOption}' && val !== correct) {
                    label.addClass('bg-danger text-white');
                }
            });
            // Show feedback section and toggle buttons
            $('.explanation-card').removeClass('d-none');
            $('.btn-submit-answer').addClass('d-none');
            $('.btn-next').removeClass('d-none');
        });
        JS;
        $this->registerJS($js, yii\web\View::POS_END);
    endif;
    ?>
<?php endif; ?>
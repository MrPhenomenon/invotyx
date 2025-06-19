<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Start New Exam';
$this->registerCssFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', [
    'depends' => [\yii\bootstrap5\BootstrapAsset::class],
]);
?>

<div
    class="                                                                                                                                                                                                                                           ">
    <div class="card shadow-sm">
        <div class="card-header ">
            <h5 class="mb-0">Start a New Exam</h5>
        </div>
        <div class="card-body">
            <?= Html::beginForm([Url::to(['exam/start-exam'])], 'post', ['id' => 'startExamForm']) ?>

            <?= Html::csrfMetaTags() ?>
            <div class="mb-3">
                <?= Html::label('Exam Type', null, ['class' => 'form-label d-block']) ?>
                <div class="" role="group" aria-label="Exam Type">
                    <?= Html::radio('examtype', true, ['value' => 'practice', 'class' => 'btn-check', 'id' => 'practice', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Practice Mode', 'practice', ['class' => 'btn btn-outline-primary']) ?>

                    <?= Html::radio('examtype', false, ['value' => 'exam', 'class' => 'btn-check', 'id' => 'exam', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Exam Mode', 'exam', ['class' => 'btn btn-outline-primary']) ?>

                </div>
            </div>

            <div class="mb-3">
                <label for="chapters" class="form-label">Select Chapters</label>
                <select id="chapterSelect" name="chapter_ids[]" multiple>
                    <?php foreach ($chapters as $chapter): ?>
                        <option value="<?= $chapter->id ?>"><?= Html::encode($chapter->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="chapters" class="form-label">Select Topic</label>
                <select id="topicSelect" name="topic_ids[]" multiple
                    data-url="<?= Url::to(['exam/get-topics']) ?>">
                    <option value="0" selected>All topics for selected chapters</option>
                </select>
            </div>

            <div class="mb-3">
                <?= Html::label('Number of Questions', 'numQuestions', ['class' => 'form-label']) ?>
                <?= Html::input('number', 'question_count', 10, [
                    'class' => 'form-control',
                    'id' => 'numQuestions',
                    'min' => '10',
                    'max' => '200',
                    'style' => 'max-width: 120px;'
                ]) ?>
            </div>

            <div class="mb-3">
                <?= Html::label('Difficulty Level', null, ['class' => 'form-label d-block']) ?>
                <div class="" role="group" aria-label="Difficulty Level">
                    <?= Html::radio('difficulty', false, ['value' => '1', 'class' => 'btn-check', 'id' => 'difficultyEasy', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Easy', 'difficultyEasy', ['class' => 'btn btn-outline-primary']) ?>

                    <?= Html::radio('difficulty', true, ['value' => '2', 'class' => 'btn-check', 'id' => 'difficultyMedium', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Medium', 'difficultyMedium', ['class' => 'btn btn-outline-primary']) ?>
                    <!-- Bootstrap CSS will style this as active due to :checked -->

                    <?= Html::radio('difficulty', false, ['value' => '3', 'class' => 'btn-check', 'id' => 'difficultyHard', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Hard', 'difficultyHard', ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </div>

            <div class="mb-3">
                <?= Html::label('Tags (optional)', null, ['class' => 'form-label d-block']) ?>
                <div class="d-flex flex-wrap">
                    <div class="form-check me-3">
                        <?= Html::checkbox('tags[]', false, ['value' => 'unseen', 'class' => 'form-check-input', 'id' => 'unseen']) ?>
                        <?= Html::label('Unseen', 'unseen', ['class' => 'form-check-label']) ?>
                    </div>
                    <div class="form-check me-3">
                        <?= Html::checkbox('tags[]', false, ['value' => 'attemptedWrong ', 'class' => 'form-check-input', 'id' => 'attemptedWrong']) ?>
                        <?= Html::label('Attempted Wrong ', 'attemptedWrong', ['class' => 'form-check-label']) ?>
                    </div>
                    <div class="form-check">
                        <?= Html::checkbox('tags[]', false, ['value' => 'previously-asked', 'class' => 'form-check-input', 'id' => 'tagPreviouslyAsked']) ?>
                        <?= Html::label('Previously Asked', 'tagPreviouslyAsked', ['class' => 'form-check-label']) ?>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div>
                    <label for="time_limit" class="form-label d-block">Time Limit</label>

                    <!-- Untimed toggle -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="untimedToggle" name="untimed" value="1"
                            onchange="toggleTimer(this)">
                        <label class="form-check-label" for="untimedToggle">Untimed</label>
                    </div>
                    <div class="row" id="timerControls">
                        <div class="col-10  align-items-center">
                            <input type="range" class="form-range" min="10" max="180" step="10" id="time_limit"
                                name="time_limit" value="60"
                                oninput="document.getElementById('timeDisplay').textContent = this.value + ' min'">
                        </div>
                        <div class="col-2">
                            <div class="text-primary fw-bold fs-5" id="timeDisplay">60 min</div>
                        </div>
                    </div>
                    <!-- Timer slider -->

                </div>
            </div>

            <!-- Randomize Questions -->
            <div class="form-check ps-0 form-switch mb-3 d-flex justify-content-between align-items-center">
                <label class="form-check-label d-flex justify-content-between w-100" for="randomizeQuestionsSwitch">
                    <span style="font-size: 0.95rem; color: #212529;">Randomize Questions</span>
                    <?= Html::checkbox('randomize_questions', false, [
                        'class' => 'form-check-input',
                        'id' => 'randomizeQuestionsSwitch',
                        'role' => 'switch'
                    ]) ?>
                </label>
            </div>

            <!-- Include Bookmarked -->
            <div class="form-check ps-0 form-switch mb-4 d-flex justify-content-between align-items-center">
                <label class="form-check-label d-flex justify-content-between w-100" for="includeBookmarkedSwitch">
                    <span style="font-size: 0.95rem; color: #212529;">Include Bookmarked</span>
                    <?= Html::checkbox('include_bookmarked', false, [
                        'class' => 'form-check-input',
                        'id' => 'includeBookmarkedSwitch',
                        'role' => 'switch'
                    ]) ?>
                </label>
            </div>


            <div class="mt-4 text-end">
                <?= Html::submitButton('Start Practice Session', [
                    'class' => 'btn btn-primary py-2 fw-medium',
                ]) ?>
            </div>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);

$js = <<<JS
function toggleTimer(checkbox) {
        const slider = document.getElementById('time_limit');
        const display = document.getElementById('timeDisplay');
        if (checkbox.checked) {
            slider.disabled = true;
            display.textContent = 'Untimed';
        } else {
            slider.disabled = false;
            display.textContent = slider.value + ' min';
        }
    }

new Choices('#chapterSelect', {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select chapters',
     noChoicesText: 'No other chapters available'
});

const topicSelect = new Choices('#topicSelect', {
    removeItemButton: true,
    placeholderValue: 'Select topics',
    noChoicesText: 'Select chapters to see topics'
});

let selectedChapters = [];
let topicsLoaded = false;

$('#chapterSelect').on('change', function () {
    selectedChapters = $(this).val() || [];
    topicsLoaded = false;

    topicSelect.clearStore();
    topicSelect.setChoices([{ value: '', label: 'Select chapters to see topics', disabled: true }], 'value', 'label', true);
});

// Use Choices-specific event instead of 'focus'
document.getElementById('topicSelect').addEventListener('showDropdown', function () {
    if (topicsLoaded || !selectedChapters.length) return;

    console.log('Loading topics...');
    topicSelect.clearStore();
    topicSelect.setChoices([{ value: '', label: 'Loading topics...', disabled: true }], 'value', 'label', true);

    $.ajax({
        url: \$(this).data('url'),
        type: 'GET',
        data: { chapter_ids: selectedChapters },
        success: function (data) {
            const choices = [{ value: '0', label: 'All topics for selected chapters', selected: true }]
                .concat(data.map(topic => ({ value: topic.id, label: topic.name })));
            topicSelect.setChoices(choices, 'value', 'label', true);
            topicsLoaded = true;
        },
        error: function () {
            topicSelect.setChoices([{ value: '', label: 'Failed to load topics', disabled: true }], 'value', 'label', true);
        }
    });
});

// Handle default "All topics" logic
document.getElementById('topicSelect').addEventListener('change', function () {
    const selected = topicSelect.getValue();
    const selectedNonZero = selected.filter(item => item.value !== '0');

    if (selected.length > 1 && selected.some(item => item.value === '0')) {
        topicSelect.removeActiveItemsByValue('0');
    }

    if (selectedNonZero.length === 0) {
        topicSelect.setChoiceByValue('0');
    }
});
JS;

$this->registerJS($js, \yii\web\View::POS_END)
    ?>
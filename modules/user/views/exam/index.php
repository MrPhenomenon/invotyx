<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Chapter; // Ensure these are properly used if needed in the view directly
use common\models\OrganSystem;
use common\models\Subject;

$this->title = 'Start New Exam';
$this->registerCssFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', [
    'depends' => [\yii\bootstrap5\BootstrapAsset::class],
]);
?>

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Start a New Exam</h5>
        </div>
        <div class="card-body">
            <?= Html::beginForm('exam/start-exam', 'post', ['id' => 'startExamForm']) ?>
            <?= Html::csrfMetaTags() ?>

            <div class="mb-3">
                <?= Html::label('Exam Type', null, ['class' => 'form-label d-block fw-semibold']) ?>
                <div class="btn-group" role="group" aria-label="Exam Type">
                    <?= Html::radio('examtype', true, ['value' => 'practice', 'class' => 'btn-check', 'id' => 'practice', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Practice Mode', 'practice', ['class' => 'btn btn-outline-primary']) ?>

                    <?= Html::radio('examtype', false, ['value' => 'test', 'class' => 'btn-check', 'id' => 'test', 'autocomplete' => 'off']) ?>
                    <?= Html::label('Test Mode', 'test', ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </div>

            <div id="examModeMessage" class="alert alert-info d-none mb-3"></div>

            <div class="mb-3">
                <?= Html::label('Exam Scope', null, ['class' => 'form-label d-block fw-semibold']) ?>
                <div class="btn-group" role="group" aria-label="Exam Scope">
                    <?= Html::radio('exam_scope', true, ['value' => 'subject_chapter_topic', 'class' => 'btn-check', 'id' => 'scopeSubjectChapterTopic', 'autocomplete' => 'off']) ?>
                    <?= Html::label('By Subject/Chapter/Topic', 'scopeSubjectChapterTopic', ['class' => 'btn btn-outline-primary']) ?>

                    <?= Html::radio('exam_scope', false, ['value' => 'organ_system', 'class' => 'btn-check', 'id' => 'scopeOrganSystem', 'autocomplete' => 'off']) ?>
                    <?= Html::label('By Organ System', 'scopeOrganSystem', ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </div>

            <div id="scopeSubjectChapterTopicFields" class="mt-3">
                <div class="mb-3">
                    <label for="subjectSelect" class="form-label fw-semibold">Select Subjects</label>
                    <select id="subjectSelect" name="subject_ids[]" multiple>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject->id ?>"><?= Html::encode($subject->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="chapterSelect" class="form-label fw-semibold">Select Chapters</label>
                    <select id="chapterSelect" name="chapter_ids[]" multiple>
                        <?php foreach ($chapters as $chapter): ?>
                            <option value="<?= $chapter->id ?>"><?= Html::encode($chapter->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="topicSelect" class="form-label fw-semibold">Select Topics</label>
                    <select id="topicSelect" name="topic_ids[]" multiple data-url="<?= Url::to(['exam/get-topics']) ?>">
                        <option value="0" selected>All topics for selected chapters</option>
                    </select>
                </div>
            </div>

            <div id="scopeOrganSystemFields" class="mt-3 d-none">
                <div class="mb-3">
                    <label for="organSystemSelect" class="form-label fw-semibold">Select Organ Systems</label>
                    <select id="organSystemSelect" name="organ_system_ids[]" multiple>
                        <?php foreach ($organSystems as $organSystem): ?>
                            <option value="<?= $organSystem->id ?>"><?= Html::encode($organSystem->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Common customization fields (Number of Questions, Difficulty, Tags, Time Limit, Randomize, Bookmarked) -->
            <div id="commonCustomizationSection">
                <div class="mb-3">
                    <?= Html::label('Number of Questions', 'numQuestions', ['class' => 'form-label fw-semibold']) ?>
                    <?= Html::input('number', 'question_count', 10, [
                        'class' => 'form-control',
                        'id' => 'numQuestions',
                        'min' => '10',
                        'max' => '200',
                        'style' => 'max-width: 120px;'
                    ]) ?>
                </div>

                <div class="mb-3">
                    <?= Html::label('Difficulty Level', null, ['class' => 'form-label d-block fw-semibold']) ?>
                    <div class="btn-group" role="group" aria-label="Difficulty Level">
                        <?= Html::radio('difficulty', false, ['value' => 'Easy', 'class' => 'btn-check', 'id' => 'difficultyEasy', 'autocomplete' => 'off']) ?>
                        <?= Html::label('Easy', 'difficultyEasy', ['class' => 'btn btn-outline-primary']) ?>

                        <?= Html::radio('difficulty', true, ['value' => 'Moderate', 'class' => 'btn-check', 'id' => 'difficultyMedium', 'autocomplete' => 'off']) ?>
                        <?= Html::label('Moderate', 'difficultyMedium', ['class' => 'btn btn-outline-primary']) ?>

                        <?= Html::radio('difficulty', false, ['value' => 'Hard', 'class' => 'btn-check', 'id' => 'difficultyHard', 'autocomplete' => 'off']) ?>
                        <?= Html::label('Hard', 'difficultyHard', ['class' => 'btn btn-outline-primary']) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <?= Html::label('Filter by Tags (optional)', null, ['class' => 'form-label d-block mb-3 fw-semibold']) ?>
                    <div class="d-flex flex-wrap gap-4 p-3 bg-light rounded-3 shadow-sm"> <!-- Grouped in a light box with gap -->
                        <div class="form-check form-check-inline"> <!-- Inline for better layout within group -->
                            <?= Html::checkbox('tags[]', false, ['value' => 'unseen', 'class' => 'form-check-input', 'id' => 'unseen']) ?>
                            <?= Html::label('Unseen Questions', 'unseen', ['class' => 'form-check-label text-dark fw-medium']) ?>
                        </div>
                        <div class="form-check form-check-inline">
                            <?= Html::checkbox('tags[]', false, ['value' => 'attemptedWrong', 'class' => 'form-check-input', 'id' => 'attemptedWrong']) ?>
                            <?= Html::label('Answered Incorrectly', 'attemptedWrong', ['class' => 'form-check-label text-dark fw-medium']) ?>
                        </div>
                        <div class="form-check form-check-inline">
                            <?= Html::checkbox('tags[]', false, ['value' => 'previously-asked', 'class' => 'form-check-input', 'id' => 'tagPreviouslyAsked']) ?>
                            <?= Html::label('From Past Exams', 'tagPreviouslyAsked', ['class' => 'form-check-label text-dark fw-medium']) ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4" id="timeSection">
                    <label for="time_limit" class="form-label d-block mb-3 fw-semibold">Time Limit</label>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input form-switch-lg" type="checkbox" id="untimedToggle" name="untimed" value="1"
                            onchange="toggleTimer(this)">
                        <label class="form-check-label fw-medium" for="untimedToggle">Untimed Exam</label>
                    </div>
                    <div class="row g-3 align-items-center" id="timerControls">
                        <div class="col-9">
                            <input type="range" class="form-range" min="10" max="180" step="10" id="time_limit"
                                name="time_limit" value="60"
                                oninput="document.getElementById('timeDisplay').textContent = this.value + ' min'">
                        </div>
                        <div class="col-3 text-end">
                            <div class="text-primary fw-bold" id="timeDisplay">60 min</div> <!-- Slightly larger time display -->
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 shadow-sm"> <!-- Integrated box for toggle -->
                    <label class="form-check-label fw-semibold text-dark flex-grow-1 me-3" for="randomizeQuestionsSwitch">
                        Randomize Questions Order
                        <span class="d-block text-muted fw-normal small">Shuffle questions to prevent predictability.</span>
                    </label>
                    <?= Html::checkbox('randomize_questions', false, [
                        'class' => 'form-check-input form-switch-lg',
                        'id' => 'randomizeQuestionsSwitch',
                        'role' => 'switch'
                    ]) ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 shadow-sm"> <!-- Integrated box for toggle -->
                    <label class="form-check-label fw-semibold text-dark flex-grow-1 me-3" for="includeBookmarkedSwitch">
                        Include Bookmarked Questions
                        <span class="d-block text-muted fw-normal small">Add questions you've previously bookmarked.</span>
                    </label>
                    <?= Html::checkbox('include_bookmarked', false, [
                        'class' => 'form-check-input form-switch-lg',
                        'id' => 'includeBookmarkedSwitch',
                        'role' => 'switch'
                    ]) ?>
                </div>
            </div> <!-- /commonCustomizationSection -->

            <div class="mt-4 text-end">
                <button type="button" class="btn btn-primary py-2 fw-medium" id="startExamBtn">
                    Start Exam
                </button>
            </div>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>

<?php include "rulesModals.php" ?>
<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);

$js = <<<JS

$('#startExamBtn').on('click', function () {
    const selectedExamType = $('input[name="examtype"]:checked').val();
    // Assuming you have 'rulesModals.php' with modals like #practiceModal, #testModal
    switch (selectedExamType) {
        case 'practice':
            $('#practiceModal').modal('show');
            break;
        case 'test':
            $('#testModal').modal('show');
            break;
        default:
            // This should ideally not happen if only practice/test are options
            showToast('Please select an exam type (Practice or Test).', 'warning');
    }
});

$('.confirm-start').on('click', function () {
    $('#startExamForm').submit();
});

function updateUIBasedOnExamType(selectedType) {
    const messageBox = $('#examModeMessage');
    const startBtn = $('#startExamBtn');

    messageBox.addClass('d-none').text('');

    if (selectedType === 'practice') {
        startBtn.text('Start Practice Session');
        messageBox.removeClass('d-none').html('<i class="bi bi-info-circle-fill me-2"></i> Practice Mode: Immediate feedback on each question, including the correct answer and explanation. Fully customizable.');
    } else if (selectedType === 'test') {
        startBtn.text('Start Test Session');
        messageBox.removeClass('d-none').html('<i class="bi bi-info-circle-fill me-2"></i> Test Mode: Feedback is shown at the end. Fully customizable.');
    }
}

function updateUIBasedOnExamScope(selectedScope) {
    const subjectChapterTopicFields = $('#scopeSubjectChapterTopicFields');
    const organSystemFields = $('#scopeOrganSystemFields');

    if (selectedScope === 'subject_chapter_topic') {
        subjectChapterTopicFields.removeClass('d-none');
        organSystemFields.addClass('d-none');
        // Enable fields for Subject/Chapter/Topic scope
        subjectSelectChoice.enable();
        chapterSelectChoice.enable();
        topicSelectChoice.enable();
        organSystemSelectChoice.disable(); // Disable Organ System fields
    } else if (selectedScope === 'organ_system') {
        subjectChapterTopicFields.addClass('d-none');
        organSystemFields.removeClass('d-none');
        // Enable fields for Organ System scope
        organSystemSelectChoice.enable();
        subjectSelectChoice.disable(); // Disable Subject/Chapter/Topic fields
        chapterSelectChoice.disable();
        topicSelectChoice.disable();
    }
}

// Attach change listener for Exam Type
$('input[name="examtype"]').on('change', function () {
    updateUIBasedOnExamType($(this).val());
});

// Attach change listener for Exam Scope
$('input[name="exam_scope"]').on('change', function () {
    updateUIBasedOnExamScope($(this).val());
});

// Initial UI setup on load
$(document).ready(function () {
    updateUIBasedOnExamType($('input[name="examtype"]:checked').val());
    updateUIBasedOnExamScope($('input[name="exam_scope"]:checked').val());
});

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

// Initialize Choices.js for all select elements
const subjectSelectChoice = new Choices('#subjectSelect', {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select subjects',
    noChoicesText: 'No subjects available'
});

const chapterSelectChoice = new Choices('#chapterSelect', {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select chapters',
    noChoicesText: 'No chapters available'
});

const topicSelectChoice = new Choices('#topicSelect', {
    removeItemButton: true,
    placeholderValue: 'Select topics',
    noChoicesText: 'Select chapters to see topics'
});

const organSystemSelectChoice = new Choices('#organSystemSelect', {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select organ systems',
    noChoicesText: 'No organ systems available'
});

let selectedChaptersForTopics = [];
let topicsLoaded = false;

// Event listener for chapter selection (to load topics)
$('#chapterSelect').on('change', function () {
    selectedChaptersForTopics = $(this).val() || [];
    topicsLoaded = false; // Reset flag to force reload

    topicSelectChoice.clearStore();
    topicSelectChoice.setChoices([{ value: '', label: 'Select chapters to see topics', disabled: true }], 'value', 'label', true);

    if (selectedChaptersForTopics.length === 0) {
        topicSelectChoice.setChoices([{ value: '0', label: 'All topics for selected chapters', selected: true }], 'value', 'label', true);
        return;
    }

    console.log('Loading topics...');
    topicSelectChoice.clearStore();
    topicSelectChoice.setChoices([{ value: '', label: 'Loading topics...', disabled: true }], 'value', 'label', true);

    $.ajax({
        url: $('#topicSelect').data('url'), // Ensure the data-url attribute is on topicSelect
        type: 'GET',
        data: { chapter_ids: selectedChaptersForTopics },
        success: function (data) {
            const choices = [{ value: '0', label: 'All topics for selected chapters', selected: true }]
                .concat(data.map(topic => ({ value: topic.id, label: topic.name })));
            topicSelectChoice.setChoices(choices, 'value', 'label', true);
            topicsLoaded = true;
        },
        error: function () {
            topicSelectChoice.setChoices([{ value: '', label: 'Failed to load topics', disabled: true }], 'value', 'label', true);
        }
    });
});

// Handle default "All topics" logic (if 0 is selected, unselect others)
document.getElementById('topicSelect').addEventListener('change', function () {
    const selected = topicSelectChoice.getValue();
    const hasAllTopicsSelected = selected.some(item => item.value === '0');

    if (hasAllTopicsSelected && selected.length > 1) {
        // If "All topics" is selected along with others, remove the others
        topicSelectChoice.removeActiveItemsByValue(selected.filter(item => item.value !== '0').map(item => item.value));
        // Ensure "All topics" remains selected
        if (!topicSelectChoice.getValue(true).includes('0')) {
             topicSelectChoice.setChoiceByValue('0');
        }
    } else if (!hasAllTopicsSelected && selected.length === 0) {
        // If nothing is selected, default to "All topics"
        topicSelectChoice.setChoiceByValue('0');
    }
});
JS;

$this->registerJS($js, \yii\web\View::POS_END);
?>

<?php
$js = '';
foreach (Yii::$app->session->getAllFlashes() as $type => $message) {
    $js .= "showToast(" . json_encode($message) . ", " . json_encode($type) . ");\n";
}
if ($js) {
    $this->registerJs($js, \yii\web\View::POS_READY);
}
?>
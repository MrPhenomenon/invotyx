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
                            <option value="<?= $subject->id ?>"><?= Html::encode($subject->name) ?>
                                (<?= $subject->mcq_count ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="chapterSelect" class="form-label fw-semibold">Select Chapters</label>
                    <select id="chapterSelect" name="chapter_ids[]" multiple>
                        <option value="0" selected>All Chapters</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="topicSelect" class="form-label fw-semibold">Select Topics</label>
                    <select id="topicSelect" name="topic_ids[]" multiple data-url="<?= Url::to(['/api/get-topics']) ?>">
                        <option value="0" selected>All topics</option>
                    </select>
                </div>
            </div>

            <div id="scopeOrganSystemFields" class="mt-3 d-none">
                <div class="mb-3">
                    <label for="organSystemSelect" class="form-label fw-semibold">Select Organ Systems</label>
                    <select id="organSystemSelect" name="organ_system_ids[]" multiple>
                        <?php foreach ($organSystems as $organSystem): ?>
                            <option value="<?= $organSystem->id ?>"><?= Html::encode($organSystem->name) ?>
                                (<?= $organSystem->mcq_count ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

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
                    <?= Html::label('Difficulty Level', null, ['class' => 'form-label fw-semibold']) ?>
                    <select id="difficulty-select" multiple name="difficulty[]" class="form-select">
                        <option value="0" selected>All difficulties</option>
                    </select>
                </div>


                <div class="mb-4">
                    <?= Html::label('Filter by Tags (optional)', null, ['class' => 'form-label d-block mb-3 fw-semibold']) ?>
                    <div class="d-flex flex-wrap gap-4 p-3 bg-light rounded-3 shadow-sm">
                        <div class="form-check form-check-inline">
                            <?= Html::checkbox('tags[]', false, ['value' => 'attemptedWrong', 'class' => 'form-check-input', 'id' => 'attemptedWrong']) ?>
                            <?= Html::label('Answered Incorrectly', 'attemptedWrong', ['class' => 'form-check-label text-dark fw-medium']) ?>
                        </div>
                        <div class="form-check form-check-inline">
                            <?= Html::checkbox('tags[]', false, ['value' => 'previously-asked', 'class' => 'form-check-input', 'id' => 'tagPreviouslyAsked']) ?>
                            <?= Html::label('Previously Attempted', 'tagPreviouslyAsked', ['class' => 'form-check-label text-dark fw-medium']) ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4" id="timeSection">
                    <label for="time_limit" class="form-label d-block mb-3 fw-semibold">Time Limit</label>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input form-switch-lg" type="checkbox" id="untimedToggle" name="untimed"
                            value="1" onchange="toggleTimer(this)">
                        <label class="form-check-label fw-medium" for="untimedToggle">Untimed Exam</label>
                    </div>
                    <div class="row g-3 align-items-center" id="timerControls">
                        <div class="col-9">
                            <input type="range" class="form-range" min="10" max="180" step="10" id="time_limit"
                                name="time_limit" value="60"
                                oninput="document.getElementById('timeDisplay').textContent = this.value + ' min'">
                        </div>
                        <div class="col-3 text-end">
                            <div class="text-primary fw-bold" id="timeDisplay">60 min</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3 shadow-sm">
                    <label class="form-check-label fw-semibold text-dark flex-grow-1 me-3"
                        for="randomizeQuestionsSwitch">
                        Randomize Questions Order
                        <span class="d-block text-muted fw-normal small">Shuffle questions to prevent
                            predictability.</span>
                    </label>
                    <?= Html::checkbox('randomize_questions', false, [
                        'class' => 'form-check-input form-switch-lg',
                        'id' => 'randomizeQuestionsSwitch',
                        'role' => 'switch'
                    ]) ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 shadow-sm">
                    <label class="form-check-label fw-semibold text-dark flex-grow-1 me-3"
                        for="includeBookmarkedSwitch">
                        Include Bookmarked Questions
                        <span class="d-block text-muted fw-normal small">Add questions you've previously
                            bookmarked.</span>
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
    switch (selectedExamType) {
        case 'practice':
            $('#practiceModal').modal('show');
            break;
        case 'test':
            $('#testModal').modal('show');
            break;
        default:
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

const difficultyChoices = new Choices('#difficulty-select', {
    removeItemButton: true,
    placeholderValue: 'Select difficulties',
    searchEnabled: false,
    shouldSort: false,
});

difficultyChoices.setChoices([
    { value: 'Easy', label: 'Easy' },
    { value: 'Moderate', label: 'Moderate' },
    { value: 'Hard', label: 'Hard' },
], 'value', 'label', true);

let selectedChaptersForTopics = [];
let topicsLoaded = false;


$('#chapterSelect').on('change', function () {
    selectedChaptersForTopics = $(this).val() || [];
    const selectedSubjects = $('#subjectSelect').val() || [];
    topicsLoaded = false;

    topicSelectChoice.clearStore();
    topicSelectChoice.setChoices([{ value: '', label: 'Select chapters to see topics', disabled: true }], 'value', 'label', true);

    if (selectedChaptersForTopics.length === 0) {
        topicSelectChoice.setChoices([{ value: '0', label: 'All topics for selected chapters', selected: true }], 'value', 'label', true);
        return;
    }

    console.log('Loading topics...');
    topicSelectChoice.setChoices([{ value: '', label: 'Loading topics...', disabled: true }], 'value', 'label', true);

    $.ajax({
        url: $('#topicSelect').data('url'),
        type: 'POST',
        data: {
            chapter_ids: selectedChaptersForTopics,
            subject_ids: selectedSubjects
        },
        success: function (data) {
            const choices = [{ value: '0', label: 'All topics for selected chapters', selected: true }]
                .concat(data.map(topic => ({ value: topic.id, label: topic.name + ' (' + topic.mcq_count + ')'})));
            topicSelectChoice.setChoices(choices, 'value', 'label', true);
            topicsLoaded = true;
            updateDifficultyCounts();
        },
        error: function () {
            topicSelectChoice.setChoices([{ value: '', label: 'Failed to load topics', disabled: true }], 'value', 'label', true);
        }
    });
});

$('#subjectSelect').on('change', function () {
    const selectedSubjects = $(this).val() || [];

    // Reset chapters + topics
    chapterSelectChoice.clearStore();
    topicSelectChoice.clearStore();

    chapterSelectChoice.setChoices([{ value: '', label: 'Select subjects to see chapters', disabled: true }], 'value', 'label', true);
    topicSelectChoice.setChoices([{ value: '', label: 'Select chapters to see topics', disabled: true }], 'value', 'label', true);

    if (selectedSubjects.length === 0) {
        return;
    }

    console.log('Loading chapters...');
    chapterSelectChoice.setChoices([{ value: '', label: 'Loading chapters...', disabled: true }], 'value', 'label', true);

    $.ajax({
        url: '/api/get-chapters',
        type: 'POST',
        data: { subject_ids: selectedSubjects },
        success: function (data) {
            const choices = data.map(chap => ({ value: chap.id, label: chap.name + ' (' + chap.mcq_count + ')' }));
            chapterSelectChoice.setChoices(choices, 'value', 'label', true);
            updateDifficultyCounts();
        },
        error: function () {
            chapterSelectChoice.setChoices([{ value: '', label: 'Failed to load chapters', disabled: true }], 'value', 'label', true);
        }
    });
});


document.getElementById('topicSelect').addEventListener('change', function () {
    const selected = topicSelectChoice.getValue();
    const hasAllTopicsSelected = selected.some(item => item.value === '0');
    updateDifficultyCounts();
    if (hasAllTopicsSelected && selected.length > 1) {
        topicSelectChoice.removeActiveItemsByValue(selected.filter(item => item.value !== '0').map(item => item.value));
        if (!topicSelectChoice.getValue(true).includes('0')) {
             topicSelectChoice.setChoiceByValue('0');
        }
    } else if (!hasAllTopicsSelected && selected.length === 0) {
        topicSelectChoice.setChoiceByValue('0');
    }
});
document.getElementById('chapterSelect').addEventListener('change', function () {
    const selected = chapterSelectChoice.getValue();
    const hasAllChaptersSelected = selected.some(item => item.value === '0');

    if (hasAllChaptersSelected && selected.length > 1) {
        hasAllChaptersSelected.removeActiveItemsByValue(selected.filter(item => item.value !== '0').map(item => item.value));
        if (!hasAllChaptersSelected.getValue(true).includes('0')) {
             hasAllChaptersSelected.setChoiceByValue('0');
        }
    } else if (!hasAllChaptersSelected && selected.length === 0) {
        hasAllChaptersSelected.setChoiceByValue('0');
    }
});

function updateDifficultyCounts() {
    var selectedSubjects = $('#subjectSelect').val() || [];
    var selectedTopics = $('#topicSelect').val() || [];
    var selectedChapters = $('#chapterSelect').val() || [];
    $.ajax({
        url: '/api/get-difficulty-counts',
        type: 'POST',
        data: { 
            subject_ids: selectedSubjects,
            chapter_ids: selectedChapters,
            topic_ids: selectedTopics
        },
        success: function (counts) {
            const diffChoices = [
                { value: 'all', label: `All Difficulties (\${counts.all || 0})` },
                { value: 'Easy', label: `Easy (\${counts.Easy || 0})` },
                { value: 'Moderate', label: `Moderate (\${counts.Moderate || 0})` },
                { value: 'Hard', label: `Hard (\${counts.Hard || 0})` }
            ];
            difficultyChoices.setChoices(diffChoices, 'value', 'label', true);
        }
    });
}
    
JS;

$this->registerJS($js, \yii\web\View::POS_END);
?>
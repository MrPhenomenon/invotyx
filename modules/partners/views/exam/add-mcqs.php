<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var \app\models\Exam $exam */

$this->title = 'Bulk Add MCQs';
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($exam->title), 'url' => ['view', 'id' => $exam->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .image-preview-container {
        position: relative;
        display: none;
        /* Hidden by default */
        max-width: 200px;
        margin-top: 10px;
    }

    .image-preview {
        max-width: 100%;
        height: auto;
        border-radius: .375rem;
        border: 1px solid #dee2e6;
    }

    .btn-remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        line-height: 1;
        padding: 4px 6px;
    }
</style>

<div class="mcq-bulk-add">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">For exam: <?= Html::encode($exam->title) ?></p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="mcq-bulk-form" method="post" enctype="multipart/form-data">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>

                <div class="accordion" id="mcq-accordion">
                    <div class="accordion-item mcq-block">
                        <h2 class="accordion-header" id="heading-0">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse-0" aria-expanded="true" aria-controls="collapse-0">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <span class="mcq-title">Question #1 </span>
                                    <div class="btn btn-sm btn-outline-danger btn-remove-mcq me-3"
                                        title="Remove Question">
                                        <i class="bi bi-trash"></i>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-0" class="accordion-collapse collapse show" aria-labelledby="heading-0"
                            data-bs-parent="#mcq-accordion">
                            <div class="accordion-body">
                                <?= $this->render('_mcq_form_fields', ['index' => 0]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-outline-secondary" id="add-mcq-btn">
                        <i class="bi bi-plus-circle-dotted me-1"></i> Add Another Question
                    </button>
                </div>
        </div>
        <div class="card-footer text-end">
            <a href="<?= Url::to(['view', 'id' => $exam->id]) ?>" class="btn btn-link">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg" id="save-all-btn">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                    style="display: none;"></span>
                Save All Questions
            </button>
        </div>
        </form>
    </div>
</div>


<div id="mcq-template" style="display: none;">
    <div class="accordion-item mcq-block">
        <h2 class="accordion-header" id="heading-__INDEX__">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse-__INDEX__" aria-expanded="false" aria-controls="collapse-__INDEX__">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <span class="mcq-title">Question</span>
                    <div type="button" class="btn btn-sm btn-outline-danger btn-remove-mcq me-3"
                        title="Remove Question">
                        <i class="bi bi-trash"></i>
                    </div>
                </div>
            </button>
        </h2>
        <div id="collapse-__INDEX__" class="accordion-collapse collapse" aria-labelledby="heading-__INDEX__"
            data-bs-parent="#mcq-accordion">
            <div class="accordion-body">
                <?= $this->render('_mcq_form_fields', ['index' => '__INDEX__']) ?>
            </div>
        </div>
    </div>
</div>


<?php
$js = <<<JS
let mcqIndex = 1;

// --- Helper function to re-number all visible MCQ blocks ---
function renumberMcqBlocks() {
    $('#mcq-accordion .mcq-block').each(function(i) {
        $(this).find('.mcq-title').text(`Question #\${i + 1}`);
    });
}

// --- Add a new MCQ block ---
$('#add-mcq-btn').on('click', function () {
    // Get the template HTML and replace the placeholder index
    let template = $('#mcq-template').html().replace(/__INDEX__/g, mcqIndex);
    
    // Append the new block to the accordion
    let newBlock = $(template).appendTo('#mcq-accordion');
    
    // Show the new accordion item
    new bootstrap.Collapse(newBlock.find('.accordion-collapse'), {
      toggle: true
    });

    mcqIndex++;
    renumberMcqBlocks();
    
    // Scroll to the new block
    $('html, body').animate({
        scrollTop: newBlock.offset().top - 70
    }, 500);
});

// --- Remove an MCQ block (using event delegation) ---
$('#mcq-accordion').on('click', '.btn-remove-mcq', function (e) {
    e.stopPropagation(); // Prevent accordion from toggling
    
    if ($('#mcq-accordion .mcq-block').length <= 1) {
        alert("You cannot remove the last question.");
        return;
    }
    
    if (confirm("Are you sure you want to remove this question?")) {
        $(this).closest('.mcq-block').remove();
        renumberMcqBlocks();
    }
});

// --- Image Preview Logic (using event delegation) ---
$('#mcq-accordion').on('change', '.image-upload-input', function() {
    const input = this;
    const container = $(this).closest('.image-upload-wrapper').find('.image-preview-container');
    const preview = container.find('.image-preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.attr('src', e.target.result);
            container.slideDown(200);
        };
        reader.readAsDataURL(input.files[0]);
    }
});

$('#mcq-accordion').on('click', '.btn-remove-image', function() {
    const wrapper = $(this).closest('.image-upload-wrapper');
    const input = wrapper.find('.image-upload-input');
    const container = wrapper.find('.image-preview-container');

    input.val(''); // Clear the file input
    container.slideUp(200);
});

// --- Form Submission UX ---
$('#mcq-bulk-form').on('submit', function () {
    $('.accordion-collapse:not(.show)').each(function () {
        new bootstrap.Collapse(this, { toggle: true });
    });

    const saveBtn = $('#save-all-btn');
    saveBtn.prop('disabled', true);
    saveBtn.find('.spinner-border').show();
    saveBtn.contents().filter(function(){ return this.nodeType === 3; }).last().replaceWith(" Saving...");
});

$('#mcq-accordion').on('click', '.btn-parse-mcq', function () {
    const block = \$(this).closest('.mcq-block');
    const text = block.find('.full-mcq-input').val();

    const questionMatch = text.match(/Question:\\s*(.+?)(?=\\n[A-E]\\.|\\nAnswer:|\\nExplanation:|\\nReference:|\\n|\$)/s);
    const optionMatches = {};
    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
        const regex = new RegExp(letter + '\\\\.\\\\s*(.+?)(?=\\\\n[A-E]\\\\.|\\\\nAnswer:|\\\\nExplanation:|\\\\nReference:|\\\\n|\\\$)', 's');
        const match = text.match(regex);
        if (match) optionMatches[letter.toLowerCase()] = match[1].trim();
    });
    const answerMatch = text.match(/Answer:\\s*([A-E])/i);
   const explanationMatch = text.match(/Explanation:\\s*([\\s\\S]*?)(?:\\nReference:|$)/);
    const referenceMatch = text.match(/Reference:\\s*(.+)/s);

    if (questionMatch) block.find('textarea[name*="[question]"]').val(questionMatch[1].trim());
    for (const [key, val] of Object.entries(optionMatches)) {
       block.find("input[name*=\"[option_" + key + "]\"]").val(val);
    }
    if (answerMatch) {
        block.find('input[type="radio"][value="' + answerMatch[1].toLowerCase() + '"]').prop('checked', true);
    }
    if (explanationMatch) block.find('.explanation-field').val(explanationMatch[1].trim());
    if (referenceMatch) block.find('.reference-field').val(referenceMatch[1].trim());

    showToast('MCQ parsed successfully.', 'success');
});

renumberMcqBlocks();
JS;
$this->registerJs($js, View::POS_READY);
?>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="mcq-bulk-add">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="mcq-bulk-form" method="post" enctype="multipart/form-data" action="<?= Url::to(['mcq/save-multiple']) ?>">
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
                                <?= $this->render('partials/_mcq_form_fields', ['index' => 0, 'organsys' => $organsys, 'chapters' => $chapters, 'subjects' => $subjects, 'topics' => $topics]) ?>
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
            <button type="submit" class="btn btn-primary" id="save-all-btn">
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
                <?= $this->render('partials/_mcq_form_fields', ['index' => '__INDEX__', 'organsys' => $organsys, 'chapters' => $chapters, 'subjects' => $subjects, 'topics' => $topics]) ?>
            </div>
        </div>
    </div>
</div>


<?php
$js = <<<'JS'
let mcqIndex = 1;

function renumberMcqBlocks() {
    $('#mcq-accordion .mcq-block').each(function(i) {
        $(this).find('.mcq-title').text(`Question #${i + 1}`);
    });
}


$('#add-mcq-btn').on('click', function () {

    let template = $('#mcq-template').html().replace(/__INDEX__/g, mcqIndex);

    let newBlock = $(template).appendTo('#mcq-accordion');

    new bootstrap.Collapse(newBlock.find('.accordion-collapse'), {
      toggle: true
    });

    mcqIndex++;
    renumberMcqBlocks();

    $('html, body').animate({
        scrollTop: newBlock.offset().top - 70
    }, 500);
});


$('#mcq-accordion').on('click', '.btn-remove-mcq', function (e) {
    e.stopPropagation();
    
    if ($('#mcq-accordion .mcq-block').length <= 1) {
        alert("You cannot remove the last question.");
        return;
    }
    
    if (confirm("Are you sure you want to remove this question?")) {
        $(this).closest('.mcq-block').remove();
        renumberMcqBlocks();
    }
});

$('#mcq-bulk-form').on('submit', function (e) {
    e.preventDefault();
    const url = $(this).attr('action');

    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('#save-all-btn').addClass('disabled');
            $('#save-all-btn').find('.spinner-border').show();
        },
        success: function (response) {
            if (response.success) {
                showToast(response.message, 'success');
            } else {
                showToast(response.message, 'danger');
            }
            setTimeout(() => {
                location.reload();
            }, 1000);
        },
        error: function (xhr, status, error) {
            alert(error);
        }
    });
});

$('#mcq-accordion').on('click', '.btn-parse-mcq', function () {
    const block = $(this).closest('.mcq-block');
    const text = block.find('.full-mcq-input').val();

    const idMatch = text.match(/Question ID:\s*(.+)/i);
    const questionMatch = text.match(/Question:\s*([\s\S]*?)(?=\nOrgan System:|\n[A-E]\.|$)/i);

    const organMatch = text.match(/Organ System:\s*(.+)/i);
    const subjectMatch = text.match(/Subject:\s*(.+)/i);
    const chapterMatch = text.match(/Chapter:\s*(.+)/i);
    const topicMatch = text.match(/Topic:\s*(.+)/i);

    const optionMatches = {};
    ['A', 'B', 'C', 'D', 'E'].forEach(letter => {
        const regex = new RegExp(letter + '\\.\\s*(.+?)(?=\\n[A-E]\\. |\\nAnswer:|\\nExplanation:|\\nReference:|\\n|$)', 's');
        const match = text.match(regex);
        if (match) optionMatches[letter.toLowerCase()] = match[1].trim();
    });

    const answerMatch = text.match(/Answer:\s*([A-E])/i);
    const explanationMatch = text.match(/Explanation:\s*([\s\S]*?)(?=\nReference:|$)/i);
    const referenceMatch = text.match(/Reference:\s*([\s\S]*?)(?=\nDifficulty Level:|$)/i);
    const difficultyMatch = text.match(/Difficulty Level:\s*(.+)/i);
    const tagsMatch = text.match(/Tags:\s*(.+)/i);

    // Fill fields
    if (idMatch) block.find('input[name*="[ID]"]').val(idMatch[1].trim());
    if (questionMatch) block.find('textarea[name*="[question]"]').val(questionMatch[1].trim());

    for (const [key, val] of Object.entries(optionMatches)) {
        block.find("input[name*='[option_" + key + "]']").val(val);
    }

    if (answerMatch) {
        block.find('input[type="radio"][value="' + answerMatch[1].toLowerCase() + '"]').prop('checked', true);
    }

    if (explanationMatch) block.find('.explanation-field').val(explanationMatch[1].trim());
    if (referenceMatch) block.find('.reference-field').val(referenceMatch[1].trim());

    if (difficultyMatch)
        block.find('select[name*="[difficulty_level]"]').val(difficultyMatch[1].trim());

    if (tagsMatch)
        block.find('input[name*="[tags]"]').val(tagsMatch[1].trim());

    // Dropdown selection helper
    const setDropdown = (select, value) => {
        value = value.trim().toLowerCase();
        select.find('option').each(function () {
            if ($(this).text().trim().toLowerCase() === value) {
                $(this).prop('selected', true);
            }
        });
    };

    if (organMatch) setDropdown(block.find('select[name="organ_systems[]"]'), organMatch[1]);
    if (subjectMatch) setDropdown(block.find('select[name="subjects[]"]'), subjectMatch[1]);
    if (chapterMatch) setDropdown(block.find('select[name="chapters[]"]'), chapterMatch[1]);
    if (topicMatch) setDropdown(block.find('select[name="topics[]"]'), topicMatch[1]);

    showToast('MCQ parsed successfully.', 'success');
});

renumberMcqBlocks();
JS;
$this->registerJs($js, View::POS_READY);
?>
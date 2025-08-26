<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var \yii\web\View $this */
/** @var array $topics */

$this->title = 'Add Multiple MCQs';
$this->params['breadcrumbs'][] = $this->title;

// A small CSS block for our custom UI elements
$this->registerCss("
    #mcq-template { display: none; }
    .accordion-button:not(.collapsed)::after {
        background-image: var(--bs-accordion-btn-active-icon);
        transform: var(--bs-accordion-btn-icon-transform);
    }
    .remove-mcq-btn {
        z-index: 10;
    }
");
?>

<div class="add-mcq-page">

    <?php $form = ActiveForm::begin([
        'id' => 'mcq-form',
        'enableClientValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
            'data-url' => Url::to(['mcq/save-multiple'])
        ]
    ]); ?>

    <!-- Page Header & Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <div class="d-flex gap-2">
            <button type="button" id="add-mcq-btn" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Add Another MCQ
            </button>
            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Save All MCQs', [
                'class' => 'btn btn-success shadow-sm',
                'id' => 'save-all-btn'
            ]) ?>
        </div>
    </div>

    <!-- Accordion Container for MCQs -->
    <div class="accordion shadow-sm" id="mcq-accordion-container">
        <!-- The first visible MCQ Block -->
        <div class="accordion-item mcq-block" data-index="0">
            <h2 class="accordion-header" id="heading-0">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-0" aria-expanded="true" aria-controls="collapse-0">
                    <span class="mcq-header-title fw-bold">MCQ #1</span>
                </button>
                <!-- This first block cannot be removed -->
            </h2>
            <div id="collapse-0" class="accordion-collapse collapse show" aria-labelledby="heading-0" data-bs-parent="#mcq-accordion-container">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Question ID</label><input type="text" name="mcqs[0][question_id]" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Topic</label><select name="mcqs[0][topic_id]" class="form-select" required><?php foreach ($topics as $topic): ?><option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option><?php endforeach ?></select></div>
                        <div class="col-12"><label class="form-label">Question Text</label><textarea name="mcqs[0][question_text]" class="form-control" rows="2" required></textarea></div>
                        
                        <div class="col-12"><label class="form-label">Options</label></div>
                        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                            <div class="col-12"><div class="input-group"><span class="input-group-text"><?= strtoupper($opt) ?>.</span><input type="text" name="mcqs[0][option_<?= $opt ?>]" class="form-control" required></div></div>
                        <?php endforeach; ?>
                        
                        <div class="col-md-6"><label class="form-label">Correct Option</label><select name="mcqs[0][correct_option]" class="form-select" required><option value="">Select</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option></select></div>
                        <div class="col-md-6"><label class="form-label">Reference</label><input type="text" name="mcqs[0][reference]" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Explanation</label><textarea name="mcqs[0][explanation]" class="form-control" rows="3"></textarea></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- Hidden Template for Cloning -->
    <div id="mcq-template" class="accordion-item mcq-block">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false">
                <span class="mcq-header-title fw-bold"></span>
            </button>
            <button type="button" class="btn-close remove-mcq-btn position-absolute top-0 end-0 mt-3 me-4" aria-label="Close"></button>
        </h2>
        <div class="accordion-collapse collapse">
            <div class="accordion-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Question ID</label><input type="text" name="mcqs[TPL][question_id]" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Topic</label><select name="mcqs[TPL][topic_id]" class="form-select" required><?php foreach ($topics as $topic): ?><option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option><?php endforeach ?></select></div>
                    <div class="col-12"><label class="form-label">Question Text</label><textarea name="mcqs[TPL][question_text]" class="form-control" rows="2" required></textarea></div>
                    
                    <div class="col-12"><label class="form-label">Options</label></div>
                    <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                        <div class="col-12"><div class="input-group"><span class="input-group-text"><?= strtoupper($opt) ?>.</span><input type="text" name="mcqs[TPL][option_<?= $opt ?>]" class="form-control" required></div></div>
                    <?php endforeach; ?>

                    <div class="col-md-6"><label class="form-label">Correct Option</label><select name="mcqs[TPL][correct_option]" class="form-select" required><option value="">Select</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option></select></div>
                    <div class="col-md-6"><label class="form-label">Reference</label><input type="text" name="mcqs[TPL][reference]" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Explanation</label><textarea name="mcqs[TPL][explanation]" class="form-control" rows="3"></textarea></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(function() {
    let mcqIndex = 1;
    const container = $('#mcq-accordion-container');
    const template = $('#mcq-template');

    function updateMcqHeaders() {
        container.find('.mcq-block').each(function(index) {
            $(this).find('.mcq-header-title').text('MCQ #' + (index + 1));
        });
    }

    // Add new MCQ block
    $('#add-mcq-btn').on('click', function() {
        const newBlock = template.clone().removeAttr('id').addClass('mcq-block');
        
        // Update names and IDs for the new block
        newBlock.attr('data-index', mcqIndex);
        newBlock.find('.accordion-header').attr('id', 'heading-' + mcqIndex);
        
        const collapse = newBlock.find('.accordion-collapse');
        collapse.attr('id', 'collapse-' + mcqIndex).attr('aria-labelledby', 'heading-' + mcqIndex);
        
        const button = newBlock.find('.accordion-button');
        button.attr('data-bs-target', '#collapse-' + mcqIndex).attr('aria-controls', 'collapse-' + mcqIndex);
        
        newBlock.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) {
                const newName = name.replace(/\[TPL\]/g, '[' + mcqIndex + ']');
                $(this).attr('name', newName).val('');
            }
        });

        // Collapse others and show the new one
        container.find('.accordion-collapse.show').removeClass('show');
        container.find('.accordion-button').addClass('collapsed').attr('aria-expanded', 'false');
        
        collapse.addClass('show');
        button.removeClass('collapsed').attr('aria-expanded', 'true');

        container.append(newBlock);
        updateMcqHeaders();
        mcqIndex++;
        
        // Scroll to the new element
        $('html, body').animate({
            scrollTop: newBlock.offset().top - 20
        }, 300);
    });

    // Remove an MCQ block
    container.on('click', '.remove-mcq-btn', function() {
        $(this).closest('.mcq-block').remove();
        updateMcqHeaders();
        // Optionally, expand the last item after deleting one
        container.find('.accordion-item:last .accordion-collapse').collapse('show');
    });

    // Handle form submission
    $('#mcq-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const saveBtn = $('#save-all-btn');
        const originalBtnText = saveBtn.html();

        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

        $.ajax({
            type: "POST",
            url: form.data('url'),
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                // Assuming showToast is a global function you have
                if (window.showToast) {
                    const message = res.message || 'MCQs saved successfully!';
                    const type = res.success === 'warning' ? 'warning' : 'success';
                    showToast(message, type);
                } else {
                    alert(res.message || 'MCQs saved successfully!');
                }

                // Optional: Reset form on success
                // container.find('.mcq-block:not(:first)').remove();
                // form[0].reset();
                // mcqIndex = 1;
                // updateMcqHeaders();
            },
            error: function(xhr) {
                if (window.showToast) {
                    showToast('An error occurred. Please check the form and try again.', 'danger');
                } else {
                    alert('An error occurred.');
                }
            },
            complete: function() {
                saveBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
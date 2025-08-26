<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var \app\models\PartnerMcqs $model */

$this->title = 'Update MCQ';
$this->params['breadcrumbs'][] = ['label' => 'My Exams', 'url' => ['exam/index', 'access' => Yii::$app->request->get('access')]];
$this->params['breadcrumbs'][] = ['label' => 'Manage MCQs', 'url' => ['exam/manage-mcqs', 'exam_id' => $model->partner_exam_id, 'access' => Yii::$app->request->get('access')]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mcq-update">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-0"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Editing question for exam:
                <strong><?= Html::encode($model->partnerExam->title) ?></strong>
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="card-body">
            <?= $form->field($model, 'question')->textarea(['rows' => 3]) ?>

            <div class="mb-3">
                <label class="form-label fw-bold">Options & Correct Answer</label>
                <div class="row g-3">
                    <?php foreach (['a', 'b', 'c', 'd', 'e'] as $option): ?>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-text">
                                    <?= Html::radio('PartnerMcqs[correct_option]', strtolower($model->correct_option) == $option, [
                                        'value' => $option,
                                        'class' => 'form-check-input mt-0',
                                        'required' => true
                                    ]) ?>
                                </div>
                                <?= Html::textInput("PartnerMcqs[option_$option]", $model->{"option_$option"}, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Option ' . strtoupper($option),
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= Html::error($model, 'correct_option', ['class' => 'invalid-feedback d-block']) ?>
            </div>
            <div class="mb-3">
                <?= $form->field($model, 'explanation')->textarea(['rows' => 3]) ?>
            </div>

            <div class="mb-3">
                    <?= $form->field($model, 'reference')->textarea(['rows' => 2]) ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Question Image (Optional)</label>
                <?php if (!empty($model->image_url)): ?>
                    <div class="mb-2" id="current-image-container">
                        <p class="mb-1">Current Image:</p>
                        <?= Html::img($model->image_url, ['class' => 'img-thumbnail', 'style' => 'max-width: 250px;']) ?>
                        <?= $form->field($model, 'remove_image')->checkbox(['class' => 'mt-2']) ?>
                    </div>
                <?php endif; ?>

                <div id="image-upload-container">
                    <?= $form->field($model, 'image_url', [
                        'inputOptions' => ['class' => 'form-control', 'id' => 'image-upload-input', 'accept' => 'image/*'],
                        'template' => '{label}{input}{error}{hint}'
                    ])->fileInput()->label(false) ?>
                </div>

                <div id="image-preview-container" style="display: none; margin-top: 10px;">
                    <p class="mb-1">New Image Preview:</p>
                    <img id="image-preview" src="#" alt="New Image Preview" class="img-thumbnail"
                        style="max-width: 250px;">
                </div>
            </div>

        </div>
        <div class="card-footer text-end">
            <?= Html::a('Cancel', ['exam/manage-mcqs', 'exam_id' => $model->partner_exam_id, 'access' => Yii::$app->request->get('access')], ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> Save Changes', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
// JavaScript for image preview and showing/hiding the "Remove" checkbox
$js = <<<'JS'
const imageInput = document.getElementById('image-upload-input');
const imagePreview = document.getElementById('image-preview');
const previewContainer = document.getElementById('image-preview-container');
const removeCheckbox = document.getElementById('partnermcqs-remove_image');
const currentImageContainer = document.getElementById('current-image-container');

// Show preview for new image
imageInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.setAttribute('src', e.target.result);
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(this.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
});

// If user checks "Remove Image", hide the current image preview
if(removeCheckbox) {
    removeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            if(currentImageContainer) {
                currentImageContainer.style.opacity = '0.5';
            }
        } else {
             if(currentImageContainer) {
                currentImageContainer.style.opacity = '1';
            }
        }
    });
}
JS;
$this->registerJs($js, View::POS_READY);
?>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var \app\models\PartnerExams $model */
/** @var string $title */
$accessList = $accessList ?? [[]];
?>

<h2><?= Html::encode($title) ?></h2>

<div class="card card-body">
    <?php $form = ActiveForm::begin(); ?>

    <h4 class="mb-3">Exam Information</h4>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'course_conductor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->dropDownList([
        1 => 'Active',
        0 => 'Inactive'
    ], ['prompt' => 'Select Status']) ?>

    <h4 class="mt-4 mb-3">Allowed Students</h4>

    <div id="student-access-list">
        <?php foreach ($accessList as $i => $access): ?>
            <div class="row student-access-row mb-2">
                <div class="col-md-5">
                    <input type="email" name="PartnerExamAccess[<?= $i ?>][email]" class="form-control"
                        value="<?= Html::encode($access['email'] ?? '') ?>" placeholder="Student Email" required>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="PartnerExamAccess[<?= $i ?>][passkey]" class="form-control passkey-input"
                            value="<?= Html::encode($access['passkey'] ?? '') ?>" placeholder="Passkey" required>
                        <button type="button" class="btn btn-outline-secondary generate-passkey">Generate</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-row "><i class="bi bi-x-circle-fill"></i></button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="add-access-row" class="btn btn-outline-primary mt-2">Add Another Student</button>


    <div class="form-group mt-3 d-flex justify-content-end">
        <?= Html::submitButton($model->isNewRecord ? 'Create Exam' : 'Update Exam', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
let rowIndex = 1;

function generateRandomPasskey(length = 10) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let passkey = '';
    for (let i = 0; i < length; i++) {
        passkey += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return passkey;
}

$('#add-access-row').on('click', function () {
    const row = `
    <div class="row student-access-row mb-2">
        <div class="col-md-5">
            <input type="email" name="PartnerExamAccess[\${rowIndex}][email]" class="form-control" placeholder="Student Email" required>
        </div>
        <div class="col-md-5">
            <div class="input-group">
                <input type="text" name="PartnerExamAccess[\${rowIndex}][passkey]" class="form-control passkey-input" placeholder="Passkey" required>
                <button type="button" class="btn btn-outline-secondary generate-passkey">Generate</button>
            </div>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-row "><i class="bi bi-x-circle-fill"></i></button>

        </div>
    </div>
    `;
    $('#student-access-list').append(row);
    rowIndex++;
});

$(document).on('click', '.remove-row', function () {
    $(this).closest('.student-access-row').remove();
});

$(document).on('click', '.generate-passkey', function () {
    const input = $(this).closest('.input-group').find('.passkey-input');
    input.val(generateRandomPasskey());
});
JS;

$this->registerJs($js);
?>
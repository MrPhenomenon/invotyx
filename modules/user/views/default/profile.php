<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Users $user */
/** @var app\models\form\ChangePasswordForm $changePasswordForm */
/** @var app\models\ActiveSubscription $activeSubscription */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Your Profile';
$this->params['breadcrumbs'][] = $this->title;

$examTypes = ArrayHelper::map(\app\models\ExamType::find()->all(), 'id', 'name');

$specialties = $user->exam_type ? ArrayHelper::map(
    \app\models\ExamSpecialties::find()->where(['exam_type' => $user->exam_type])->all(),
    'id',
    'name'
) : [];

$specialtyName = $user->speciality->name ?? 'Not set';
$examTypeName = $examTypes[$user->exam_type] ?? 'Not set';

?>

<div class="container mt-4">
    <h2 class="mb-4"><?= Html::encode($this->title) ?></h2>

    <?php if (Yii::$app->session->hasFlash('profile-updated')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= Yii::$app->session->getFlash('profile-updated') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- User Profile Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Profile Details</h5>
            <button id="edit-profile-btn" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-pencil-alt me-1"></i> Edit Profile
            </button>
        </div>
        <div class="card-body">

            <!-- VIEW SECTION -->
            <div id="profile-view-section">
                <div class="row mb-2">
                    <div class="col-sm-3 text-muted">Full Name</div>
                    <div class="col-sm-9 fw-bold"><?= Html::encode($user->name) ?></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-sm-3 text-muted">Email</div>
                    <div class="col-sm-9"><?= Html::encode($user->email) ?></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-sm-3 text-muted">Exam Type</div>
                    <div class="col-sm-9"><?= Html::encode($examTypeName) ?></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-sm-3 text-muted">Specialty</div>
                    <div class="col-sm-9"><?= Html::encode($specialtyName) ?></div>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-sm-3 text-muted">Expected Exam Date</div>
                    <div class="col-sm-9">
                        <?= $user->expected_exam_date ? Yii::$app->formatter->asDate($user->expected_exam_date, 'long') : 'Not set' ?>
                    </div>
                </div>
            </div>

            <!-- EDIT SECTION (Initially hidden) -->
            <div id="profile-edit-section" style="display: none;">
                <?php $form = ActiveForm::begin([
                    'id' => 'update-profile-form',
                    'action' => ['default/update-profile'],
                    'method' => 'post',
                ]); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($user, 'name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($user, 'email')->textInput(['readonly' => true, 'class' => 'form-control-plaintext']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($user, 'exam_type')->dropDownList($examTypes, [
                            'prompt' => 'Select Exam Type',
                            'onchange' => '
                                $.get("' . \yii\helpers\Url::to(['default/specialties']) . '?exam_type=" + $(this).val(), function(data) {
                                    $("#users-speciality_id").html(data).prop("disabled", false);
                                });
                            '
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($user, 'speciality_id')->dropDownList($specialties, ['prompt' => 'Select Specialty']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($user, 'expected_exam_date')->input('date') ?>
                    </div>
                </div>
                <div class="mt-3">
                    <?= Html::submitButton('Save Changes', ['class' => 'btn btn-primary']) ?>
                    <button type="button" id="cancel-edit-btn" class="btn btn-secondary">Cancel</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <?php if ($user->auth_type === 'local'): ?>

        <!-- Change Password Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Security</h5>
            </div>
            <div class="card-body">
                <?php if (Yii::$app->session->hasFlash('password-changed')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= Yii::$app->session->getFlash('password-changed') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('password-error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= Yii::$app->session->getFlash('password-error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin([
                    'id' => 'change-password-form',
                    'action' => ['default/change-password'],
                    'method' => 'post'
                ]); ?>
                <p class="card-text">Update the password associated with your account.</p>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($changePasswordForm, 'current_password')->passwordInput() ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($changePasswordForm, 'new_password')->passwordInput() ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($changePasswordForm, 'confirm_password')->passwordInput() ?>
                    </div>
                </div>
                <div class="mt-3">
                    <?= Html::submitButton('Change Password', ['class' => 'btn btn-warning']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

    <?php endif; ?>

    <!-- Active Subscription Card -->
    <?php if ($activeSubscription): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">Active Subscription</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 text-muted">Plan</div>
                    <div class="col-sm-9 fw-bold fs-5 text-success">
                        <?= Html::encode($activeSubscription->subscription->name ?? 'N/A') ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3 text-muted">Active From</div>
                    <div class="col-sm-9"><?= Yii::$app->formatter->asDate($activeSubscription->start_date, 'long') ?></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3 text-muted">Renews/Expires On</div>
                    <div class="col-sm-9"><?= Yii::$app->formatter->asDate($activeSubscription->end_date, 'long') ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php

$js = <<<JS
$(document).ready(function() {
    const viewSection = $('#profile-view-section');
    const editSection = $('#profile-edit-section');
    const editButton = $('#edit-profile-btn');
    const cancelButton = $('#cancel-edit-btn');

    editButton.on('click', function() {
        viewSection.hide();
        editSection.show();
        $(this).hide();
    });

    cancelButton.on('click', function() {
        editSection.hide();
        viewSection.show();
        editButton.show();
    });
});

// Profile form AJAX
$('#update-profile-form').on('submit', function(e) {
    e.preventDefault();

    const form = $(this);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                showToast(response.message);
                $('#profile-edit-section').hide();
                $('#profile-view-section').show();
                $('#edit-profile-btn').show();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(response.message || 'Could not update profile.', 'warning');
            }
        },
        error: function() {
            showToast('Server error occurred.', 'danger');
        }
    });
});

// Password form AJAX
$('#change-password-form').on('submit', function(e) {
    e.preventDefault();

    const form = $(this);
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                showToast(response.message);
                form.trigger('reset');
            } else {
                showToast(response.message || 'Failed to change password.', 'danger');
            }
        },
        error: function() {
            toastr.error('Server error occurred.');
        }
    });
});

JS;
$this->registerJs($js);
?>
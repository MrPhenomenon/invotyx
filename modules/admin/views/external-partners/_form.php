<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\ExternalPartners */
?>
<div class="external-partners-create container mt-4">
    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'email')->input('email') ?>
    <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'status')->dropDownList(['active' => 'Active', 'inactive' => 'Inactive']) ?>
    <?= $form->field($model, 'created_by')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

    <div class="form-group mt-3 text-end">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php if (Yii::$app->session->hasFlash('partner')): ?>
    <?php
    $flash = Yii::$app->session->getFlash('partner');
    $type = $flash['type'] ?? 'info';
    $msg = $flash['message'] ?? '';
    $js = <<<JS
        showToast('$msg', '$type');
    JS;
    $this->registerJs($js);
    ?>
<?php endif; ?>

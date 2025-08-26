<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Subscriptions $model */
/** @var yii\widgets\ActiveForm $form */

// JS for the dynamic feature list
$this->registerJs("
    $('#add-feature-btn').on('click', function() {
        var featureList = $('#feature-list');
        var newIndex = featureList.find('.feature-item').length;
        var newItem = $('<div class=\"feature-item input-group mb-2\">' +
            '<input type=\"text\" name=\"Subscriptions[features_array][]\" class=\"form-control\">' +
            '<button class=\"btn btn-outline-danger remove-feature-btn\" type=\"button\"><i class=\"fas fa-trash\"></i></button>' +
        '</div>');
        featureList.append(newItem);
    });

    $('#feature-list').on('click', '.remove-feature-btn', function() {
        $(this).closest('.feature-item').remove();
    });
");
?>

<div class="subscriptions-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'price')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'duration_days')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-12">
                    <label class="form-label"><?= $model->getAttributeLabel('features_array') ?> (Enter each feature on a new line. Prefix with [x] to mark as disabled)</label>
                    <div id="feature-list">
                        <?php
                        // Ensure features_array is an array to prevent errors
                        $features = is_array($model->features_array) ? $model->features_array : [];
                        if (empty($features)) {
                            // Add one empty input for new records
                            $features[] = '';
                        }

                        foreach ($features as $i => $feature) {
                            // We use the ActiveForm field method here for proper model binding
                            echo $form->field($model, "features_array[$i]", [
                                'template' => '<div class="feature-item input-group mb-2">{input}<button class="btn btn-outline-danger remove-feature-btn" type="button"><i class="fas fa-trash"></i></button></div>{error}',
                                'inputOptions' => ['class' => 'form-control', 'placeholder' => 'e.g., Structured MCQ pool'],
                            ])->textInput(['value' => $feature])->label(false);
                        }
                        ?>
                    </div>
                    <button type="button" id="add-feature-btn" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus me-2"></i>Add Another Feature
                    </button>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::submitButton('<i class="fas fa-save me-2"></i>Save Plan', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
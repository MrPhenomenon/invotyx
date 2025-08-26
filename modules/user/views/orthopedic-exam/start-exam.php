<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = "Start Exam";

?>
<?php
$flashes = Yii::$app->session->getAllFlashes();
$js = '';
foreach ($flashes as $type => $message) {
    $js .= "showToast(" . json_encode($message) . ", " . json_encode($type) . ");\n";
}
if (!empty($js)) {
    $this->registerJs($js);
}
?>

<div class="container my-5 px-5">
    <h2 class="mb-4"><?= Html::encode($exam->title) ?></h2>
    <h4 class="mb-4">Course Conductor: <?= Html::encode($exam->course_conductor) ?></h4>

    <div class="card">
        <div class="card-body">

            <?php $form = ActiveForm::begin(['id' => 'exam-start-form', 'method' => 'post']); ?>

            <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'user_hospital')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'user_email')->textInput(['type' => 'email']) ?>
            <?= $form->field($model, 'passkey')->passwordInput(['maxlength' => true]) ?>

            <div class="instructions-card alert alert-primary">
                <h4 class="text-primary"><i class="fas fa-info-circle"></i> Exam Instructions</h4>
                <ul>
                    <li><i class="fas fa-check-circle"></i> This exam consists of <strong>two papers</strong>: Paper 1
                        and Paper 2.</li>
                    <li><i class="fas fa-check-circle"></i> Each paper includes <strong>100 Single Best Answer (SBA)
                            MCQs</strong>.</li>
                    <li><i class="fas fa-check-circle"></i> You have <strong>2.5 hours allocated per paper</strong>.
                    </li>
                    <li><i class="fas fa-check-circle"></i> There will be a <strong>mandatory 30-minute break</strong>
                        between the two papers.</li>
                    <li><i class="fas fa-check-circle"></i> Once the timer starts for a paper, you <strong>cannot pause
                            or stop the paper</strong>.</li>
                    <li><i class="fas fa-check-circle"></i> You can <strong>skip a question once during each
                            paper</strong>.</li>
                    <li><i class="fas fa-check-circle"></i> All skipped questions will reappear at the end of the paper
                        in the order they were skipped. You <strong>will not be allowed to skip them again</strong>.
                    </li>
                </ul>
            </div>
            <div class="form-group mt-3">
                <?= Html::submitButton('Start Exam', [
                    'class' => 'btn btn-primary',
                    'id' => 'start-btn',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
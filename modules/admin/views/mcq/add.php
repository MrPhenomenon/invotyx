<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<h3>Add Multiple MCQs</h3>

<?php $form = ActiveForm::begin([
  'id' => 'mcq-form',
  'enableClientValidation' => false,
  'options' => ['enctype' => 'multipart/form-data'],
]); ?>


<div id="mcq-container" class="card">

  <!-- First MCQ Block -->
  <div class=" card-body mcq-block">
    <h5>MCQ #1</h5>
    <div class="mb-2">
      <label class="form-label">Question ID</label>
      <input type="text" name="mcqs[0][question_id]" class="form-control" required>
    </div>
    <div class="mb-2">
      <label class="form-label">Question Text</label>
      <input type="text" name="mcqs[0][question_text]" class="form-control" required>
    </div>

    <label class="form-label">Options</label>
    <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
      <div class="mb-2">
        <div class="input-group">
          <span class="input-group-text"><?= strtoupper($opt) ?>.</span>
          <input type="text" name="mcqs[0][option_<?= $opt ?>]" class="form-control" required>
        </div>
      </div>
    <?php endforeach; ?>


    <div class="mb-2">
      <label class="form-label">Correct Option</label>
      <select name="mcqs[0][correct_option]" class="form-select" required>
        <option value="">Select</option>
        <option value="a">A</option>
        <option value="b">B</option>
        <option value="c">C</option>
        <option value="d">D</option>
        <option value="e">E</option>
      </select>
    </div>

    <div class="mb-2">
      <label class="form-label">Explanation</label>
      <textarea name="mcqs[0][explanation]" class="form-control"></textarea>
    </div>

    <div class="mb-2">
      <label class="form-label">Topic</label>
      <select name="mcqs[0][topic_id]" class="form-select" required>
        <?php foreach ($topics as $topic): ?>
          <option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="mb-2">
      <label class="form-label">Difficulty</label>
      <select name="mcqs[0][difficulty_level]" class="form-select" required>
        <option value="1">Easy</option>
        <option value="2">Moderate</option>
        <option value="3">Difficult</option>
      </select>
    </div>
  </div>

</div>

<div class="pb-5 d-flex justify-content-between">
  <button type="button" id="add-mcq-btn" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Add Another
    MCQ</button>
  <?= Html::submitButton('Save All MCQs', ['class' => 'btn btn-success ']) ?>
</div>


<?php ActiveForm::end(); ?>

<?php

$js = <<<JS
let mcqIndex = 1;

$('#add-mcq-btn').on('click', function() {
  const block = $('.mcq-block').first().clone();
  block.find('input, select, textarea').each(function() {
    const name = $(this).attr('name');
    const newName = name.replace(/\[0\]/g, '[' + mcqIndex + ']');
    $(this).attr('name', newName).val('');
  });
  block.find('h5').text('MCQ #' + (mcqIndex + 1));
  $('#mcq-container').append(block);
  mcqIndex++;
});

$('#mcq-form').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  $.ajax({
    type: "POST",
    url: "/admin/mcq/save-multiple",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log('Success:', response);
    },
    error: function (xhr) {
      console.log('Error:', xhr.responseText);
    }
  });
});
 

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
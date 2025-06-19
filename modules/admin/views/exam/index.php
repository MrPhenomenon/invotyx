<?php
use yii\helpers\Html;
use yii\helpers\Url;

?>

<h3>📚 Exams & Specializations</h3>

<div class="row g-4">
  <!-- Chapters Table -->
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Exams</strong>
        <button class="btn btn-sm btn-primary" id="add-exam"><i class="bi bi-plus-circle-fill"></i> Add</button>
      </div>
      <div class="card-body p-2">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Number of Specializations</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="chapter-body">
            <?php foreach ($exams as $exam): ?>
              <tr>
                <td><?= $exam['name'] ?></td>
                <td><?= $exam['specialties_count'] ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info">Update</button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $exam['id'] ?>"
                    data-item="Exam <?= $exam['name'] ?>" data-url="/">Delete</button>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Topics Table -->
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Specialization</strong>
        <button class="btn btn-sm btn-primary" id="add-spec"><i class="bi bi-plus-circle-fill"></i> Add</button>
      </div>
      <div class="card-body p-2">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Exam</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="topic-body">
            <?php foreach ($specializations as $specs): ?>
              <tr>
                <td><?= $specs['name'] ?></td>
                <td><?= $specs['examType']['name'] ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info">Update</button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $specs['id'] ?>"
                    data-item="Specialization <?= $specs['name'] ?>" data-url="/">Delete</button>
                </td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Chapter Modal -->
<div class="modal fade" id="examModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="exam-form" data-url="<?= Url::to(['exam/add-exam']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Exam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Exam Name</label>
            <input type="text" name="name" class="form-control" placeholder="FCPS, PLAB etc." required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success">Save</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Topic Modal -->
<div class="modal fade" id="specModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="spec-form" data-url="<?= Url::to(['exam/add-specialization']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Exam Specialization</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Specializaion Name</label>
            <input type="text" name="name" class="form-control" placeholder="Medicine, Surgery etc" required>
          </div>
          <div class="mb-3">
            <label>Select Exam</label>
            <select name="exam_id" class="form-select">
              <?php foreach ($exams as $exam): ?>
                <option value="<?= $exam['id'] ?>"><?= $exam['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success">Save</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php

$js = <<<JS

$('#add-exam').on('click', function () {
  $('#exam-form')[0].reset();
  new bootstrap.Modal('#examModal').show();
});

$('#add-spec').on('click', function () {
  $('#spec-form')[0].reset();
  new bootstrap.Modal('#specModal').show();
});


$('#exam-form').on('submit', function (e) {
  e.preventDefault();
  const name = $(this).find('input[name="name"]').val();
 
  $.ajax({
    url: \$(this).data('url'), 
    type: 'POST',
    data: { name },
    success: function (res) {
      res.success ? showToast('Exam added.', 'success') : showToast('Exam couldnt be added', 'danger');
      $('#examModal').modal('hide');
       location.reload();
    },
    error: function () {
      showToast('Failed to add chapter.', 'danger');
    }
  });
});


$('#spec-form').on('submit', function (e) {
  e.preventDefault();
  const name = $(this).find('input[name="name"]').val();
  const examId = $(this).find('select[name="exam_id"]').val();

  $.ajax({
    url: \$(this).data('url'),
    type: 'POST',
    data: { name, examId },
    success: function (res) {
       res.success ? showToast('Specialization added.', 'success') : showToast('Specialization couldnt be added', 'danger');
      $('#specModal').modal('hide');
       location.reload();
    },
    error: function () {
      showToast('Failed to add topic.', 'danger');
    }
  });

});

JS;
$this->registerJS($js, yii\web\View::POS_END)
  ?>
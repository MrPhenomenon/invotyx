<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var array $exams */
/** @var array $specializations */

$this->title = 'Exams & Specializations Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="exam-specialization-management-page">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📚 <?= Html::encode($this->title) ?></h1>
    </div>

    <div class="row g-4">
        <!-- Exams Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Exams</h6>
                    <button class="btn btn-sm btn-primary shadow-sm" id="add-exam"><i class="fas fa-plus me-2"></i>New Exam</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Specializations</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="chapter-body">
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($exam['name']) ?></td>
                                        <td class="text-center"><span class="badge bg-secondary rounded-pill"><?= $exam['specialties_count'] ?></span></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Update</button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $exam['id'] ?>" data-item="Exam <?= Html::encode($exam['name']) ?>, This will also delete the specializations for this exam" data-url="<?= Url::to(['exam/delete-exam'])?>"><i class="fas fa-trash-alt"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Specializations Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-microscope me-2"></i>Specializations</h6>
                    <button class="btn btn-sm btn-primary shadow-sm" id="add-spec"><i class="fas fa-plus me-2"></i>New Specialization</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Exam</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="topic-body">
                                <?php foreach ($specializations as $specs): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($specs['name']) ?></td>
                                        <td><?= Html::encode($specs['examType']['name']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-info"><i class="fas fa-edit"></i> Update</button>
                                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $specs['id'] ?>" data-item="Specialization <?= Html::encode($specs['name']) ?>" data-url="<?= Url::to(['exam/delete-specialization'])?>"><i class="fas fa-trash-alt"></i> Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (Unchanged in structure and IDs, only minor styling) -->

<!-- Add Exam Modal -->
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
            <label class="form-label">Exam Name</label>
            <input type="text" name="name" class="form-control" placeholder="FCPS, PLAB etc." required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Specialization Modal -->
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
            <label class="form-label">Specialization Name</label>
            <input type="text" name="name" class="form-control" placeholder="Medicine, Surgery etc" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Select Exam</label>
            <select name="exam_id" class="form-select">
              <?php foreach ($exams as $exam): ?>
                <option value="<?= $exam['id'] ?>"><?= $exam['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Update Specialization Modal -->
<div class="modal fade" id="specUpdateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- NOTE: The form ID "spec-form" is duplicated in the original. This is preserved. -->
      <form id="spec-form" data-url="<?= Url::to(['exam/update-specialization']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Update Exam Specialization</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="spec-update-id">
          <div class="mb-3">
            <label class="form-label">Specialization Name</label>
            <input type="text" name="name" class="form-control" placeholder="Medicine, Surgery etc" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Select Exam</label>
            <select name="exam_id" class="form-select">
              <?php foreach ($exams as $exam): ?>
                <option value="<?= $exam['id'] ?>"><?= $exam['name'] ?></option>
              <?php endforeach ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Update Exam Modal -->
<div class="modal fade" id="examUpdateModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- NOTE: The form ID "spec-form" is duplicated in the original. This is preserved. -->
      <form id="spec-form" data-url="<?= Url::to(['exam/update-exam']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Update Exam</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <input type="hidden" name="id">
            <label class="form-label">Exam Name</label>
            <input type="text" name="name" class="form-control" placeholder="FCPS, PLAB etc." required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php
// The original JavaScript is kept exactly as it is, as it's tied to the preserved HTML structure.
$js = <<<JS

$('#add-exam').on('click', function () {
  $('#exam-form')[0].reset();
  new bootstrap.Modal('#examModal').show();
});

$('#add-spec').on('click', function () {
  $('#spec-form')[0].reset();
  new bootstrap.Modal('#specModal').show();
});


$('#examModal form').on('submit', function (e) { // More specific selector
  e.preventDefault();
  const form = $(this);
  const saveBtn = form.find('button[type="submit"]');
  const originalBtnText = saveBtn.html();
  saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
 
  $.ajax({
    url: form.data('url'), 
    type: 'POST',
    data: form.serialize(),
    success: function (res) {
      res.success ? showToast('Exam added.', 'success') : showToast('Exam couldnt be added', 'danger');
      bootstrap.Modal.getInstance(document.getElementById('examModal')).hide();
      setTimeout(() => { location.reload(); }, 800);
    },
    error: function () {
      showToast('Failed to add exam.', 'danger');
    },
    complete: function() {
      saveBtn.prop('disabled', false).html(originalBtnText);
    }
  });
});


$('#specModal form').on('submit', function (e) { // More specific selector
  e.preventDefault();
  const form = $(this);
  const saveBtn = form.find('button[type="submit"]');
  const originalBtnText = saveBtn.html();
  saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

  $.ajax({
    url: form.data('url'),
    type: 'POST',
    data: form.serialize(),
    success: function (res) {
       res.success ? showToast('Specialization added.', 'success') : showToast('Specialization couldnt be added', 'danger');
      bootstrap.Modal.getInstance(document.getElementById('specModal')).hide();
      setTimeout(() => { location.reload(); }, 800);
    },
    error: function () {
      showToast('Failed to add specialization.', 'danger');
    },
    complete: function() {
      saveBtn.prop('disabled', false).html(originalBtnText);
    }
  });
});

$('#topic-body').on('click', '.btn-info', function () {
  const row = $(this).closest('tr');
  const name = row.find('td').eq(0).text().trim();
  const examName = row.find('td').eq(1).text().trim();
  const id = row.find('.btn-delete').data('id');
  let examId = '';
  $('#specUpdateModal select[name="exam_id"] option').each(function() {
    if ($(this).text().trim() === examName) {
      examId = $(this).val();
    }
  });

  $('#spec-update-id').val(id);
  $('#specUpdateModal input[name="name"]').val(name);
  $('#specUpdateModal select[name="exam_id"]').val(examId);

  new bootstrap.Modal('#specUpdateModal').show();
});

$('#chapter-body').on('click', '.btn-info', function () {
  const row = $(this).closest('tr');
  const name = row.find('td').eq(0).text().trim();
  const id = row.find('.btn-delete').data('id');

  $('#examUpdateModal input[name="name"]').val(name);
  $('#examUpdateModal input[name="id"]').val(id);
  
  new bootstrap.Modal('#examUpdateModal').show();
});

$('#examUpdateModal form').on('submit', function (e) {
  e.preventDefault();
  const form = $(this);
  const saveBtn = form.find('button[type="submit"]');
  const originalBtnText = saveBtn.html();
  saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

  $.ajax({
    url: $(this).data('url'),
    type: 'POST',
    data: form.serialize(),
    success: function (res) {
      res.success
        ? showToast('Exam updated.', 'success')
        : showToast(res.message || 'Update failed', 'danger');
      bootstrap.Modal.getInstance(document.getElementById('examUpdateModal')).hide();
     setTimeout(() => { location.reload(); }, 800);
    },
    error: function () {
      showToast('Failed to update exam.', 'danger');
    },
    complete: function() {
      saveBtn.prop('disabled', false).html(originalBtnText);
    }
  });
});

$('#specUpdateModal form').on('submit', function (e) {
  e.preventDefault();
  const form = $(this);
  const saveBtn = form.find('button[type="submit"]');
  const originalBtnText = saveBtn.html();
  saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
  
  const id = $('#spec-update-id').val();
  const name = $(this).find('input[name="name"]').val();
  const exam_id = $(this).find('select[name="exam_id"]').val();

  $.ajax({
    url: $(this).data('url'),
    type: 'POST',
    data: { id, name, exam_id }, // NOTE: This manual object creation is preserved from original
    success: function (res) {
      res.success
        ? showToast('Specialization updated.', 'success')
        : showToast(res.message || 'Update failed', 'danger');
      bootstrap.Modal.getInstance(document.getElementById('specUpdateModal')).hide();
      setTimeout(() => { location.reload(); }, 800);
    },
    error: function () {
      showToast('Failed to update specialization.', 'danger');
    },
    complete: function() {
      saveBtn.prop('disabled', false).html(originalBtnText);
    }
  });
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
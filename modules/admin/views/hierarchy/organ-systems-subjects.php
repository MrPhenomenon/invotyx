<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Manage Organ Systems & Subjects';
?>

<div class="organ-subject-management-page">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">🧩 <?= Html::encode($this->title) ?></h1>
  </div>

  <div class="row g-4">
    <!-- Organ Systems Card -->
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-heartbeat me-2"></i>Organ Systems</h6>
          <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#organSystemModal"
            id="add-organ-system"><i class="fas fa-plus me-2"></i>New Organ System</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="thead-light">
                <tr>
                  <th>Name</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody id="organ-system-body">
                <?php foreach ($organSystems as $os): ?>
                  <tr>
                    <td class="fw-bold"><?= Html::encode($os['name']) ?></td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $os['id'] ?>"
                        data-item="Organ System '<?= Html::encode($os['name']) ?>'"
                        data-url="<?= Url::to(['hierarchy/delete-organ-system']) ?>">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Subjects Card -->
    <div class="col-lg-6">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Subjects</h6>
          <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#subjectModal"
            id="add-subject"><i class="fas fa-plus me-2"></i>New Subject</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="thead-light">
                <tr>
                  <th>Name</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody id="subject-body">
                <?php foreach ($subjects as $subject): ?>
                  <tr>
                    <td class="fw-bold"><?= Html::encode($subject['name']) ?></td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $subject['id'] ?>"
                        data-item="Subject '<?= Html::encode($subject['name']) ?>'"
                        data-url="<?= Url::to(['hierarchy/delete-subject']) ?>">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="organSystemModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="organ-system-form" data-url="<?= Url::to(['hierarchy/add-organ-system']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Organ System</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Organ System Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="submit-organ-system"><i
              class="fas fa-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Subject Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="subject-form" data-url="<?= Url::to(['hierarchy/add-subject']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="submit-subject"><i
              class="fas fa-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php
$js = <<<JS
$(document).ready(function() {
        $('#submit-organ-system').click(function(e) {
            e.preventDefault();
            var form = $('#organ-system-form');
            var formData = form.serialize();
            $.ajax({
                type: 'POST',
                url: form.data('url'),
                data: formData,
                success: function(response) {
                    response.success ? showToast(response.message, 'success') : showToast(response.message || 'Error adding organ system', 'error');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr, status, error) {
                    toastr.error(error);
                }
            });
        });
    });

$(document).ready(function() {
        $('#submit-subject').click(function(e) {
            e.preventDefault();
            var form = $('#subject-form');
            var formData = form.serialize();
            $.ajax({
                type: 'POST',
                url: form.data('url'),
                data: formData,
                success: function(response) {
                    response.success ? showToast(response.message, 'success') : showToast(response.message || 'Error adding subject', 'error');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr, status, error) {
                    toastr.error(error);
                }
            });
        });
    });
JS;
$this->registerJS($js, yii\web\View::POS_READY);
?>
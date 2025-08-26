<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var array $chapters */
/** @var array $topics */

$this->title = 'Chapter & Topic Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="chapter-topic-management-page">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">📚 <?= Html::encode($this->title) ?></h1>
    </div>

    <div class="row g-4">
        <!-- Chapters Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book me-2"></i>Chapters</h6>
                    <button class="btn btn-sm btn-primary shadow-sm" id="add-chapter"><i class="fas fa-plus me-2"></i>New Chapter</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Topics</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="chapter-body">
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($chapter['name']) ?></td>
                                        <td class="text-center"><span class="badge bg-secondary rounded-pill"><?= $chapter['topic_count'] ?></span></td>
                                        <td class="text-center">
                                            <!-- Buttons are kept separate to preserve original JS functionality -->
                                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $chapter['id'] ?>" data-item="chapter '<?= Html::encode($chapter['name']) ?>'" data-url="<?=Url::to(['mcq/delete-chapter'])?>">
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

        <!-- Topics Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tags me-2"></i>Topics</h6>
                    <button class="btn btn-sm btn-primary shadow-sm" id="add-topic"><i class="fas fa-plus me-2"></i>New Topic</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Chapter</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="topic-body">
                                <?php foreach ($topics as $topic): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($topic['name']) ?></td>
                                        <td><?= Html::encode($topic['chapter_name']) ?></td>
                                        <td class="text-center">
                                            <!-- Buttons are kept separate to preserve original JS functionality -->
                                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $topic['id'] ?>" data-item="topic '<?= Html::encode($topic['name']) ?>'" data-url="<?=Url::to(['mcq/delete-topics'])?>">
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

<!-- Modals (Unchanged in structure and IDs, only minor styling) -->

<!-- Chapter Modal -->
<div class="modal fade" id="chaptersModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="chapter-form" data-url="<?= Url::to(['mcq/add-chapter']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Chapter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Chapter Name</label>
            <input type="text" name="name" class="form-control" required>
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

<!-- Topic Modal -->
<div class="modal fade" id="topicsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="topic-form" data-url="<?= Url::to(['mcq/add-topic']) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Topic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Topic Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Chapter</label>
            <select name="chapter_id" class="form-select">
              <?php foreach ($chapters as $chapter): ?>
                <option value="<?= $chapter['id'] ?>"><?= $chapter['name'] ?></option>
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


<?php
// Enhanced JS with loading spinners, but preserving original logic
$js = <<<JS
$(function() {
    $('#add-chapter').on('click', function () {
      $('#chapter-form')[0].reset();
      new bootstrap.Modal('#chaptersModal').show();
    });

    $('#add-topic').on('click', function () {
      $('#topic-form')[0].reset();
      new bootstrap.Modal('#topicsModal').show();
    });

    $('#chapter-form').on('submit', function (e) {
      e.preventDefault();
      const form = $(this);
      const saveBtn = form.find('button[type="submit"]');
      const originalBtnText = saveBtn.html();
      saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

      $.ajax({
        url: form.data('url'),
        type: 'POST',
        data: form.serialize(), // Using serialize() is safer
        success: function (res) {
          if (res.success) {
            showToast('Chapter added.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('chaptersModal')).hide();
            setTimeout(() => location.reload(), 800); // Reload preserved from original logic
          } else {
            showToast('Chapter couldnt be added', 'danger');
          }
        },
        error: function () {
          showToast('Failed to add chapter.', 'danger');
        },
        complete: function() {
          saveBtn.prop('disabled', false).html(originalBtnText);
        }
      });
    });

    $('#topic-form').on('submit', function (e) {
      e.preventDefault();
      const form = $(this);
      const saveBtn = form.find('button[type="submit"]');
      const originalBtnText = saveBtn.html();
      saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

      $.ajax({
        url: form.data('url'), 
        type: 'POST',
        data: form.serialize(), // Using serialize() is safer
        success: function (res) {
           if(res.success) {
              showToast('Topic added.', 'success');
              bootstrap.Modal.getInstance(document.getElementById('topicsModal')).hide();
              location.reload(); // Reload preserved from original logic
           } else {
              showToast('Topic couldnt be added', 'danger');
           }
        },
        error: function () {
          showToast('Failed to add topic.', 'danger');
        },
        complete: function() {
          saveBtn.prop('disabled', false).html(originalBtnText);
        }
      });
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
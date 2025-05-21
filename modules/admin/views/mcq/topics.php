<?php
use yii\helpers\Html;
?>

<h3>📚 Chapter & Topic Management</h3>

<div class="row g-4">
  <!-- Chapters Table -->
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Chapters</strong>
        <button class="btn btn-sm btn-primary" id="add-chapter"><i class="bi bi-plus-circle-fill"></i> Add</button>
      </div>
      <div class="card-body p-2">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Number of Topics</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="chapter-body">
            <?php foreach ($chapters as $chapter): ?>
              <tr>
                <td><?= $chapter['name'] ?></td>
                <td><?= $chapter['topic_count'] ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" data-id="<?= $chapter['id'] ?>">Edit</button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $chapter['id'] ?>"
                    data-item="a chapter <?= $chapter['name'] ?>" data-url="/admin/default/delete-user">Delete</button>
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
        <strong>Topics</strong>
        <button class="btn btn-sm btn-primary" id="add-topic"><i class="bi bi-plus-circle-fill"></i> Add</button>
      </div>
      <div class="card-body p-2">
        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Chapter</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="topic-body">
          <?php foreach ($topics as $topic): ?>
              <tr>
                <td><?= $topic['name'] ?></td>
                <td><?= $topic['chapter_name'] ?></td>
                <td>
                  <button class="btn btn-sm btn-primary" data-id="<?= $topic['id'] ?>">Edit</button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $topic['id'] ?>"
                    data-item="a topic <?= $topic['name'] ?>" data-url="/admin/default/delete-user">Delete</button>
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
<div class="modal fade" id="chaptersModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="chapter-form">
        <div class="modal-header">
          <h5 class="modal-title">Add Chapter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Chapter Name</label>
            <input type="text" name="name" class="form-control" required>
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
<div class="modal fade" id="topicsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="topic-form">
        <div class="modal-header">
          <h5 class="modal-title">Add Topic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Topic Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Chapter</label>
            <select name="chapter_id" class="form-select">
              <?php foreach ($chapters as $chapter): ?>
                <option value="<?= $chapter['id'] ?>"><?= $chapter['name'] ?></option>
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
  const name = $(this).find('input[name="name"]').val();

  $.ajax({
    url: '/admin/mcq/add-chapter', 
    type: 'POST',
    data: { name },
    success: function (res) {
      res.success ? showToast('Chapter added.', 'success') : showToast('Chapter couldnt be added', 'danger');
      $('#chaptersModal').modal('hide');

    },
    error: function () {
      showToast('Failed to add chapter.', 'danger');
    }
  });
});


$('#topic-form').on('submit', function (e) {
  e.preventDefault();
  const name = $(this).find('input[name="name"]').val();
  const chapterId = $(this).find('select[name="chapter_id"]').val();

  $.ajax({
    url: '/admin/mcq/add-topic',
    type: 'POST',
    data: { name, chapter_id: chapterId },
    success: function (res) {
       res.success ? showToast('Topic added.', 'success') : showToast('Topic couldnt be added', 'danger');
      $('#topicsModal').modal('hide');
    },
    error: function () {
      showToast('Failed to add topic.', 'danger');
    }
  });
});

JS;
$this->registerJS($js, yii\web\View::POS_END)
  ?>
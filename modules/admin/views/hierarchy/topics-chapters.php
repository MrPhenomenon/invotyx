<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Chapters & Topics Management (Display Only - Editing Disabled)';
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
                    <!-- Open modal without JS -->
                    <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#chaptersModal">
                        <i class="fas fa-plus me-2"></i>New Chapter
                    </button>
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
                            <tbody id="chapter-body">
                                <?php foreach ($chapters as $chapter): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($chapter['name']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger btn-delete" 
                                                    data-id="<?= $chapter['id'] ?>" 
                                                    data-item="chapter '<?= Html::encode($chapter['name']) ?>'" 
                                                    data-url="<?= Url::to(['mcq/delete-chapter']) ?>">
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
                    <!-- Open modal without JS -->
                    <button class="btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#topicsModal">
                        <i class="fas fa-plus me-2"></i>New Topic
                    </button>
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
                            <tbody id="topic-body">
                                <?php foreach ($topics as $topic): ?>
                                    <tr>
                                        <td class="fw-bold"><?= Html::encode($topic['name']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-danger btn-delete" 
                                                    data-id="<?= $topic['id'] ?>" 
                                                    data-item="topic '<?= Html::encode($topic['name']) ?>'" 
                                                    data-url="<?= Url::to(['mcq/delete-topics']) ?>">
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

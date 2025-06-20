<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Team Management</h3>

    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teamModal">
        <i class="bi bi-person-plus-fill"></i> Add Member
        </button>
    </div>
</div>

<!-- Team Table -->
<div class="table-responsive">
    <table class="table table-bordered" id="team-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="team-body">
            <?php foreach ($members as $mem): ?>
                <tr>
                    <td><?= $mem['id'] ?></td>
                    <td><?= $mem['name'] ?></td>
                    <td><?= $mem['role'] ?></td>
                    <td><?= $mem['email'] ?></td>

                    <td>
                        <button class="btn btn-sm btn-primary" data-id="<?= $mem['id'] ?>">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $mem['id'] ?>" data-item="a member <?= $mem['name'] ?>"
                            data-url="<?= Url::to(['default/delete-user']) ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="teamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="team-form" data-url="<?= Url::to(['default/add-management']) ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="" class="form-select">
                            <option value="Support Team">Support Team</option>
                            <option value="Finance Manager">Finance Manager</option>
                            <option value="Content Manager">Content Manager</option>
                            <option value="Super Admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#team-form').on('submit', function(e) {
  e.preventDefault();
  
  const form = new FormData(this);

    $.ajax({
        type: "post",
         url: \$(this).data('url'), 
        data: form,
        processData: false,
    contentType: false,
        success: function (response) {
            response.response == 'success' ? location.reload() : showToast(response.message, 'danger')
        }
    });

});

JS;
$this->registerJS($js, yii\web\View::POS_END)
    ?>
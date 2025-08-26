<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var \yii\web\View $this */
/** @var array $topics */
/** @var array $mcqs */
/** @var \yii\data\Pagination $pagination */

$this->title = 'Manage MCQs';
$this->params['breadcrumbs'][] = $this->title;

$mcqDeleteUrl = Url::to(['mcq/delete-mcq']);
$updateUrl = Url::to(['mcq/update']);

$this->registerCss("
    /* Custom styles for better table readability */
    .table-responsive { overflow-x: visible; }
    .table td, .table th {
        vertical-align: middle;
    }
    .question-text {
        max-width: 350px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }
    .modal-body ul {
        list-style-type: none;
        padding-left: 0;
    }
    .modal-body ul li {
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        margin-bottom: 5px;
        border: 1px solid #dee2e6;
    }
");
?>

<div class="manage-mcqs-page">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-search me-2"></i>Find MCQs</h6>
        </div>
        <div class="card-body">
            <form id="search-form" data-url="<?= Url::to(['mcq/search']) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">Question ID</label><input type="text" name="question_id" class="form-control" placeholder="Enter ID"></div>
                    <div class="col-md-3"><label class="form-label">Topic</label><select name="topic" class="form-select"><option value="">All Topics</option><?php foreach ($topics as $topic): ?><option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option><?php endforeach ?></select></div>
                    <div class="col-md-3"><label class="form-label">Date Range</label><input type="text" name="dates" class="form-control" placeholder="Select a date range"></div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-2"></i>Filter</button>
                            <button type="button" onclick="location.reload()" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table Card -->
    <div class="card shadow-sm border-0" id="results-container">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ul me-2"></i>MCQ List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Question ID</th>
                            <th>Topic</th>
                            <th>Question</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="results-body">
                        <?php foreach ($mcqs as $mcq): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= Html::encode($mcq['question_id']) ?></span></td>
                                <td><?= Html::encode($mcq['topic']['name']) ?></td>
                                <td><span class="question-text" title="<?= Html::encode($mcq['question_text']) ?>"><?= Html::encode($mcq['question_text']) ?></span></td>
                                <td><?= Yii::$app->formatter->asDate($mcq['created_at']) ?></td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item view-details" href="#" data-mcq="<?= htmlspecialchars(json_encode($mcq), ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-eye me-2"></i>Details</a></li>
                                            <li><a class="dropdown-item update-mcq" href="#"><i class="fas fa-edit me-2"></i>Update</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item btn-delete text-danger" href="#" data-id="<?= $mcq['id'] ?>" data-item="MCQ <?= Html::encode($mcq['question_id']) ?>" data-url="<?= $mcqDeleteUrl ?>"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <?= LinkPager::widget(['pagination' => $pagination, 'options' => ['class' => 'pagination justify-content-center mb-0'], 'linkContainerOptions' => ['class' => 'page-item'], 'linkOptions' => ['class' => 'page-link'], 'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link']]); ?>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="mcqModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0"><h5 class="modal-title">MCQ Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body" id="mcq-modal-body"></div>
    </div>
  </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="updateForm">
        <div class="modal-header"><h5 class="modal-title">Update MCQ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="mcq_id" id="mcq-id">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Question ID</label><input type="text" name="mcq_question_id" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Topic</label><select name="mcq_topic_id" class="form-select" required><?php foreach ($topics as $topic): ?><option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option><?php endforeach ?></select></div>
                <div class="col-12"><label class="form-label">Question Text</label><textarea name="mcq_question_text" class="form-control" rows="2" required></textarea></div>
                <div class="col-12"><label class="form-label">Options</label></div>
                <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                    <div class="col-12"><div class="input-group"><span class="input-group-text"><?= strtoupper($opt) ?>.</span><input type="text" name="mcq_option_<?= $opt ?>" class="form-control" required></div></div>
                <?php endforeach; ?>
                <div class="col-md-6"><label class="form-label">Correct Option</label><select name="mcq_correct_option" class="form-select" required><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option></select></div>
                <div class="col-md-6"><label class="form-label">Reference</label><input type="text" name="mcq_reference" class="form-control"></div>
                <div class="col-12"><label class="form-label">Explanation</label><textarea name="mcq_explanation" class="form-control" rows="3"></textarea></div>
            </div>
        </div>
        <div class="modal-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button></div>
      </form>
    </div>
  </div>
</div>

<?php
$js = <<<JS
$(function() {
    // Initialize daterangepicker
    $('input[name="dates"]').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('input[name="dates"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    
    // Delegated click handlers for dynamically added content
    $('#results-body').on('click', '.view-details', function(e) {
        e.preventDefault();
        const mcq = $(this).data('mcq');
        const html = `
            <p><strong>Question ID:</strong> <span class="badge bg-secondary">\${mcq.question_id}</span></p>
            <p><strong>Topic:</strong> \${mcq.topic.name || '—'}</p>
            <hr>
            <p><strong>Question:</strong><br>\${mcq.question_text}</p>
            <p class="mt-3"><strong>Options:</strong>
              <ul>
                <li><strong>A:</strong> \${mcq.option_a}</li>
                <li><strong>B:</strong> \${mcq.option_b}</li>
                <li><strong>C:</strong> \${mcq.option_c}</li>
                <li><strong>D:</strong> \${mcq.option_d}</li>
                <li><strong>E:</strong> \${mcq.option_e}</li>
              </ul>
            </p>
            <p><strong>Correct Option:</strong> <span class="badge bg-success">\${mcq.correct_option.toUpperCase()}</span></p>
            <hr>
            <p><strong>Explanation:</strong><br>\${mcq.explanation || '<em>No explanation provided.</em>'}</p>
            <p><strong>Reference:</strong> \${mcq.reference || '<em>No reference provided.</em>'}</p>
        `;
        $('#mcq-modal-body').html(html);
        new bootstrap.Modal(document.getElementById('mcqModal')).show();
    });

    $('#results-body').on('click', '.update-mcq', function(e) {
        e.preventDefault();
        const mcq = $(this).closest('tr').find('.view-details').data('mcq');
        if (!mcq) return;

        $('#mcq-id').val(mcq.id);
        $('[name="mcq_question_id"]').val(mcq.question_id);
        $('[name="mcq_topic_id"]').val(mcq.topic_id).trigger('change');
        $('[name="mcq_correct_option"]').val(mcq.correct_option.toLowerCase()).trigger('change');
        $('[name="mcq_question_text"]').val(mcq.question_text);
        $('[name="mcq_option_a"]').val(mcq.option_a);
        $('[name="mcq_option_b"]').val(mcq.option_b);
        $('[name="mcq_option_c"]').val(mcq.option_c);
        $('[name="mcq_option_d"]').val(mcq.option_d);
        $('[name="mcq_option_e"]').val(mcq.option_e);
        $('[name="mcq_explanation"]').val(mcq.explanation);
        $('[name="mcq_reference"]').val(mcq.reference);
        
        new bootstrap.Modal(document.getElementById('updateModal')).show();
    });

    // Handle search form submission via AJAX
    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        $.ajax({
            type: "POST",
            url: form.data('url'),
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (response) {
                let html = '';
                if(response.data && response.data.length > 0) {
                    response.data.forEach(mcq => {
                        const mcqJson = JSON.stringify(mcq).replace(/"/g, '"').replace(/'/g, ''');
                        const questionText = mcq.question_text || '';
                        html += `
                            <tr>
                                <td><span class="badge bg-secondary">\${mcq.question_id || ''}</span></td>
                                <td>\${mcq.topic.name || '—'}</td>
                                <td><span class="question-text" title="\${questionText}">\${questionText}</span></td>
                                <td>\${new Date(mcq.created_at).toLocaleDateString()}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Actions</button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item view-details" href="#" data-mcq="\${mcqJson}"><i class="fas fa-eye me-2"></i>Details</a></li>
                                            <li><a class="dropdown-item update-mcq" href="#"><i class="fas fa-edit me-2"></i>Update</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item btn-delete text-danger" href="#" data-id="\${mcq.id}" data-item="MCQ \${mcq.question_id}" data-url="${mcqDeleteUrl}"><i class="fas fa-trash-alt me-2"></i>Delete</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted p-4">No results found for your search criteria.</td></tr>';
                }
                $('#results-body').html(html);
                $('.pagination').hide(); // Hide pagination after an AJAX search
            },
            error: function (xhr) {
                console.log('Error:', xhr.responseText);
                $('#results-body').html('<tr><td colspan="5" class="text-center text-danger p-4">An error occurred while searching.</td></tr>');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-filter me-2"></i>Filter');
            }
        });
    });

    // Update form submission via AJAX
    $('#updateForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "$updateUrl",
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    showToast('MCQ updated successfully', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(response.message || 'Update failed', 'danger');
                }
            },
            error: function () { showToast('Server error', 'danger'); }
        });
    });
});
JS;
$this->registerJS($js, yii\web\View::POS_END);
?>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var \yii\web\View $this */
/** @var array $topics */
/** @var array $subjects */ // Added for new dropdown
/** @var array $organSystems */ // Added for new dropdown
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
    .dropdown-item {
        cursor: pointer; /* Indicate clickable items */
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
            <?php include 'partials/filters.php' ?>
        </div>
    </div>

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
                            <th>Organ System</th>
                            <th>Subject</th>
                            <th>Chapter</th>
                            <th>Topic</th>
                            <th>Question</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                   <tbody id="results-body">
                        <?php include 'partials/table.php' ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <?= LinkPager::widget(['pagination' => $pagination, 'options' => ['class' => 'pagination justify-content-center mb-0'], 'linkContainerOptions' => ['class' => 'page-item'], 'linkOptions' => ['class' => 'page-link'], 'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link']]); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="mcqModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">MCQ Details</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="mcq-modal-body"></div>
        </div>
    </div>
</div>


<?php
include 'partials/update_modal.php';
$js = <<<'JS'

$(function() {
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

    $('#results-body').on('click', '.view-details', function(e) {
        e.preventDefault();

        const $updateBtn = $(this).closest('.dropdown-menu').find('.update-mcq');

        const html = `
            <p><strong>Question ID:</strong> <span class="badge bg-secondary">${$updateBtn.data('question-id') || '—'}</span></p>

            <hr>
            <p><strong>Question:</strong><br>${$updateBtn.data('question-text') || '—'}</p>
            <p class="mt-3"><strong>Options:</strong>
              <ul>
                <li><strong>A:</strong> ${$updateBtn.data('option-a') || '—'}</li>
                <li><strong>B:</strong> ${$updateBtn.data('option-b') || '—'}</li>
                <li><strong>C:</strong> ${$updateBtn.data('option-c') || '—'}</li>
                <li><strong>D:</strong> ${$updateBtn.data('option-d') || '—'}</li>
                <li><strong>E:</strong> ${$updateBtn.data('option-e') || '—'}</li>
              </ul>
            </p>
            <p><strong>Correct Option:</strong> <span class="badge bg-success">${$updateBtn.data('correct-option') || '—'}</span></p>
            <hr>
            <p><strong>Explanation:</strong><br>${$updateBtn.data('explanation') || '<em>No explanation provided.</em>'}</p>
            <p><strong>Reference:</strong> ${$updateBtn.data('reference') || '<em>No reference provided.</em>'}</p>
            <p><strong>Tags:</strong> ${$updateBtn.data('tags') || '<em>No tags provided.</em>'}</p>
        `;

        $('#mcq-modal-body').html(html);
        new bootstrap.Modal(document.getElementById('mcqModal')).show();
    });


    $('#results-body').on('click', '.update-mcq', function(e) {
        e.preventDefault();
      
        const btn = $(this);
        $('#mcq-id').val(btn.data('mcq-id'));
        $('[name="mcq_question_id"]').val(btn.data('question-id'));
        $('[name="mcq_topic_id"]').val(btn.data('topic-id')).trigger('change');
        $('[name="mcq_chapter_id"]').val(btn.data('chapter-id')).trigger('change');
        $('[name="mcq_subject_id"]').val(btn.data('subject-id')).trigger('change'); 
        $('[name="mcq_organ_system_id"]').val(btn.data('organ-system-id')).trigger('change');
        $('[name="mcq_correct_option"]').val(btn.data('correct-option').toLowerCase()).trigger('change');
        $('[name="mcq_question_text"]').val(btn.data('question-text'));
        $('[name="mcq_option_a"]').val(btn.data('option-a'));
        $('[name="mcq_option_b"]').val(btn.data('option-b'));
        $('[name="mcq_option_c"]').val(btn.data('option-c'));
        $('[name="mcq_option_d"]').val(btn.data('option-d'));
        $('[name="mcq_option_e"]').val(btn.data('option-e'));
        $('[name="mcq_explanation"]').val(btn.data('explanation'));
        $('[name="mcq_reference"]').val(btn.data('reference'));
        $('[name="mcq_tags"]').val(btn.data('tags'));
        
        new bootstrap.Modal(document.getElementById('updateModal')).show();
    });

    $('#search-form').on('submit', function() {
    $(this).find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Searching...');
});

    $('#updateForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: $(this).serialize(),
            success: function (response) {
                if (response.success) {
                    showToast(response.message || 'MCQ updated successfully', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(response.message || 'Update failed', 'danger');
                }
            },
            error: function (xhr) { 
                showToast('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText), 'danger');
                console.log('Update Error:', xhr.responseText);
            }
        });
    });
});
JS;
$this->registerJS($js, yii\web\View::POS_END);
?>
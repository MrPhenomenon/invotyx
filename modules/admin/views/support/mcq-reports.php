<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$this->title = 'Reported MCQs';
$this->params['breadcrumbs'][] = $this->title;
$url = Url::to(['support/mark-as-solved']);
$js = <<<JS
const confirmMarkSolvedModal = $('#confirmMarkSolvedModal');
const modalReportIdSpan = $('#modal-report-id');
const modalConfirmButton = $('#confirm-mark-solved-button');
let currentReportButton = null;

$('.mark-as-solved-btn').on('click', function(e) {
    e.preventDefault();
    currentReportButton = $(this);
    const reportId = currentReportButton.data('report-id');
    const mcqId = currentReportButton.data('mcq-id');

    modalReportIdSpan.text(reportId);
    $('#modal-mcq-id').text(mcqId);
    confirmMarkSolvedModal.modal('show');
});

modalConfirmButton.on('click', function() {
    if (!currentReportButton) return;

    confirmMarkSolvedModal.modal('hide');

    const url = '$url' + '?id=' + currentReportButton.data('report-id');

    $.ajax({
        url: url,
        type: 'GET',
        data: {
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',
        beforeSend: function() {
            currentReportButton.prop('disabled', true).text('Processing...');
        },
        success: function(response) {
            if (response.success) {
                currentReportButton.closest('.report-card').find('.report-status').html('<span class="badge bg-success text-white">solved</span>');
                currentReportButton.prop('disabled', true).removeClass('btn-warning').addClass('btn-secondary').text('Solved');
                showToast(response.message, 'success');
            } else {
                if (currentReportButton.closest('.report-card').find('.report-status span').text() !== 'solved') {
                    currentReportButton.prop('disabled', false).text('Mark as Solved');
                }
               showToast(response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            if (currentReportButton.closest('.report-card').find('.report-status span').text() !== 'solved') {
                currentReportButton.prop('disabled', false).text('Mark as Solved');
            }
            showToast('An error occurred: ' + (xhr.responseJSON ? xhr.responseJSON.message : error), 'danger');
        }
    });
});

JS;
$this->registerJs($js);

?>
<div class="reports-custom-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert-container">
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->session->hasFlash('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= Yii::$app->session->getFlash('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>


    <?php if (empty($reports)): ?>
        <div class="alert alert-info">
            No reports found.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($reports as $report): ?>
                <div class="col-md-6 mb-4">
                    <div class="card report-card h-100 shadow-sm">
                        <div class="card-header text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Report #<?= Html::encode($report->id) ?></h5>
                            <span class="report-status">
                                <?php
                                $badgeClass = '';
                                switch ($report->status) {
                                    case 'pending':
                                        $badgeClass = 'bg-warning text-dark';
                                        break;
                                    case 'solved':
                                        $badgeClass = 'bg-success text-white';
                                        break;
                                    case 'dismissed':
                                        $badgeClass = 'bg-danger text-white';
                                        break;
                                    default:
                                        $badgeClass = 'bg-info text-white';
                                        break;
                                }
                                echo Html::tag('span', Html::encode($report->status), ['class' => "badge {$badgeClass}"]);
                                ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <p class="card-text mb-1">
                                <strong>MCQ ID:</strong> <?= Html::encode($report->mcq_id) ?>
                            </p>
                            <p class="card-text mb-1">
                                <strong>Reported By:</strong>
                                <?php if ($report->user): ?>
                                    <?= Html::encode($report->user->email) ?>
                                <?php else: ?>
                                    <span class="text-muted">User Not Found (ID: <?= Html::encode($report->reported_by) ?>)</span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text text-muted small mb-2">
                                Reported at <?= Yii::$app->formatter->asDatetime($report->reported_at) ?>
                            </p>
                            <hr>
                            <h6 class="card-subtitle mb-2 text-muted">Message:</h6>
                            <p class="card-text mb-3"><?= nl2br(Html::encode($report->message)) ?></p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center bg-light">
                            <?= Html::a(
                                '<i class="glyphicon glyphicon-edit"></i> Go to MCQ',
                                Url::to(['mcq/manage', 'question_id' => $report->mcq_id]),
                                ['class' => 'btn btn-info btn-sm', 'target' => '_blank', 'title' => 'Go to MCQ to fix it']
                            ) ?>

                            <?= Html::button(
                                ($report->status === 'solved' ? '<i class="glyphicon glyphicon-ok"></i> Solved' : '<i class="glyphicon glyphicon-check"></i> Mark as Solved'),
                                [
                                    'class' => 'btn btn-sm mark-as-solved-btn ' . ($report->status === 'solved' ? 'btn-secondary' : 'btn-warning'),
                                    'data-report-id' => $report->id,
                                    'data-mcq-id' => $report->mcq_id,
                                    'title' => 'Mark this report as solved',
                                    'disabled' => ($report->status === 'solved')
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<div class="modal fade" id="confirmMarkSolvedModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Confirm Action</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>
          Are you sure you want to mark Report #<strong id="modal-report-id"></strong> 
          (MCQ ID: <strong id="modal-mcq-id"></strong>) as solved?
        </p>
        <p class="text-muted">This action cannot be undone.</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirm-mark-solved-button">
          Yes, Mark as Solved
        </button>
      </div>

    </div>
  </div>
</div>

<style>
    .reports-custom-list h1 {
        margin-bottom: 30px;
    }
    .report-card {
        border: 1px solid #e0e0e0;
        transition: transform 0.2s ease-in-out;
    }
    .report-card .card-header {
        font-size: 1.1rem;
    }
    .report-card .card-body {
        padding-top: 20px;
        padding-bottom: 10px;
    }
    .report-card .card-footer {
        padding-top: 15px;
        padding-bottom: 15px;
        border-top: 1px solid #eee;
    }
    .report-card .badge {
        font-size: 0.8em;
        padding: 0.4em 0.7em;
        border-radius: .25rem;
    }
    .report-card .btn {
        min-width: 130px;
        text-align: center;
    }
    .report-card .btn i {
        margin-right: 5px;
    }

    @media (max-width: 767px) {
        .report-card .card-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .report-card .card-footer .btn {
            margin-bottom: 10px;
        }
        .report-card .card-footer .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>
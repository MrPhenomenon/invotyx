<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $userId int */

$this->title = 'My Bookmarked Questions';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerCss(<<<CSS
    .bookmarked-item {
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        padding: 1.5rem;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .bookmarked-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .bookmarked-question-text {
        font-size: 1.15rem;
        font-weight: 500;
        color: #343a40;
        margin-bottom: 1rem;
    }
    .bookmarked-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .btn-unbookmark {
        transition: color 0.2s ease-in-out;
    }

    .bookmarked-actions {
        margin-top: 1rem;
        border-top: 1px dashed #e9ecef;
        padding-top: 1rem;
    }

    .bookmarked-options .list-group-item {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        padding: 0.75rem 1rem;
        color: #495057;
    }
    .bookmarked-options .list-group-item.correct-option {
        background-color: #d4edda; /* Light green */
        border-color: #28a745; /* Green */
        color: #155724;
        font-weight: 600;
    }
CSS);

// For the AJAX call to toggle bookmark
$toggleBookmarkUrl = Url::to(['exam/toggle-bookmark']); // If actionToggleBookmark is in ExamController
// $toggleBookmarkUrl = Url::to(['bookmarked/toggle-bookmark']); // If you move it to BookmarkedController

$js = <<<JS
$(document).on('click', '.btn-unbookmark', function(e) {
    e.preventDefault(); // Prevent default button action
    const button = $(this);
    const mcqId = button.data('mcq-id');
    const csrfToken = yii.getCsrfToken();

    if (!confirm('Are you sure you want to remove this bookmark?')) {
        return;
    }

    button.prop('disabled', true).addClass('disabled');

    $.post('$toggleBookmarkUrl', { mcq_id: mcqId, _csrf: csrfToken })
        .done(function(res) {
            if (res.success && res.action === 'removed') {
                button.closest('.bookmarked-item').fadeOut(400, function() {
                    $(this).remove();
                    if ($('.bookmarked-item').length === 0) {
                        $('#bookmarked-list-container').html('<div class="alert alert-info text-center py-4 rounded-3 shadow-sm">You haven\'t bookmarked any questions yet.</div>');
                    }
                });
                showToast(res.message, 'info');
            } else {
                showToast(res.message || 'Failed to remove bookmark.', 'danger');
                button.prop('disabled', false).removeClass('disabled');
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            showToast('AJAX Error: ' + (jqXHR.responseJSON ? jqXHR.responseJSON.message : errorThrown), 'danger');
            button.prop('disabled', false).removeClass('disabled');
        });
});

JS;
$this->registerJS($js, yii\web\View::POS_END);
?>

<div class="container py-4">
    <h3 class="mb-4 text-primary"><?= Html::encode($this->title) ?></h3>

    <div id="bookmarked-list-container">
        <?php if ($dataProvider->getCount() > 0): ?>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_bookmarked_item',
                'options' => [
                    'tag' => 'div',
                    'class' => 'list-view',
                    'id' => 'bookmarked-list',
                ],
                'itemOptions' => [
                    'tag' => false,
                ],
                'summary' => '<div class="text-muted mb-3">{begin}-{end} of {totalCount} questions</div>',
                'emptyText' => '<div class="alert alert-info text-center py-4 rounded-3 shadow-sm">You haven\'t bookmarked any questions yet.</div>',
            ]); ?>
        <?php else: ?>
            <div class="alert alert-info text-center py-4 rounded-3 shadow-sm">
                You haven't bookmarked any questions yet.
            </div>
        <?php endif; ?>
    </div>
</div>
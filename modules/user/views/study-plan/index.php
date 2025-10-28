<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;

/** @var \yii\data\ActiveDataProvider $dataProvider */
/** @var string $today */
/** @var string $planStartDate */

$this->title = 'Your Personalized Study Plan';

$this->registerCss("
.day-entry {
    background-color: #ffffff;
    border: 1px solid #e0e0e0;
    border-left: 4px solid;
    margin-bottom: 1.5rem;
    border-radius: .35rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    transition: all 0.2s ease-in-out;
}
.day-entry:hover {
    box-shadow: 0 3px 8px rgba(0,0,0,.12);
    transform: translateY(-2px);
}

.status-completed { border-left-color: #28a745; }
.status-skipped { border-left-color: #dc3545; }
.status-upcoming { border-left-color: #ced4da; }
.status-revision { border-left-color: #ffc107; }
.status-mock { border-left-color: #6c757d; } 

.status-current {
    border: 2px solid var(--primary-color); 
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.25);
    position: relative;
    z-index: 10;
}
.status-current .day-header {
    background-color: #e6f0ff !important;
}
.status-current:hover {
    box-shadow: 0 0 20px rgba(0, 123, 255, 0.4);
    transform: translateY(-3px);
}


.day-header {
    padding: 1rem 1.5rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: .35rem .35rem 0 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    color: #343a40;
}

.day-header h5 {
    font-size: 1.15rem;
    font-weight: 600;
    color: #343a40;
}
.day-header small {
    color: #6c757d;
}
.day-header .badge {
    font-size: 0.75rem;
    padding: .3em .6em;
    font-weight: 500;
}
.day-header .badge.bg-info { background-color: #e0f2ff !important; color: var(--primary-color) !important; } 
.day-header .badge.bg-secondary { background-color: #e9ecef !important; color: #495057 !important; }

.day-header .status-indicator-badge {
    background-color: #6c757d;
    color: #fff;
    font-weight: 500;
    min-width: 70px;
    text-align: center;
    margin-left: 0.5rem;
}
.day-header .status-indicator-badge.bg-success { background-color: #28a745 !important; }
.day-header .status-indicator-badge.bg-danger { background-color: #dc3545 !important; }
.day-header .status-indicator-badge.bg-primary { background-color:  var(--primary-color) !important; }
.day-header .status-indicator-badge.bg-warning { background-color: #ffc107 !important; color: #343a40 !important; }


.day-body {
    padding: 1rem 1.5rem;
}


.subject-group {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px dashed #f2f2f2;
}
.subject-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.chapter-heading-link {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .75rem 0;
    text-decoration: none;
    color: #343a40;
    font-weight: 500;
    transition: color 0.15s ease-in-out;
    border-bottom: 1px solid #f8f9fa;
}
.chapter-heading-link:hover {
    color:  var(--primary-color);
}
.chapter-heading-link .badge {
    min-width: 55px;
    text-align: center;
    background-color: #e9ecef;
    color: #495057;
}

.topic-list-container {
    background-color: #fcfcfc;
    border-top: 1px solid #f2f2f2;
    padding: .75rem 1.25rem .25rem;
    margin-left: -1.5rem; 
    margin-right: -1.5rem;
    border-radius: 0 0 .35rem .35rem;
}
.topic-list-container.show {
    border-top: 1px solid #e9ecef;
}

.topic-item {
    padding: .25rem 0;
    color: #6c757d;
    font-size: .85em;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.topic-item .badge {
    background-color: #e0f0e0;
    color: #28a745;
    font-weight: 500;
}

.badge.bg-purple-subtle {
    background-color: #f2e6ff !important;
    color: #6f42c1 !important;
}

.pagination .page-item .page-link {
    border-radius: .25rem;
    margin: 0 .25rem;
    color:  var(--primary-color);
    border-color: #dee2e6;
}
.pagination .page-item.active .page-link {
    background-color:  var(--primary-color);
    border-color:  var(--primary-color);
    color: #fff;
}
.pagination .page-item.disabled .page-link {
    color: #adb5bd;
}
");
?>

<div class="study-plan-index py-4">
    <div class="study-plan-container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="text-primary fw-bold mb-0"><?= Html::encode($this->title) ?></h2>
            <div>
                <a href="<?= Url::current(['page' => $calculatedTodayPage]) ?>" class="btn btn-outline-primary" id="go-to-today-btn">
                    <i class="fas fa-calendar-day me-2"></i>Go to Today
                </a>
            </div>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger mb-0 ms-auto">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ($dataProvider->getModels() as $day):

            $dayStatus = 'upcoming';
            $borderClass = 'status-upcoming'; 
            $dayHeaderClasses = ['day-header'];

            $planDate = strtotime($day->plan_date);
            $todayTimestamp = strtotime($today);
            $daysToExam = (new DateTime($day->studyPlan->exam_date))->diff(new DateTime($day->plan_date))->days;

            if ($planDate === $todayTimestamp) {
                $dayStatus = 'current';
                $borderClass = 'status-current';
            }
        ?>
          <div class="day-entry status-<?= Html::encode($day->status) ?> <?= ($dayStatus === 'current' ? 'active-today' : '') ?>"
            <?= ($dayStatus === 'current' ? 'id="today-entry"' : '') ?>>
                <div class="day-header <?= ($dayStatus === 'current' ? 'bg-status-current-tint' : '') ?>">
                    <div>
                        <h5 class="mb-0">
                            <?= Html::encode(date('D, M j, Y', strtotime($day->plan_date))) ?>
                        </h5>
                        <small class="text-muted">Day <?= Html::encode($day->day_number) ?></small>
                    </div>
                    <div class="text-end">
                        <div class="mb-1">
                            <span class="badge bg-info me-1">New: <?= Html::encode($day->new_mcqs) ?></span>
                            <?php if ($day->review_mcqs > 0): ?>
                                <span class="badge bg-secondary">Review: <?= Html::encode($day->review_mcqs) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php
                            $statusBadgeText = '';
                            $statusBadgeClass = 'status-indicator-badge';
                            if ($dayStatus === 'current') {
                                $statusBadgeText = 'Today';
                                $statusBadgeClass .= ' bg-primary';
                            } elseif ($day->status === 'completed') {
                                $statusBadgeText = 'Completed';
                                $statusBadgeClass .= ' bg-success';
                            } elseif ($day->status === 'skipped') {
                                $statusBadgeText = 'Skipped';
                                $statusBadgeClass .= ' bg-danger';
                            }
                             elseif ($day->status === 'upcoming') {
                                $statusBadgeText = 'Upcoming';
                                $statusBadgeClass .= ' bg-secondary';
                            }
                        ?>
                        <?php if ($day->is_mock_exam): ?>
                            <span class="badge bg-purple-subtle ms-1">Mock Exam</span>
                        <?php else: ?>
                            <span class="badge <?= Html::encode($statusBadgeClass) ?>"><?= Html::encode($statusBadgeText) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="day-body">
                    <?php
                    $groupedSubjects = [];
                    foreach ($day->getStudyPlanDaySubjects()->with(['subject', 'chapter', 'topic'])->all() as $subjectAlloc) {
                        $subjectName = $subjectAlloc->subject->name ?? 'Unknown Subject';
                        $chapterName = $subjectAlloc->chapter->name ?? 'Unknown Chapter';
                        $groupedSubjects[$subjectName][$chapterName][] = $subjectAlloc;
                    }
                    ?>

                    <?php if (!empty($groupedSubjects)): ?>
                        <?php foreach ($groupedSubjects as $subjectName => $chapters): ?>
                            <div class="subject-group">
                                <h6 class="text-dark mb-2 fw-semibold"><?= Html::encode($subjectName) ?></h6>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($chapters as $chapterName => $allocations): ?>
                                        <?php
                                        $totalMcqsForChapter = array_sum(array_column($allocations, 'allocated_mcqs'));
                                        $collapseId = 'collapse-' . $day->id . '-' . md5($subjectName . $chapterName);
                                        ?>
                                        <div class="list-group-item bg-transparent px-0">
                                            <a class="chapter-heading-link" data-bs-toggle="collapse"
                                                href="#<?= $collapseId ?>" role="button" aria-expanded="false"
                                                aria-controls="<?= $collapseId ?>">
                                                <span><?= Html::encode($chapterName) ?></span>
                                                <span class="badge"><?= $totalMcqsForChapter ?> MCQs</span>
                                            </a>
                                            <div class="collapse topic-list-container" id="<?= $collapseId ?>">
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($allocations as $allocation): ?>
                                                        <li class="topic-item">
                                                            <span><em><?= Html::encode($allocation->topic->name ?? 'Unknown Topic') ?></em></span>
                                                            <span class="badge"><?= Html::encode($allocation->allocated_mcqs) ?> MCQs</span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">No specific content allocated for this day.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-center mt-5">
             <?= LinkPager::widget(['pagination' => $dataProvider->pagination, 'options' => ['class' => 'pagination justify-content-center mb-0'], 'linkContainerOptions' => ['class' => 'page-item'], 'linkOptions' => ['class' => 'page-link'], 'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link']]); ?>
        </div>
    </div>
</div>


<?php $this->registerJs("
$(document).ready(function() {
    var todayEntry = $('#today-entry');
    if (todayEntry.length) {
        $('html, body').animate({
            scrollTop: todayEntry.offset().top - 100 // -100px for some padding from top
        }, 500);
    }
});
"); ?>
<?php

use app\models\StudyPlanDays;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'My Dashboard';
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

$correct = $overallStats['correctlyAnswered'] ?? 0;
$incorrect = $overallStats['incorrectlyAnswered'] ?? 0;
$total = $correct + $incorrect;
$accuracyPercent = $total > 0 ? round(($correct / $total) * 100, 1) : 0;

$this->registerCss(<<<CSS
    :root {
        --primary-site-color: #0e273c;
        --primary-light-color: #1a416a;
        --percentage: {$accuracyPercent}%;
    }
    body {
        background-color: #f0f2f5; 
        font-family: 'Inter', sans-serif;
    }
    .dashboard-header {
       background: 
  linear-gradient(135deg, rgba(255,255,255,0.05), rgba(0,0,0,0.2)),
  linear-gradient(135deg, #0B1F30 0%, #123C7A 60%, #1E6DE8 100%);

        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .dashboard-header h1 {
        font-weight: 700;
    }
    .dashboard-card {
         background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;

        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: none;
    }
    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.6;
        margin-bottom: 0.75rem;
    }
    .stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #343a40;
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .section-title {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    .list-group-item.exam-item {
        border-radius: 8px;
        margin-bottom: 0.75rem;
        border: 1px solid #f0f2f5;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        transition: background-color 0.2s, box-shadow 0.2s;
    }
    .list-group-item.exam-item:hover {
        background-color: #f0f2f5;
        box-shadow: 0 4px 8px rgba(0,0,0,0.06);
    }
    .btn-action-lg {
        font-size: 1.15rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.6rem;
        font-weight: 600;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #f0f2f5;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .profile-placeholder {
        background-color: #e9ecef;
        color: #adb5bd;
        font-size: 4rem;
        width: 120px;
        height: 120px;
        border: 4px solid #f0f2f5;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .progress-circle {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--bs-primary);
        font-size: 1rem;
        overflow: hidden;
        margin: 0 auto;
        box-shadow: inset 0 0 8px rgba(0,0,0,0.05);
    }

    .progress-circle::before, .progress-circle::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        box-sizing: border-box;
    }

    .progress-circle::before {
        background: conic-gradient(var(--bs-success) var(--percentage), #e9ecef var(--percentage));
        transform: rotate(-90deg);
    }

    .progress-circle::after {
        background-color: white;
        width: 85px;
        height: 85px;
        top: 7.5px;
        left: 7.5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .progress-circle-text {
        position: relative;
        z-index: 1;
        color: #343a40;
    }

    .card-quick-actions {
       background: 
  linear-gradient(135deg, rgba(255,255,255,0.05), rgba(0,0,0,0.2)),
  linear-gradient(135deg, #0B1F30 0%, #123C7A 60%, #1E6DE8 100%); 
        color: white;
        text-align: center;
        padding: 2rem;
        border-radius: 1rem;
    }
    .card-quick-actions .card-title {
        color: white;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }
    .card-quick-actions .btn {
        font-size: 1.2rem;
        padding: 1rem 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-quick-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    }
    .card-quick-actions .btn-start-exam {
        background-color: #fff;
        color: var(--primary-site-color);
    }
    .card-quick-actions .btn-review-exams {
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--primary-site-color);
    }
    .card-quick-actions .btn-start-exam:hover {
        background-color: #e6e6e6;
    }
    .card-quick-actions .btn-review-exams:hover {
        background-color: #e6e6e6;
    }
    .card-quick-actions .d-grid .btn {
        width: 100%;
    }

    .profile-card-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .profile-card-subscription-status .status-text {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .profile-card-subscription-status .status-text.text-success { color: var(--bs-success)!important; }
    .profile-card-subscription-status .status-text.text-danger { color: var(--bs-danger)!important; }

CSS);
?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="dashboard-header text-center text-md-start">
                <div class="row g-3 align-items-center justify-content-center">

                    <div class="col-md-6 text-center">
                        <h1 class="mb-2 text-white">Welcome, <?= Html::encode($user->name) ?>!</h1>
                        <p class="lead text-light mb-0">Your personalized learning journey starts here.</p>
                    </div>

                    <div class="col-md-6 text-center">
                        <?php if ($user->expected_exam_date): ?>
                            <?php
                            $today = new DateTime();
                            $examDate = new DateTime($user->expected_exam_date);
                            $daysLeft = $today->diff($examDate)->days;
                            ?>
                            <h6 class="fw-bold text-white mb-2">Exam Countdown</h6>
                            <div class="display-6 fw-bold text-warning"><?= $daysLeft ?></div>
                            <p class="mb-0 text-light">Days left until
                                <?= Yii::$app->formatter->asDate($user->expected_exam_date) ?>
                            </p>
                        <?php else: ?>
                            <h6 class="fw-bold text-white mb-2">Exam Countdown</h6>
                            <p class="mb-0 text-light">No exam date set</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="container py-4">

    <div class="row g-4 mb-5">

        <div id="accuracy-chart" class="col-md-6 col-lg-6">
            <div class="card dashboard-card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3 fw-bold text-dark">Accuracy Trend</h5>
                    <div id="accuracyTrendChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div id="quick-actions" class="card h-100 shadow-sm dashboard-card">
                <div class="card-body d-flex flex-column py-4 px-4">

                    <h4 class="fw-semibold text-primary mb-4">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Quick Actions
                    </h4>

                    <?php if ($studyPlanDayToday): ?>
                        <div class="p-3 mb-4 bg-light border rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    Today’s Study Plan
                                    <small class="text-muted">(Day
                                        <?= Html::encode($studyPlanDayToday->day_number) ?>)</small>
                                </h6>
                            </div>

                            <p class="mb-2">
                                <strong>Total MCQs:</strong>
                                <?php if ($studyPlanDayToday->is_mock_exam): ?>
                                    <span class="badge bg-info text-white">Mock Exam</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">
                                        <?= $studyPlanDayToday->new_mcqs + $studyPlanDayToday->review_mcqs + $studyPlanDayToday->redistributed_skipped_mcqs ?>
                                    </span>
                                <?php endif; ?>
                            </p>

                            <?php if (!empty($groupedSubjects)): ?>
                                <div class="border-top pt-2">
                                    <h6 class="fw-semibold text-secondary mb-2">Content Breakdown</h6>
                                    <ul class="list-unstyled small mb-0">
                                        <?php foreach ($groupedSubjects as $subjectName => $subjectData): ?>
                                            <li class="mb-1">
                                                <span class="fw-semibold"><?= Html::encode($subjectName) ?></span>
                                                <span class="badge bg-primary ms-2"><?= $subjectData['total_mcqs'] ?> MCQs</span>
                                                <ul class="list-unstyled ms-3 text-muted small">
                                                    <?php foreach ($subjectData['chapters'] as $chapterName => $chapterMcqs): ?>
                                                        <li><?= Html::encode($chapterName) ?>
                                                            <span class="badge bg-light text-dark"><?= $chapterMcqs ?></span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="row pt-2">
                                <?php if ($currentExamSession):
                                    $resumeUrl = $studyPlanDayToday->is_mock_exam == 1 ? Url::to(['mock-exam/take', 'session' => $currentExamSession->id]) : Url::to(['mcq/start', 'session_id' => $currentExamSession->id]);
                                    ?>
                                    <div class="col-12">
                                        <?= Html::a(
                                            '<i class="fas fa-play me-2"></i>Resume Today\'s Exam',
                                            $resumeUrl,
                                            ['class' => 'btn btn-success w-100', 'title' => 'Resume Today\'s Exam']
                                        )
                                            ?>
                                    </div>
                                <?php elseif ($studyPlanDayToday->status == StudyPlanDays::STATUS_COMPLETED): ?>
                                    <div class="col-12">
                                        <?= Html::a(
                                            '<i class="fas fa-check me-2"></i>Today\'s Exam Completed',
                                            ['/'],
                                            ['class' => 'btn btn-success w-100 disabled']
                                        )
                                            ?>
                                    </div>
                                <?php elseif ($studyPlanDayToday->is_mock_exam): ?>
                                    <div class="col-12">
                                        <?= Html::a(
                                            '<i class="fas fa-play me-2"></i>Start Mock Exam',
                                            ['exam/start-mock'],
                                            ['class' => 'btn btn-outline-primary w-100']
                                        )
                                            ?>
                                    </div>
                                <?php else: ?>

                                    <div class="col-12 col-lg-6">
                                        <?= Html::a(
                                            '<i class="fas fa-play me-2"></i>Start in Practice Mode',
                                            ['exam/start-study-plan-exam', 'mode' => 'practice'],
                                            ['class' => 'btn btn-outline-primary w-100', 'title' => 'Immediate feedback on each question']
                                        )
                                            ?>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <?= Html::a(
                                            '<i class="fas fa-play me-2"></i>Start in Test Mode',
                                            ['exam/start-study-plan-exam', 'mode' => 'test'],
                                            ['class' => 'btn btn-outline-primary w-100', 'title' => 'Feedback at the end of the exam']
                                        )
                                            ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>


                    <div class="mt-auto d-grid gap-2">
                        <?= Html::a('<i class="fas fa-plus-circle me-2"></i>Start New Exam', ['exam/'], ['class' => 'btn btn-outline-primary btn-lg']) ?>
                        <?= Html::a('<i class="fas fa-list-alt me-2"></i>Review Past Exams', ['results/'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div id="performance">
        <h2 class="section-title text-dark mb-4 mt-5">Your Performance at a Glance</h2>
        <div class="row g-4 mb-5 text-center">
            <div class="col-sm-6 col-md-3">
                <div class="card dashboard-card h-100 shadow-sm bg-light">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="fas fa-lightbulb stat-icon text-info"></i>
                        <h4 class="stat-value"><?= $overallStats['totalAttempted'] ?? 0 ?></h4>
                        <p class="stat-label">Questions Attempted</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card dashboard-card h-100 shadow-sm bg-light">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="fas fa-check-circle stat-icon text-success"></i>
                        <h4 class="stat-value"><?= $correct ?></h4>
                        <p class="stat-label">Correct Answers</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card dashboard-card h-100 shadow-sm bg-light">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <i class="fas fa-times-circle stat-icon text-danger"></i>
                        <h4 class="stat-value"><?= $incorrect ?? 0 ?></h4>
                        <p class="stat-label">Incorrect Answers</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card dashboard-card h-100 shadow-sm bg-light">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="progress-circle mb-2">
                            <span class="progress-circle-text"><?= $accuracyPercent ?>%</span>
                        </div>
                        <p class="stat-label">Accuracy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="exam-overview">
        <div class="row g-4">
            <div class="col-lg-6">
                <h3 class="section-title text-dark mb-4">Your Active Exams</h3>
                <?php if (!empty($ongoingExams)): ?>
                    <div class="list-group">
                        <?php foreach ($ongoingExams as $exam): ?>
                            <div class="list-group-item exam-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">
                                        <i class="fas fa-history text-warning me-2"></i>
                                        <?= $exam->getName() ?>
                                    </h6>
                                    <p class="mb-0 small text-muted">
                                        Started: <?= Yii::$app->formatter->asRelativeTime($exam->start_time) ?>
                                    </p>
                                    <?php
                                    $totalQs = count(json_decode($exam->mcq_ids, true));
                                    $cacheKey = 'exam_state_' . Yii::$app->user->id . '_' . $exam->id;
                                    $examData = Yii::$app->cache->get($cacheKey);
                                    $answeredCount = count($examData['responses'] ?? []);
                                    $skippedCount = count($examData['skipped_mcq_ids'] ?? []);
                                    $currentProgress = ($totalQs > 0) ? round((($answeredCount + $skippedCount) / $totalQs) * 100) : 0;
                                    ?>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: <?= $currentProgress ?>%;" aria-valuenow="<?= $currentProgress ?>"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small text-muted">Progress: <?= $currentProgress ?>% done</span>
                                </div>
                                <?= Html::a('<i class="fas fa-play me-1"></i> Resume', ['mcq/start', 'session_id' => $exam->id], ['class' => 'btn btn-sm btn-warning']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center py-4 rounded-3 shadow-sm">
                        Looks like you don't have any active exams. <br>
                        <?= Html::a('Start a New Exam now!', ['exam/'], ['class' => 'alert-link mt-2 d-inline-block']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-6">
                <h3 class="section-title text-dark mb-4">Recent Completed Exams</h3>
                <?php if (!empty($recentExams)): ?>
                    <div class="list-group">
                        <?php foreach ($recentExams as $exam): ?>
                            <div class="list-group-item exam-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">
                                        <i class="fas fa-medal text-success me-2"></i>
                                        <?= $exam->getName() ?>
                                    </h6>
                                    <p class="mb-0 small text-muted">
                                        Completed: <?= Yii::$app->formatter->asRelativeTime($exam->end_time) ?> | Accuracy:
                                        <span
                                            class="fw-bold text-<?= $exam->accuracy >= 70 ? 'success' : ($exam->accuracy >= 50 ? 'warning' : 'danger') ?>"><?= round($exam->accuracy, 1) ?>%</span>
                                    </p>
                                </div>
                                <?= Html::a('<i class="fas fa-eye me-1"></i>Result', ['results/view', 'id' => $exam->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center py-4 rounded-3 shadow-sm">
                        No completed exams found yet. <br>
                        <?= Html::a('View your achievements here!', ['results/'], ['class' => 'alert-link mt-2 d-inline-block']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = '';
if (Yii::$app->session->hasFlash('ask_tutorial') && Yii::$app->session->hasFlash('show_notice')) {
    $js = 'showNoticeModal(function() { showAskTutorialModal(); });';
} elseif (Yii::$app->session->hasFlash('ask_tutorial')) {
    $js = 'showAskTutorialModal();';
} elseif (Yii::$app->session->hasFlash('show_notice')) {
    $js = 'showNoticeModal();';
}
if ($js) {
    $this->registerJs($js);
}
?>


<?php
$trendLabels = json_encode(array_map(fn($row) => date('M d', strtotime($row['end_time'])), $accuracyTrend));
$trendData = json_encode(array_map(fn($row) => round((float) $row['accuracy']), $accuracyTrend));

$js = <<<JS
var options = {
    chart: {
        type: 'line',
        height: 300,
        toolbar: {
    show: true,
    tools: { zoom: true, pan: true, reset: true, download: false},
    zoom: {
        enabled: true,
        type: 'x',
        autoScaleYaxis: true
    }
  },
    },
    series: [{
        name: 'Accuracy %',
        data: $trendData
    }],
    xaxis: {
        categories: $trendLabels,
        title: { text: 'Date' },
         tickAmount: 10,
    },
    yaxis: {
        min: 0,
        max: 100,
        title: { text: 'Accuracy %' }
    },
    stroke: { curve: 'smooth' },
    markers: { size: 4 },
    colors: ['#0d6efd']
};

var chart = new ApexCharts(document.querySelector("#accuracyTrendChart"), options);
chart.render();
JS;

$this->registerJs($js);
?>
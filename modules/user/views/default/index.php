<?php

use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'My Dashboard';
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

$overallAccuracyPercentage = $overallStats['overallAccuracy'] ?? 0;

$this->registerCss(<<<CSS
    :root {
        --primary-site-color: #0e273c;
        --primary-light-color: #1a416a;
        --percentage: {$overallAccuracyPercentage}%;
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

    .profile-card-section {
        border-bottom: 1px dashed #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
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
            <div class="dashboard-header text-center">
                <h1 class="mb-2 text-white">Welcome, <?= Html::encode($user->name) ?>!</h1>
                <p class="lead">Your personalized learning journey starts here.</p>
            </div>
        </div>
    </div>
</div>
<div class="container py-4">

    <div class="row g-4 mb-5">

        <div class="col-md-6 col-lg-6">
            <div class="card dashboard-card h-100 shadow-sm">
                <div class="card-body">

                    <div class="text-center mb-4">
                        <?php if ($user->profile_picture): ?>
                            <?= Html::img($user->profile_picture, ['alt' => 'Profile Picture', 'class' => 'rounded-circle profile-img mb-3']) ?>
                        <?php else: ?>
                            <div
                                class="profile-placeholder rounded-circle d-inline-flex align-items-center justify-content-center mb-3">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="card-title mb-1 fw-bold text-dark"><?= Html::encode($user->name) ?></h5>
                        <p class="card-text text-muted small"><?= Html::encode($user->email) ?></p>
                    </div>

                    <div class="row g-3 border-top pt-4">
                        <div class="col-md-6 profile-card-section text-center ps-md-4">
                            <h6 class="fw-bold text-dark mb-2">Exam Details:</h6>
                            <?php if ($user->speciality): ?>
                                <p class="card-text mb-1"><i
                                        class="fas fa-stethoscope me-2 text-info"></i><?= Html::encode($user->speciality->name) ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($user->expected_exam_date): ?>
                                <p class="card-text mb-1"><i class="fas fa-calendar-alt me-2 text-warning"></i>Target Exam:
                                    <?= Yii::$app->formatter->asDate($user->expected_exam_date) ?>
                                </p>
                            <?php endif; ?>
                            <?= Html::a('<i class="fas fa-user-edit me-2"></i>Edit Profile', ['/user/profile'], ['class' => 'btn btn-outline-primary btn-sm mt-3']) ?>
                        </div>
                        <div class="col-md-6 profile-card-section border-start  text-center">
                            <h6 class="fw-bold text-dark mb-2">Subscription:</h6>
                            <div class="profile-card-subscription-status">
                                <?php if ($subscription && $subscription->subscription): ?>
                                    <p class="mb-0 status-text text-success">
                                        <?= Html::encode($subscription->subscription->name) ?>
                                    </p>
                                    <p class="card-text small text-muted">Expires:
                                        <?= Yii::$app->formatter->asDate($subscription->end_date) ?>
                                    </p>
                                <?php else: ?>
                                    <p class="mb-0 status-text text-danger">No Active Subscription</p>
                                    <p class="card-text small text-muted">Upgrade for more features!</p>
                                <?php endif; ?>
                            </div>
                            <?= Html::a('<i class="fas fa-credit-card me-2"></i>Manage', ['/payments/subscriptions'], ['class' => 'btn btn-outline-primary btn-sm mt-3']) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-6">
            <div class="card dashboard-card card-quick-actions h-100 shadow-lg">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h3 class="card-title mb-4">What would you like to do?</h3>
                    <div class="d-grid gap-3 w-75">
                        <?= Html::a('<i class="fas fa-play-circle me-2"></i>Start New Exam', ['exam/'], ['class' => 'btn btn-start-exam btn-action-lg']) ?>
                        <?= Html::a('<i class="fas fa-list-alt me-2"></i>Review Past Exams', ['results/'], ['class' => 'btn btn-review-exams btn-action-lg']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <h4 class="stat-value"><?= $overallStats['correctlyAnswered'] ?? 0 ?></h4>
                    <p class="stat-label">Correct Answers</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card dashboard-card h-100 shadow-sm bg-light">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="fas fa-times-circle stat-icon text-danger"></i>
                    <h4 class="stat-value"><?= $overallStats['incorrectlyAnswered'] ?? 0 ?></h4>
                    <p class="stat-label">Incorrect Answers</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card dashboard-card h-100 shadow-sm bg-light">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="progress-circle">
                        <span class="progress-circle-text"><?= $overallStats['overallAccuracy'] ?? 0 ?>%</span>
                    </div>
                    <h5 class="mt-3 mb-0 fw-bold text-dark">Overall Accuracy</h5>
                    <p class="stat-label"><?= $overallStats['examsCompleted'] ?? 0 ?> Exams Completed</p>
                </div>
            </div>
        </div>
    </div>

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
                                    <?= Html::encode($exam->mode) ?> Exam on
                                    <?= Html::encode($exam->specialty->name ?? 'N/A') ?>
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
                            <?= Html::a('<i class="fas fa-play me-1"></i> Resume', ['/exam/start', 'session_id' => $exam->id], ['class' => 'btn btn-sm btn-warning']) ?>
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

        <!-- Recent Completed Exams -->
        <div class="col-lg-6">
            <h3 class="section-title text-dark mb-4">Recent Completed Exams</h3>
            <?php if (!empty($recentExams)): ?>
                <div class="list-group">
                    <?php foreach ($recentExams as $exam): ?>
                        <div class="list-group-item exam-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">
                                    <i class="fas fa-medal text-success me-2"></i>
                                    <?= Html::encode(ucfirst($exam->mode)) ?> Exam
                                </h6>
                                <p class="mb-0 small text-muted">
                                    Completed: <?= Yii::$app->formatter->asRelativeTime($exam->end_time) ?> | Accuracy: <span
                                        class="fw-bold text-<?= $exam->accuracy >= 70 ? 'success' : ($exam->accuracy >= 50 ? 'warning' : 'danger') ?>"><?= round($exam->accuracy, 1) ?>%</span>
                                </p>
                            </div>
                            <?= Html::a('<i class="fas fa-eye me-1"></i> View Results', ['results/view', 'id' => $exam->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4 rounded-3 shadow-sm">
                    No completed exams found yet. <br>
                    <?= Html::a('View your achievements here!', ['/results/index'], ['class' => 'alert-link mt-2 d-inline-block']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\Session[] $sessions */

$badgeClasses = [
    'Completed' => 'bg-success',
    'InProgress' => 'bg-warning text-dark',
    'Terminated' => 'bg-danger',
];
?>

<?php if (empty($sessions)): ?>
    <div class="alert alert-info text-center" role="alert">
        <h4 class="alert-heading">No Sessions Found</h4>
        <p>You have not completed any exam sessions yet. When you do, they will appear here.</p>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($sessions as $session): ?>
            <div class="col-md-6 col-12">
                <div class="card mb-3">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center text-truncate gap-2">
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="card-title mb-0 text-truncate">
                                <i class="bi bi-file-text-fill me-2"></i>
                                <?= $session->getName() ?>
                                <span class="ms-2 badge rounded-pill <?= $badgeClasses[$session->status] ?? 'bg-secondary' ?>">
                                    <?= Html::encode($session->status) ?>
                                </span>
                            </h5>
                        </div>

                        <div class="flex-shrink-0">
                            <?php if ($session->status === 'Completed'): ?>
                                <a href="<?= Url::to(['view', 'id' => $session->id]) ?>"
                                    class="btn btn-sm btn-primary rounded-3 w-100 w-md-auto">
                                    View Result <i class="bi bi-arrow-right-circle ms-1"></i>
                                </a>
                            <?php elseif ($session->status === 'InProgress'): 
                                $url = $session->isModeMock() ? ['/user/mock-exam/take', 'session' => $session->id] : ['/user/mcq/start', 'session_id' => $session->id];
                                ?>
                                <a href="<?= Url::to($url) ?>"
                                    class="btn btn-sm btn-warning rounded-3 w-100 w-md-auto">
                                    Resume <i class="bi bi-play-circle ms-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body  py-2">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Score:</strong>
                                <span><?= (int) $session->correct_count ?> / <?= (int) $session->total_questions ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Accuracy:</strong>
                                <span class="fw-bold text-primary"><?= round($session->accuracy) ?>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <strong>Completed At:</strong>
                                <span><?= ucfirst(Yii::$app->formatter->asRelativeTime($session->end_time)) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
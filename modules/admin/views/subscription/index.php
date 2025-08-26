<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $subscriptionDataProvider */
/** @var yii\data\ActiveDataProvider $userSubscriptionDataProvider */
/** @var array $analytics */

$this->title = 'Subscription Management';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .icon-circle { height: 3.5rem; width: 3.5rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
    .bg-light-primary { background-color: rgba(59, 130, 246, 0.1); }
    .bg-light-success { background-color: rgba(22, 163, 74, 0.1); }
    .bg-light-warning { background-color: rgba(245, 158, 11, 0.1); }
    .bg-light-info { background-color: rgba(13, 202, 240, 0.1); }
");
?>

<div class="subscription-index">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div>

    <!-- Analytics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-success text-success me-3"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Active Subscribers</div>
                        <div class="h3 fw-bold mb-0"><?= $analytics['active_subscribers'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-info text-info me-3"><i class="fas fa-box-open fa-lg"></i></div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Total Plans</div>
                        <div class="h3 fw-bold mb-0"><?= $analytics['total_plans'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-primary text-primary me-3"><i class="fas fa-star fa-lg"></i></div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Most Popular Plan</div>
                        <div class="h4 fw-bold mb-0"><?= Html::encode($analytics['most_popular_plan']) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-warning text-warning me-3"><i
                            class="fas fa-hourglass-end fa-lg"></i></div>
                    <div>
                        <div class="text-muted fw-bold text-uppercase small">Expiring This Week</div>
                        <div class="h3 fw-bold mb-0"><?= $analytics['expiring_soon'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Manage Subscription Plans -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs me-2"></i>Manage Subscription
                        Plans</h6>
                    <?= Html::a('<i class="fas fa-plus me-2"></i>New Plan', ['create'], ['class' => 'btn btn-sm btn-primary shadow-sm']) ?>
                </div>
                <?= GridView::widget([
                    'dataProvider' => $subscriptionDataProvider,
                    'summary' => '',
                    'tableOptions' => ['class' => 'table table-hover mb-0'],
                    'columns' => [
                        'name:text:Plan Name',
                        'price:currency',
                        'duration_days:text:Duration (Days)',
                        [
                            'attribute' => 'features_json',
                            'format' => 'raw',
                            'value' => function ($model) {
                                                if (empty($model->features_json)) {
                                                    return '<span class="text-muted">None</span>';
                                                }
                                                try {
                                                    $features = Json::decode($model->features_json);
                                                    if (!is_array($features)) {
                                                        throw new \Exception();
                                                    }

                                                    $html = '<ul class="list-unstyled mb-0">';
                                                    foreach ($features as $feature) {
                                                        // Check for the [x] marker
                                                        if (strpos(trim($feature), '[x]') === 0) {
                                                            $cleanFeature = trim(substr(trim($feature), 3));
                                                            $html .= '<li><i class="far fa-times-circle text-muted me-2"></i><del>' . Html::encode($cleanFeature) . '</del></li>';
                                                        } else {
                                                            $html .= '<li><i class="far fa-check-circle text-success me-2"></i>' . Html::encode($feature) . '</li>';
                                                        }
                                                    }
                                                    return $html . '</ul>';
                                                } catch (\Throwable $e) {
                                                    return '<span class="text-danger">Invalid JSON Format</span>';
                                                }
                                            }
                        ],
                        'created_at:datetime',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {delete}',
                            'buttons' => [
                                'update' => fn($url) => Html::a('<i class="fas fa-edit"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'Update']),
                                'delete' => fn($url) => Html::a('<i class="fas fa-trash-alt"></i>', $url, ['class' => 'btn btn-sm btn-outline-danger', 'title' => 'Delete', 'data-confirm' => 'Are you sure you want to delete this plan?', 'data-method' => 'post']),
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>

        <!-- Active User Subscriptions -->
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users-cog me-2"></i>Active User
                        Subscriptions</h6>
                </div>
                <?= GridView::widget([
                    'dataProvider' => $userSubscriptionDataProvider,
                    'tableOptions' => ['class' => 'table table-hover mb-0'],
                    'layout' => "<div class='table-responsive'>{items}</div><div class='card-footer bg-white'>{summary}\n{pager}</div>",
                    'columns' => [
                        'user.username:text:User',
                        'subscription.name:text:Plan',
                        'start_date:date',
                        'end_date:date',
                        [
                            'attribute' => 'is_active',
                            'format' => 'raw',
                            'value' => fn() => '<span class="badge bg-success">Active</span>'
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
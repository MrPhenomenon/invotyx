<?php
use yii\helpers\Html;
use yii\helpers\Url;

$user = Yii::$app->user->identity;

$hasActiveSubscription = Yii::$app->session->get('user.has_active_subscription', false);
$subscriptionName = Yii::$app->session->get('user.subscription_name');
$subscriptionEndDate = Yii::$app->session->get('user.subscription_end_date');

?>
<header class="pc-header d-flex justify-content-between align-items-center p-3 border-bottom" style="min-height: 65px;">
  <!-- Sidebar toggle -->
  <div class="d-flex align-items-center">
    <i class="bi bi-list fs-3 me-3 cursor-pointer" id="sidebarToggle" type="button"></i>
  </div>


  <div class="d-flex align-items-center">
    <form id="search-mcq" action="<?= Url::to(['mcq/search']) ?>" method="get" class="me-5">
      <div class="input-group" style="min-width:200px">
        <input type="text" name="q" class="form-control" placeholder="Search MCQs..."
          value="<?= Yii::$app->request->get('q') ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </div>
    </form>

    <div class="dropdown pe-lg-5 text-white">
      <a href="#" class=" text-decoration-none dropdown-toggle arrow-none link-light fw-bold" id="userDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-4 me-2"></i>
        <?= Html::encode($user->name) ?>
      </a>
      <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown"
        data-popper-placement="bottom-end">
        <div class="dropdown-header">
          <div class="d-flex mb-1 align-items-center">
            <div class="">
              <img src="<?= Yii::getAlias('@web') ?>/dassets/images/user/avatar-2.jpg" alt="user-image"
                class="user-avtar wid-35 rounded-circle">
            </div>
            <div class="ms-3">
              <h6 class="mb-1"><?= Html::encode($user->name) ?></h6>
              <small class="text-opacity-75"><?= Html::encode($user->email) ?></small>
            </div>
          </div>
        </div>

        <div class="dropdown-body-content px-4 py-3 border-0">
          <div class="d-none d-md-block">
            <h6 class="text-dark mb-2 fw-semibold">Exam & Profile:</h6>
            <?php if ($user->speciality): ?>
              <p class="mb-1">
                <i class="fas fa-stethoscope me-2 text-info"></i>
                <?= Html::encode($user->examType->name) ?> - <?= Html::encode($user->speciality->name) ?>
              </p>
            <?php endif; ?>
            <?php if ($user->expected_exam_date): ?>
              <p class="mb-1">
                <i class="fas fa-calendar-alt me-2 text-warning"></i>
                Target Exam: <?= Yii::$app->formatter->asDate($user->expected_exam_date) ?>
              </p>
            <?php else: ?>
              <p class="mb-1 text-muted">No exam date set</p>
            <?php endif; ?>
            <div class="mt-3">
              <h6 class="text-dark mb-2 fw-semibold">Subscription:</h6>
              <?php if ($hasActiveSubscription): ?>
                <p class="mb-0 status-text text-success">
                  <?= Html::encode($subscriptionName) ?>
                </p>
                <p class="small text-muted">Expires:
                  <?= Yii::$app->formatter->asDate($subscriptionEndDate) ?>
                </p>
              <?php else: ?>
                <p class="mb-0 status-text text-danger">No Active Subscription</p>
                <p class="small text-muted">Upgrade for more features!</p>
              <?php endif; ?>
            </div>
          </div>


          <div class="dropdown-footer-actions d-flex justify-content-between align-items-center mb-3">
            <?= Html::a('<i class="fas fa-user-edit me-2"></i>View Profile', ['/user/profile'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>

          </div>

          <a href="#!" class="">
            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item p-0']) ?>
            <button type="submit" class="btn btn-link dropdown-item text-start px-0">
              <i class="bi bi-power"></i> Logout
            </button>
            <?= Html::endForm() ?>
          </a>
        </div>
      </div>
    </div>
  </div>


</header>
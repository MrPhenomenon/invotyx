<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<header class="pc-header d-flex justify-content-between align-items-center p-3 border-bottom" style="min-height: 65px;">
  <!-- Sidebar toggle -->
  <div class="d-flex align-items-center">
    <i class="bi bi-list fs-3 me-3 cursor-pointer" id="sidebarToggle" type="button"></i>
  </div>


  <div class="d-flex align-items-center">
    <form action="<?= Url::to(['mcq/search']) ?>" method="get" class="me-5">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search MCQs..."
          value="<?= Yii::$app->request->get('q') ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </div>
    </form>

    <div class="dropdown pe-5 text-white">
      <a href="#" class=" text-decoration-none dropdown-toggle arrow-none link-light fw-bold" id="userDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-4 me-2"></i>
        <?= Yii::$app->user->identity->name ?>
      </a>
      <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown"
        data-popper-placement="bottom-end">
        <div class="dropdown-header">
          <div class="d-flex mb-1 align-items-center">
            <div class="">
              <img src="<?= Yii::getAlias('@web') ?>/dassets/images/user/avatar-2.jpg" alt="user-image"
                class="user-avtar wid-35">
            </div>
            <div class="ms-3">
              <h6 class="mb-1"><?= Yii::$app->user->identity->name ?></h6>
            </div>

          </div>
        </div>
        <a href="#!" class="dropdown-item">
          <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item p-0 m-0']) ?>
          <button type="submit" class="btn btn-link dropdown-item text-start px-0">
            <i class="bi bi-power"></i> Logout
          </button>
          <?= Html::endForm() ?>
        </a>
      </div>

    </div>
  </div>


</header>
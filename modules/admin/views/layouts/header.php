<?php 
use yii\helpers\Html;
?>

<header class="pc-header d-flex justify-content-between align-items-center p-3 border-bottom" style="min-height: 65px;">
  <!-- Sidebar toggle -->
  <div class="d-flex align-items-center">
    <i class="bi bi-list fs-3 me-3 cursor-pointer" id="sidebarToggle" type="button"></i>
  </div>

  <!-- User Dropdown -->
  <div class="dropdown pe-5">
    <a href="#" class=" text-decoration-none dropdown-toggle arrow-none link-dark fw-bold" id="userDropdown"
      data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-person-circle fs-4 me-2"></i>
      <?= Yii::$app->admin->identity->name ?>
    </a>
    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown" data-popper-placement="bottom-end">
      <div class="dropdown-header">
        <div class="d-flex mb-1">
          <div class="flex-shrink-0">
            <img src="<?= Yii::getAlias('@web') ?>/dassets/images/user/avatar-2.jpg" alt="user-image"
              class="user-avtar wid-35">
          </div>
          <div class="flex-grow-1 ms-3">
            <h6 class="mb-1"><?= Yii::$app->admin->identity->name ?></h6>
            <span><?= Yii::$app->admin->identity->role ?></span>
          </div>

        </div>
      </div>
      <a href="#!" class="dropdown-item">
        <?= Html::beginForm(['/site/logout-admin'], 'post', ['class' => 'dropdown-item p-0 m-0']) ?>
        <button type="submit" class="btn btn-link dropdown-item text-start">
          <i class="bi bi-power"></i> Logout
        </button>
        <?= Html::endForm() ?>
      </a>
    </div>

  </div>
  </div>
  </div>
</header>
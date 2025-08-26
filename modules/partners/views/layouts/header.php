<?php
use yii\helpers\Html;
?>

<header class="pc-header d-flex justify-content-between align-items-center p-3 border-bottom" style="min-height: 65px;">
  <!-- Sidebar toggle -->
  <div class="d-flex align-items-center">
    <i class="bi bi-list fs-3 me-3 cursor-pointer" id="sidebarToggle" type="button"></i>
  </div>

  <!-- User Dropdown -->

  <div class="d-flex align-items-center me-3">
    <div class="">
      <img src="<?= Yii::getAlias('@web') ?>/dassets/images/user/avatar-2.jpg" alt="user-image"
        class="user-avtar">
    </div>
    <div class="">
      <h6 class="mb-0"><?= $partner->name ?></h6>
    </div>

  </div>
</header>
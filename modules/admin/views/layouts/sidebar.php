<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header" style="height: 69px">
      <a href="dashboard/index.html" class="b-brand text-dark fw-bold fs-2 d-flex align-items-center">
        <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/logo.svg" width="50px" class="img-fluid logo-lg me-2" alt="flogo">
        Invotyx
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">
        <?php include('menu-list.php') ?>
      </ul> 
    </div>
  </div>
</nav>
<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<header id="header" class="header d-flex align-items-center">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">

    <a href="" class="logo d-flex align-items-center me-auto">
      <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/logo.png" alt="">

    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="" class="active">Home<br></a></li>
        <li><a href="about">About</a></li>
        <li><a href="pricing">Pricing</a></li>
        <li><a href="">Contact</a></li>
        <?php
        if (!Yii::$app->user->isGuest) {
          echo '<li><a href="' . Url::to(['user//']) . '">Dashboard</a></li>';
        }
        ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>
    <?php
    if (Yii::$app->user->isGuest) {
      echo Html::a('Login', ['/login'], ['class' => 'btn-getstarted btn btn-light']);
    } else {
      echo Html::beginForm(['/site/logout'], 'post')
        . Html::submitButton(
          Yii::$app->user->identity->name . ' (Logout)',
          ['class' => 'btn-getstarted btn btn-light']
        )
        . Html::endForm();
    }
    ?>
  </div>
</header>
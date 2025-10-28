<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<header id="header" class="header d-flex align-items-center">
  <div class="container-fluid container-xl position-relative d-flex align-items-center">

    <a href="<?= Yii::$app->homeUrl ?>" class="logo d-flex align-items-center me-auto">
      <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/logo1.png" alt="">

    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="<?= Url::to(['/']) ?>"
            class="<?= Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index' ? 'active' : '' ?>">Home</a>
        </li>
        <li><a href="<?= Url::to(['/about']) ?>"
            class="<?= Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'about' ? 'active' : '' ?>">About</a>
        </li>
        <li><a href="<?= Url::to(['/pricing']) ?>"
            class="<?= Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'pricing' ? 'active' : '' ?>">Pricing</a>
        </li>
        <li><a href="<?= Url::to(['/contact']) ?>"
            class="<?= Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'contact' ? 'active' : '' ?>">Contact</a>
        </li>
        <?php
        ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>
    <?php
    if (Yii::$app->user->isGuest) {
      echo Html::a('Login', ['/login'], ['class' => 'btn-getstarted btn btn-light']);
    } else {
       echo '<a class="btn-getstarted btn btn-light" href="' . Url::to(['user/default/index']) . '">Dashboard</a>';
    }
    ?>
  </div>
</header>
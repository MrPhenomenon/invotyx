<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\DashboardAsset;
use yii\bootstrap5\Html;


DashboardAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    <?= Html::encode($this->title) ?>
  </title>
  <?php $this->head() ?>
  <link rel="icon" href="dassets/images/favicon.svg" type="image/x-icon">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php $this->beginBody() ?>

  <?php include 'toast.php'; ?>
  <?php include 'delete-modal.php'; ?>
  <?php include 'loader.php'; ?>
  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>
  <div class="pc-container">

    <main class="pc-content py-5" role="main">
      <?= $content ?>
    </main>
  </div>

  <?php $this->endBody() ?>

</body>

</html>
<?php $this->endPage() ?>
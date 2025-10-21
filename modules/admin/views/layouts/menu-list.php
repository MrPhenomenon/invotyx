<?php

use yii\helpers\Url;
/** @var app\models\ManagementTeam $user */
$admin = Yii::$app->admin->identity;
$menuSections = $admin->getSidebarMenuItems();
?>

<?php foreach ($menuSections as $section): ?>
  <li class="pc-item pc-caption"><label><?= $section['label'] ?></label></li>
  <?php foreach ($section['items'] as $item): ?>
    <?php if (isset($item['submenu'])): ?>
      <li class="pc-item">
        <a class="pc-link" data-bs-toggle="collapse" href="#<?= md5($item['label']) ?>" role="button" aria-expanded="false">
          <i class="<?= $item['icon'] ?>"></i>
          <span class="pc-mtext"><?= $item['label'] ?></span>
          <i class="bi bi-chevron-down"></i>
        </a>
        <ul class="pc-submenu collapse" id="<?= md5($item['label']) ?>">
          <?php foreach ($item['submenu'] as $sub): ?>
            <li class="pc-item"><a class="pc-link" href="<?= Url::to($sub['url']) ?>"><?= $sub['label'] ?></a></li>
          <?php endforeach; ?>
        </ul>
      </li>
    <?php else: ?>
      <li class="pc-item">
        <a href="<?= Url::to($item['url']) ?>" class="pc-link">
          <i class="<?= $item['icon'] ?>"></i>
          <span class="pc-mtext"><?= $item['label'] ?></span>
        </a>
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endforeach; ?>



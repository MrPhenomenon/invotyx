<?php use yii\helpers\Url; ?>

<li class="pc-item pc-caption">
  <label>Statistics</label>
</li>
<li class="pc-item pc-caption">
  <label>Exams</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam/create', 'access' => $partner->access_token]) ?>" class="pc-link">
    <i class="bi bi-plus-circle"></i>
    <span class="pc-mtext">Create Exam</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam/index', 'access' => $partner->access_token]) ?>" class="pc-link">
    <i class="bi bi-pencil-square"></i>
    <span class="pc-mtext">Manage Exams</span>
  </a>
</li>
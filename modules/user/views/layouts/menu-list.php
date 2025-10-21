<?php 
use yii\helpers\Url;
?>
<li class="pc-item pc-caption">
  <label>UI Components</label>
  <i class="ti ti-dashboard"></i>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/index']) ?>" class="pc-link">
    <span class="pc-micon"> <i class="bi bi-speedometer2"></i></span>
    <span class="pc-mtext">Dashboard</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/analytics']) ?>" class="pc-link">
    <span class="pc-micon"><i class="bi bi-graph-up"></i></span>
    <span class="pc-mtext">Analytics</span>
  </a>
</li>
<!-- <li class="pc-item">
  <a href="../page" class="pc-link">
    <span class="pc-micon"><i class="bi bi-journal-check"></i></span>
    <span class="pc-mtext">Study Plans (NF)</span>
  </a>
</li> -->

<li class="pc-item pc-caption">
  <label>Exams</label>
  <i class="ti ti-news"></i>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam/']) ?>" class="pc-link">
    <span class="pc-micon"><i class="bi bi-file-earmark-text"></i></span>
    <span class="pc-mtext">Start New Exam</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['results/']) ?>" class="pc-link">
    <span class="pc-micon"><i class="bi bi-clock-history"></i></span>
    <span class="pc-mtext">Exam History</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label>MCQs</label>
  <i class="ti ti-brand-chrome"></i>
</li>

<li class="pc-item ">
  <a href="<?= Url::to(['bookmarks/']) ?>" class="pc-link">
    <span class="pc-micon"> <i class="bi bi-clipboard-check"></i></span>
    <span class="pc-mtext">Bookmarked</span>
  </a>
</li>
<li class="pc-item pc-caption">
  <label>Settings</label>
  <i class="ti ti-brand-chrome"></i>
</li>
  <li class="pc-item">
    <a href="<?= Url::to(['default/profile']) ?>" class="pc-link">
      <span class="pc-micon"> <i class="bi bi-gear"></i></span>
      <span class="pc-mtext">Profile</span>
    </a>
  </li>
</div>
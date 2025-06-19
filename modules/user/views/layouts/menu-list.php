<?php 
use yii\helpers\Url;
?>
<li class="pc-item pc-caption">
  <label>UI Components</label>
  <i class="ti ti-dashboard"></i>
</li>
<li class="pc-item">
  <a href="../elements/bc_typography.html" class="pc-link">
    <span class="pc-micon"> <i class="bi bi-speedometer2"></i></span>
    <span class="pc-mtext">Dashboard</span>
  </a>
</li>
<li class="pc-item">

  <a href="../elements/bc_typography.html" class="pc-link">
    <span class="pc-micon"><i class="bi bi-graph-up"></i></span>
    <span class="pc-mtext">Analytics</span>
  </a>
</li>
<li class="pc-item">
  <a href="../elements/bc_color.html" class="pc-link">
    <span class="pc-micon"><i class="bi bi-journal-check"></i></span>
    <span class="pc-mtext">Study Plans</span>
  </a>
</li>

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
  <a href="../pages/register.html" class="pc-link">
    <span class="pc-micon"><i class="bi bi-clock-history"></i></span>
    <span class="pc-mtext">Practice History</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label>MCQs</label>
  <i class="ti ti-brand-chrome"></i>
</li>

<li class="pc-item ">
  <a href="../other/sample-page.html" class="pc-link">
    <span class="pc-micon"> <i class="bi bi-clipboard-check"></i></span>
    <span class="pc-mtext">Bookmarked</span>
  </a>
</li>
<li class="pc-item">
  <a href="../other/sample-page.html" class="pc-link">
    <span class="pc-micon"><i class="bi bi-hourglass-bottom"></i></span>
    <span class="pc-mtext">Recently Practiced</span>
  </a>
</li>
<div class="mt-4">
  <li class="pc-item">
    <a href="../other/sample-page.html" class="pc-link">
      <span class="pc-micon">  <i class="bi bi-credit-card"></i></span>
      <span class="pc-mtext">Subscriptions</span>
    </a>
  </li>
  <li class="pc-item">
    <a href="../other/sample-page.html" class="pc-link">
      <span class="pc-micon"> <i class="bi bi-gear"></i></span>
      <span class="pc-mtext">Settings</span>
    </a>
  </li>
</div>
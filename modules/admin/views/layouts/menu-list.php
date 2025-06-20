<?php
use yii\helpers\Url;
?>

<li class="pc-item pc-caption">
  <label>Statics</label>
</li>
<li class="pc-item pc-caption">
  <label>Statics</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/index']) ?>" class="pc-link">
    <i class="bi bi-bar-chart-line"></i>
    <span class="pc-mtext">User Analytics</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/exam-performance']) ?>" class="pc-link">
    <i class="bi bi-speedometer2"></i>
    <span class="pc-mtext">Exam Performance (NF)</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/trust-breach-monitoring']) ?>" class="pc-link">
    <i class="bi bi-exclamation-triangle"></i>
    <span class="pc-mtext">Trust Breach Monitoring (NF)</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label>Subscriptions</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['subscription/index']) ?>" class="pc-link">
    <i class="bi bi-calendar3"></i>
    <span class="pc-mtext">Subscriptions Management (NF)</span>
  </a>
</li>


<li class="pc-item pc-caption">
  <label>Data Entry</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['mcq/manage']) ?>" class="pc-link">
    <i class="bi bi-gear-fill"></i>
    <span class="pc-mtext">Manage MCQs</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['mcq/add']) ?>" class="pc-link">
    <i class="bi bi-plus-circle"></i>
    <span class="pc-mtext">Add MCQs</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['mcq/import-mcq']) ?>" class="pc-link">
    <i class="bi bi-file-earmark-plus"></i>
    <span class="pc-mtext">Import from File</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['mcq/manage-topics']) ?>" class="pc-link">
    <i class="bi bi-database-gear"></i>
    <span class="pc-mtext">Manage Chapters & Topics</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label>Exam Management</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam/index']) ?>" class="pc-link">
    <i class="bi bi-database-gear"></i>
    <span class="pc-mtext">Exam Types & Specialties</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam/distribution']) ?>" class="pc-link">
    <i class="bi bi-database-gear"></i>
    <span class="pc-mtext">Mock Exam Distribution</span>
  </a>
</li>

<li class="pc-item pc-caption">
  <label>Support</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['support/tickets']) ?>" class="pc-link">
    <i class="bi bi-ticket"></i>
    <span class="pc-mtext">Handle Tickets (NF)</span>
  </a>
</li>
<li class="pc-item pc-caption">
  <label>Team Management</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/team-management']) ?>" class="pc-link">
    <i class="bi bi-person-gear"></i>
    <span class="pc-mtext">Manage Team</span>
  </a>
</li>

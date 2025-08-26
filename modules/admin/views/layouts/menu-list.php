<?php use yii\helpers\Url; ?>

<!-- Statics Section -->
<li class="pc-item pc-caption">
  <label>Statistics</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/index']) ?>" class="pc-link">
    <i class="bi bi-bar-chart-line"></i>
    <span class="pc-mtext">User Analytics</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['exam-analytics/index']) ?>" class="pc-link">
    <i class="bi bi-speedometer2"></i>
    <span class="pc-mtext">Exam Performance</span>
  </a>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/trust-breach-monitoring']) ?>" class="pc-link">
    <i class="bi bi-exclamation-triangle"></i>
    <span class="pc-mtext">Trust Breach Monitoring (NF)</span>
  </a>
</li>

<!-- Subscriptions -->
<li class="pc-item pc-caption">
  <label>Subscriptions</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['subscription/index']) ?>" class="pc-link">
    <i class="bi bi-calendar3"></i>
    <span class="pc-mtext">Subscriptions Management (NF)</span>
  </a>
</li>

<!-- Data Entry Dropdown -->
<li class="pc-item pc-caption">
  <label>Data Entry</label>
</li>
<li class="pc-item">
  <a class="pc-link" data-bs-toggle="collapse" href="#dataEntryMenu" role="button" aria-expanded="false"
    aria-controls="dataEntryMenu">
    <i class="bi bi-database-fill"></i>
    <span class="pc-mtext">MCQ Management</span>
    <i class="bi bi-chevron-down"></i>
  </a>
  <ul class="pc-submenu collapse" id="dataEntryMenu">
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['mcq/manage']) ?>">Manage MCQs</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['mcq/add']) ?>">Add MCQs</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['mcq/import-mcq']) ?>">Import from File</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['mcq/manage-topics']) ?>">Manage Chapters & Topics</a></li>
  </ul>
</li>

<!-- Exam Management Dropdown -->
<li class="pc-item pc-caption">
  <label>Exam Management</label>
</li>
<li class="pc-item">
  <a class="pc-link" data-bs-toggle="collapse" href="#examMenu" role="button" aria-expanded="false"
    aria-controls="examMenu">
    <i class="bi bi-ui-checks-grid"></i>
    <span class="pc-mtext">Exam Configuration</span>
    <i class="bi bi-chevron-down"></i>
  </a>
  <ul class="pc-submenu collapse" id="examMenu">
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['exam/index']) ?>">Exam Types & Specialties</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['exam/distribution']) ?>">Mock Exam Distribution (NF)</a></li>
  </ul>
</li>

<!-- External Partners Dropdown -->
<li class="pc-item pc-caption">
  <label>Partners</label>
</li>
<li class="pc-item">
  <a class="pc-link" data-bs-toggle="collapse" href="#partnerMenu" role="button" aria-expanded="false"
    aria-controls="partnerMenu">
    <i class="bi bi-building-add"></i>
    <span class="pc-mtext">External Partners</span>
    <i class="bi bi-chevron-down"></i>
  </a>
  <ul class="pc-submenu collapse" id="partnerMenu">
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['external-partners/create']) ?>">Add New Partner</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= Url::to(['external-partners/index']) ?>">View All Partners</a></li>
  </ul>
</li>

<!-- Support -->
<li class="pc-item pc-caption">
  <label>Support</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['support/tickets']) ?>" class="pc-link">
    <i class="bi bi-ticket"></i>
    <span class="pc-mtext">Handle Tickets (NF)</span>
  </a>
</li>

<!-- Team Management -->
<li class="pc-item pc-caption">
  <label>Team Management</label>
</li>
<li class="pc-item">
  <a href="<?= Url::to(['default/team-management']) ?>" class="pc-link">
    <i class="bi bi-person-gear"></i>
    <span class="pc-mtext">Manage Team</span>
  </a>
</li>
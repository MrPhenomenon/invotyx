<?php

use yii\helpers\Html;

$this->registerCss("
    .icon-circle {
        height: 3.5rem;
        width: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .bg-light-success { background-color: rgba(22, 163, 74, 0.1); }
    .bg-light-warning { background-color: rgba(245, 158, 11, 0.1); }
    .bg-light-primary { background-color: rgba(59, 130, 246, 0.1); }
    .bg-light-danger { background-color: rgba(220, 38, 38, 0.1); }
    
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .avatar-placeholder {
        background-color: #e9ecef;
        color: #495057;
        font-weight: bold;
    }
");
?>
<div class="user-dashboard">

  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">User Dashboard</h1>
   
  </div>

  <!-- Stat Cards Section -->
  <div class="row g-4 mb-4">
    <!-- Total Users Card -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center">
          <div class="icon-circle bg-light-success text-success me-3">
            <i class="fas fa-users fa-lg"></i>
          </div>
          <div>
            <div class="text-muted fw-bold text-uppercase small">Total Users</div>
            <div class="h3 fw-bold mb-0"><?= $userCount ?></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Pro Users Card -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center">
          <div class="icon-circle bg-light-warning text-warning me-3">
            <i class="fas fa-user-shield fa-lg"></i>
          </div>
          <div>
            <div class="text-muted fw-bold text-uppercase small">Pro Users</div>
            <div class="h3 fw-bold mb-0"><?= $proUsers ?></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Exams Taken Card -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center">
          <div class="icon-circle bg-light-primary text-primary me-3">
            <i class="fas fa-file-alt fa-lg"></i>
          </div>
          <div>
            <div class="text-muted fw-bold text-uppercase small">Exams Taken</div>
            <div class="h3 fw-bold mb-0"><?= $examTaken ?></div>
          </div>
        </div>
      </div>
    </div>
    <!-- Exams Today Card -->
    <div class="col-md-6 col-xl-3">
      <div class="card shadow-sm h-100 border-0">
        <div class="card-body d-flex align-items-center">
          <div class="icon-circle bg-light-danger text-danger me-3">
            <i class="fas fa-calendar-day fa-lg"></i>
          </div>
          <div>
            <div class="text-muted fw-bold text-uppercase small">Exams Today</div>
            <div class="h3 fw-bold mb-0"><?= $examToday ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart and Table Section -->
  <div class="row g-4">
    <!-- User Registration Chart -->
    <div class="col-xl-7">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>User Registration Trends
          </h6>
        </div>
        <div class="card-body">
          <div id="users-chart"></div>
        </div>
      </div>
    </div>

    <!-- Recently Registered Users Table -->
    <div class="col-xl-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white py-3">
          <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-clock me-2"></i>Recently Registered Users
          </h6>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0" style="vertical-align: middle;">
              <tbody>
                <?php foreach ($recentUsers as $user): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar-placeholder icon-circle me-3">
                          <span><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                        </div>
                        <div>
                          <div class="fw-bold"><?= Html::encode($user['name']) ?></div>
                          <div class="small text-muted"><?= Html::encode($user['examType']['name'] ?? 'N/A') ?></div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?php
                      $sub = $user['subscription_name'] ?? 'Free';
                      $badgeClass = 'bg-secondary';
                      if ($sub === 'Pro')
                        $badgeClass = 'bg-warning text-dark';
                      if ($sub === 'Basic')
                        $badgeClass = 'bg-info text-dark';
                      ?>
                      <span class="badge <?= $badgeClass ?>"><?= Html::encode($sub) ?></span>
                    </td>
                    <td class="text-end">
                      <?= Html::a('<i class="fas fa-search"></i>', ['user/view', 'id' => $user['id']], ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'View Profile']) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$totalUsersJson = json_encode($totalUsers, JSON_UNESCAPED_UNICODE);
$basicUsersJson = json_encode($basicUsers, JSON_UNESCAPED_UNICODE);
$proUsersJson = json_encode($proUser, JSON_UNESCAPED_UNICODE);
$mockOnlyUsersJson = json_encode($mockOnlyUsers, JSON_UNESCAPED_UNICODE);


$js = <<<JS
function floatchart() {
  (function () {
    var options = {
      chart: {
        height: 450,
        type: 'area',
    
      },
      dataLabels: { enabled: false },
      colors: ['#1890ff', '#52c41a', '#faad14', '#f5222d'],
      series: [
        {
          name: 'Total Registered',
          data: {$totalUsersJson}
        },
        {
          name: 'Basic',
          data: {$basicUsersJson}
        },
        {
          name: 'Pro',
          data: {$proUsersJson}
        },
        {
          name: 'Mock Only',
          data: {$mockOnlyUsersJson}
        }
      ],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      xaxis: {
         categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
      }
    };
    var chart = new ApexCharts(document.querySelector('#users-chart'), options);
    chart.render();
  })();
}
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <h5 class="mb-0">User Stats</h5>
    </div>
  </div>
  <!-- [ sample-page ] start -->
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-2 f-w-400 text-muted">Users</h5>
          <h3 class="mb-3"><?= $userCount ?></h3>
        </div>
        <i class="bi bi-person-fill display-2 text-success"></i>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-2 f-w-400 text-muted">Pro Users</h5>
          <h3 class="mb-3"><?= $proUsers ?></h3>
        </div>
        <i class="bi bi-star-fill display-2 text-warning"></i>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-2 f-w-400 text-muted">Exams Taken</h5>
          <h3 class="mb-3"><?= $examTaken ?></h3>
        </div>
        <i class="bi bi-ui-radios display-2 text-primary"></i>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-2 f-w-400 text-muted">Exams Taken Today</h5>
          <h3 class="mb-3"><?= $examToday ?></h3>
        </div>
        <i class="bi bi-calendar-event-fill display-2 text-danger"></i>
      </div>
    </div>
  </div>


  <div class="col-md-12 col-xl-12">
    <div class="mb-3">
      <h5 class="mb-0">User Registration</h5>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="tab-pane show active" id="chart-tab-profile">
          <div id="users-chart"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-12">
    <h5 class="mb-3">Recently Registered Users</h5>
    <div class="card tbl-card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-borderless mb-0">
            <thead>
              <tr>
                <th>USER ID</th>
                <th>USER NAME</th>
                <th>SUBSCRIPTION</th>
                <th>EXAM TYPE</th>
                <th>SPECIALITY</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $user): ?>
                <tr>
                  <th><?= $user['id'] ?></th>
                  <th><?= $user['name'] ?></th>
                  <th><?= $user['subscription_name'] ?></th>
                  <th><?= $user['examType']['name'] ?></th>
                  <th><?= $user['speciality']['name'] ?></th>
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
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

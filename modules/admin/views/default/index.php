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
          <h3 class="mb-3">4,42,236</h3>
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
          <h3 class="mb-3">4,42,236</h3>
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
          <h3 class="mb-3">4,42,236</h3>
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
          <h3 class="mb-3">4,42,236</h3>
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
              <tr>
                <td><a href="#" class="text-muted">1</a></td>
                <td>John Doe</td>
                <td>Free</td>
                <td>PLAB</td>
                <td>Physiology</td>
              </tr>
              <tr>
                <td><a href="#" class="text-muted">2</a></td>
                <td>John Doe</td>
                <td>Free</td>
                <td>PLAB</td>
                <td>Physiology</td>
              </tr>
              <tr>
                <td><a href="#" class="text-muted">3</a></td>
                <td>John Doe</td>
                <td>Free</td>
                <td>PLAB</td>
                <td>Physiology</td>
              </tr>
              <tr>
                <td><a href="#" class="text-muted">4</a></td>
                <td>John Doe</td>
                <td>Free</td>
                <td>PLAB</td>
                <td>Physiology</td>
              </tr>
              <tr>
                <td><a href="#" class="text-muted">5</a></td>
                <td>John Doe</td>
                <td>Free</td>
                <td>PLAB</td>
                <td>Physiology</td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
$js = <<<JS
function floatchart() {
  (function () {
    var options = {
      chart: {
        height: 450,
        type: 'area',
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      colors: ['#1890ff', '#13c2c2'],
      series: [{
        name: 'Total Registered Users',
        data: [31, 40, 28, 51, 42, 109, 200]
      }, {
        name: 'Pro Users',
        data: [11, 32, 28, 32, 34, 52, 41]
      }],
      stroke: {
        curve: 'smooth',
        width: 2
      },
      xaxis: {
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      }
    };
    var chart = new ApexCharts(document.querySelector('#users-chart'), options);
    chart.render();
   
  })();
}
JS;

$this->registerJS($js, \yii\web\View::POS_END);
?>
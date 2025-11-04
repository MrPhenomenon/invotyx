<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'My Analytics';

$this->registerCss("
#accuracyChart .apexcharts-bar-series path,
.legend-item {
    transition: opacity 0.3s ease-in-out;
}

    body {
        background-color: #f4f6f9;
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    .analytics-header {
        color: #343a40;
        margin-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    .analytics-header .fas {
        color: #0e273c;
        font-size: 1.8rem;
        margin-right: 0.75rem;
    }
    .metric-card {
        border: none;
        overflow: hidden;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        cursor: default;
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #fcfdfe 100%);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12); 
    }
    .metric-card .card-body {
        padding: 1.75rem;
    }
    .metric-card h6 {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .metric-card h3 {
        font-size: 2rem;
        font-weight: 700;
        color: #0e273c;
        line-height: 1.2;
        margin-bottom: 0;
        display: block;
    }
    .metric-card .icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-size: 3rem;
        color: rgba(0, 123, 255, 0.1);
        opacity: 0.8;
    }

    .table-card {
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
    }
    .table-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        font-size: 1.15rem;
        color: #343a40;
        padding: 1rem 1.75rem;
        display: flex;
        align-items: center;
    }
    .table-card .card-header .fas {
        font-size: 1.2rem;
        color: #6c757d;
    }
    .table.table-striped.table-hover.align-middle {
        border: none;
        margin-bottom: 0;
    }
    .table thead.table-light th {
        background-color: #eef1f5; 
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 0.85rem 1.75rem;
        white-space: nowrap; 
        font-size: 0.9rem;
    }
    .table tbody td {
        padding: 0.85rem 1.75rem;
        border-color: #f0f2f5;
        font-size: 0.92rem;
    }
    .table-responsive {
        border: none;
    }
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #ced4da;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background-color: #f0f2f5;
    }

    .chart-card {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 1.5rem;
        border: 1px solid #e9ecef;
    }
    .chart-card h5 {
        color: #343a40;
        font-weight: 600;
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .chart-card h5 .fas {
        color: #6c757d;
        font-size: 1.2rem;
    }

    .badge {
        padding: 0.5em 0.9em; 
        font-size: 0.75em;
        font-weight: 700;
        border-radius: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .badge.bg-success { background-color: #28a745 !important; color: #fff !important; }
    .badge.bg-warning { background-color: #ffc107 !important; color: #343a40 !important; }
    .badge.bg-danger { background-color: #dc3545 !important; color: #fff !important; }

    .text-success { color: #28a745 !important; }
    .text-warning { color: #fd7e14 !important; } 
    .text-danger { color: #dc3545 !important; }
");


?>

<div class="container-fluid mt-4">
    <h2 class="analytics-header">
        <i class="fas fa-chart-line"></i>My Analytics
    </h2>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card metric-card exams">
                <div class="card-body">
                    <i class="fas fa-layer-group icon"></i>
                    <h6>Total Exams Taken</h6>
                    <h3 class="metric-value"><?= Html::encode($totalExams) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card metric-card questions">
                <div class="card-body">
                    <i class="fas fa-question-circle icon"></i>
                    <h6>Total Questions Attempted</h6>
                    <h3 class="metric-value"><?= Html::encode($totalQuestions) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card metric-card accuracy">
                <div class="card-body">
                    <i class="fas fa-bullseye icon"></i>
                    <h6>Overall Accuracy</h6>
                    <h3 class="metric-value"><?= $accuracy !== null ? Html::encode(round($accuracy, 2)) . '%' : '-' ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card metric-card time">
                <div class="card-body">
                    <i class="fas fa-clock icon"></i>
                    <h6>Average Time Per MCQ</h6>
                    <h3 class="metric-value"><?= $avgTimePerMcq ?></h3>
                </div>
            </div>
        </div>
    </div>


    <!-- Topic Stats Table -->
    <div class="card table-card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Chapter-wise Performance</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                        <tr>
                            <th><i class="fas fa-cube me-1"></i>Chapter</th>
                            <th class="text-center"><i class="fas fa-calculator me-1"></i>Attempts</th>
                            <th class="text-center"><i class="fas fa-check-double me-1"></i>Correct</th>
                            <th class="text-center"><i class="fas fa-percent me-1"></i>Accuracy</th>
                            <th class="text-center"><i class="fas fa-award me-1"></i>Strength</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="chapter-row" style="cursor: pointer" data-chapter-id="1">CVS Pathology</td>
                            <td class="text-center">85</td>
                            <td class="text-center">68</td>
                            <td class="text-center">
                                <span class="font-weight-bold text-success">80%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">Good</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="chapter-row" style="cursor: pointer" data-chapter-id="2">Respiratory Pathology
                            </td>
                            <td class="text-center">72</td>
                            <td class="text-center">47</td>
                            <td class="text-center">
                                <span class="font-weight-bold text-warning">65%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">Prep Needed</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="chapter-row" style="cursor: pointer" data-chapter-id="3">General Anatomy</td>
                            <td class="text-center">90</td>
                            <td class="text-center">42</td>
                            <td class="text-center">
                                <span class="font-weight-bold text-danger">47%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger">Weak</span>
                            </td>
                        </tr>
                    </tbody>
</table>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card card-body chart-card">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Accuracy per Topic Overview</h5>
                <div style="max-height: 800px; overflow-y: auto;">
                    <div id="accuracyChart" class="mt-3"></div>

                    <div id="customLegend" style="margin-top:8px;text-align:left;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php


$topics = json_encode([
    'chemical carcinogenesis',
    'oncogenes and proto-oncogenes',
    'local invasion and metastasis',
    'benign vs malignant tumors',
    'molecular basis of cancer',
    'grading and staging of tumors',
    'laboratory diagnosis of cancer',
    'cancer epidemiology and risk factors',
    'nomenclature of tumors',
    'radiation carcinogenesis',
]);



$values = json_encode([
    82.5,
    76.4,
    91.2,
    68.9,
    59.3,
    72.7,
    88.4,
    64.1,
    79.6,
    84.2,
]);


$topicsUrl = Url::to(['default/topics-by-chapter']);
$js = <<<JS
const topics = $topics;
const values = $values;


const dataPoints = values.map((v, i) => ({
  x: topics[i],
  y: v,
  color: v >= 75 ? '#28a745' : v >= 60 ? '#fd7e14' : '#dc3545'
}));

const colors = values.map(v =>
  v >= 75 ? '#28a745' : v >= 60 ? '#fd7e14' : '#dc3545'
);

const options = {
  chart: { type: 'bar', height: 300 },
  plotOptions: {
    bar: {
      horizontal: true,
      barHeight: '70%',
      distributed: true // 🔥 THIS makes per-bar colors work
    }
  },
  colors: colors, // use array of same length as data
  dataLabels: {
    enabled: true,
    formatter: val => val.toFixed(2) + '%'
  },
  xaxis: { categories: topics },
  series: [{
    name: 'Accuracy (%)',
    data: values
  }],
  tooltip: {
    y: { formatter: val => val.toFixed(2) + '%' }
  }
};


const chart = new ApexCharts(document.querySelector("#accuracyChart"), options);
chart.render();

JS;
$this->registerJs($js, View::POS_END);
?>
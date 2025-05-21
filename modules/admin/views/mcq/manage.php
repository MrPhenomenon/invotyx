<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
?>
<style>
  table td,
  table th {
    max-width: 450px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }
</style>
<h3>Manage MCQs</h3>

<div class="card p-3 mb-4">
  <form id="search-form" class="row g-3">
    <div class="col-md-3">
      <label class="form-label">Question ID</label>
      <input type="text" name="question_id" class="form-control" placeholder="Enter ID">
    </div>

    <div class="col-md-3">
      <label class="form-label">Topic</label>
      <select name="topic" class="form-select">
        <option value="">-- All Topics --</option>
        <?php foreach ($topics as $topic): ?>
          <option value="<?= $topic['id'] ?>"><?= $topic['name'] ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Date Range</label>
      <input type="text" name="dates" class="form-control mb-1">
    </div>

    <div class="col-md-3 align-self-center pt-4">
      <button type="submit" class="btn btn-primary h-100 w-25 me-5"> Search</button>
      <button type="button" onclick="location.reload()" class="btn btn-secondary h-100 ms-auto"> Show All Entries</button>
    </div>
  </form>
</div>

<!-- Results Table -->
<div id="results-container">
  <h5>Search Results</h5>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Topic</th>
          <th>Question</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="results-body">
        <?php foreach ($mcqs as $mcq): ?>
          <tr>
            <td><?= $mcq['question_id'] ?></td>
            <td><?= $mcq['topic']['name'] ?></td>
            <td><?= $mcq['question_text'] ?></td>
            <td><?= $mcq['created_at'] ?></td>

            <td class="text-center">
              <button class="btn btn-sm btn-success view-details"
                data-mcq="<?= htmlspecialchars(json_encode($mcq), ENT_QUOTES, 'UTF-8') ?>">Details</button>
              <button class="btn btn-sm btn-info">Update</button>
              <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $mcq['id'] ?>"
                data-item="<?= $mcq['question_id'] ?> MCQ" data-url="/admin/mcq/delete-mcq">Delete</button>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<?php
echo LinkPager::widget([
  'pagination' => $pagination,
  'options' => ['class' => 'pagination justify-content-center mt-4'],
  'linkOptions' => ['class' => 'page-link'],
  'disabledPageCssClass' => 'disabled',
  'activePageCssClass' => 'active ',
  'prevPageLabel' => '&laquo;',
  'nextPageLabel' => '&raquo;',
]);

?>
<div class="modal fade" id="mcqModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">MCQ Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="mcq-modal-body">
        <!-- details inserted by JS -->
      </div>
    </div>
  </div>
</div>

<?php
$js = <<<JS

$('input[name="dates"]').daterangepicker({
  autoUpdateInput: false,
  locale: {
    cancelLabel: 'Clear',
    format: 'YYYY-MM-DD'
  },
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  }
});

$('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
  $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
});

$('input[name="dates"]').on('cancel.daterangepicker', function(ev, picker) {
  $(this).val('');
});

function renderMCQTable(data) {
  const body = $('#results-body').empty();
  console.log(data[0]);

  data.forEach(mcq => {
    const row = $(`
      <tr>
        <td>\${mcq.question_id}</td>
        <td>\${mcq.topic.name || '—'}</td>
        <td>\${mcq.question_text}</td>
        <td>\${mcq.created_at}</td>
        <td class="text-center">
        <button class="btn btn-sm btn-success view-details">Details</button>
          <button class="btn btn-sm btn-info">Update</button>
          <button class="btn btn-sm btn-danger">Delete</button>
        </td>
      </tr>
    `);
    row.find('.view-details').data('mcq', mcq);
    body.append(row);
    $('#results-container').fadeIn();
  });
}


$(document).on('click', '.view-details', function () {
  const mcq = $(this).data('mcq');

  const html = `
    <p><strong>Question ID:</strong> \${mcq.question_id}</p>
    <p><strong>Topic:</strong> \${mcq.topic.name || '—'}</p>
    <p><strong>Question:</strong><br>\${mcq.question_text}</p>
    <p><strong>Options:</strong>
      <ul>
        <li><strong>A:</strong> \${mcq.option_a}</li>
        <li><strong>B:</strong> \${mcq.option_b}</li>
        <li><strong>C:</strong> \${mcq.option_c}</li>
        <li><strong>D:</strong> \${mcq.option_d}</li>
        <li><strong>E:</strong> \${mcq.option_e}</li>
      </ul>
    </p>
    <p><strong>Correct:</strong> \${mcq.correct_option}</p>
    <p><strong>Explanation:</strong><br>\${mcq.explanation || '—'}</p>
    <p><strong>Reference:</strong> \${mcq.reference || '—'}</p>
  `;

  $('#mcq-modal-body').html(html);
  const modal = new bootstrap.Modal(document.getElementById('mcqModal'));
  modal.show();
});


$('#search-form').on('submit', function(e) {
  e.preventDefault()
  const formData = new FormData(this);

  $.ajax({
  type: "POST",
  url: "/admin/mcq/search",
  data: formData,
  processData: false,
  contentType: false,
  success: function (response) {
  const data = response.data;
  let html = '';

  data.forEach(mcq => {
    const mcqJson = JSON.stringify(mcq)
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');

    html += `
      <tr>
        <td>\${mcq.question_id}</td>
        <td>\${mcq.topic.name || '—'}</td>
        <td>\${mcq.question_text}</td>
        <td>\${mcq.created_at}</td>

        <td class="text-center">
          <button class="btn btn-sm btn-success view-details" data-mcq="\${mcqJson}">Details</button>
          <button class="btn btn-sm btn-info">Update</button>
          <button class="btn btn-sm btn-danger btn-delete" data-id="\${mcq.id}" data-item="MCQ \${mcq.question_id}" data-url="/admin/mcq/delete-mcq">Delete</button>
        </td>
      </tr>
    `;
  });

  $('#results-body').html(html);
  $('#results-container').fadeIn();
},
error: function (xhr) {
    console.log('Error:', xhr.responseText);
  }
});
});

JS;
$this->registerJS($js, yii\web\View::POS_END)
  ?>
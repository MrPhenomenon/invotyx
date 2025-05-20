<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<style>
  table td,
  table th {
    max-width: 300px;
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
        <option value="Physiology">Physiology</option>
        <option value="Pathology">Pathology</option>
        <option value="Anatomy">Anatomy</option>
        <!-- Populate dynamically -->
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Date Range</label>
      <input type="text" name="dates" class="form-control mb-1">
    </div>

    <div class="col-md-3 align-self-center pt-4">
      <button type="submit" class="btn btn-primary h-100 w-25"> Search</button>
    </div>
  </form>
</div>

<!-- Results Table -->
<div id="results-container" style="display:none;">
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

      </tbody>
    </table>
  </div>
</div>

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
        <td>
          <button class="btn btn-sm btn-info view-details">Details</button>
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
    
    console.log('Success:', response);
  },
  error: function (xhr) {
    console.log('Error:', xhr.responseText);
  }
});
  
  const html = `
    <tr>
      <td>12345</td>
      <td>Physiology</td>
      <td>What is the resting membrane potential?</td>
      <td>2024-10-01</td>
      <td>
        <button class="btn btn-sm btn-secondary">Edit</button>
        <button class="btn btn-sm btn-primary">Full Details</button>
        <button class="btn btn-sm btn-danger">Delete</button>
      </td>
    </tr>
  `;

  $('#results-body').html(html);
  $('#results-container').fadeIn();
});

const sampleData = [
  {
    question_id: "MCQ001",
    question_text: "A 30-year-old male presents with muscle pain and weakness after an intense workout. He reports no history of trauma, and his creatine kinase (CK) levels are elevated. Which of the following biomarkers would be most useful for assessing muscle injury?",
    option_a: "Dopamine",
    option_b: "Serotonin",
    option_c: "Acetylcholine",
    option_d: "GABA",
    option_e: "Norepinephrine",
    correct_option: "C",
    explanation: "The most appropriate biomarker for assessing muscle injury is myoglobin. Myoglobin is released into the bloodstream following muscle injury, as it is a protein found in muscle tissue that binds oxygen. It is highly sensitive and can be detected shortly after muscle damage, making it useful for early detection of muscle injury.Option B: CKMB is more specific to myocardial injury and is used to assess cardiac muscle damage. It is not the best choice for muscle injury assessment.Option C: CKBB is predominantly found in the brain and is not typically used for muscle injury assessmentOption D: CKMM is the isoenzyme of creatine kinase found in skeletal muscle, and although it is elevated in muscle injury, it is less sensitive than myoglobin and does not provide as rapid an indication of muscle damage.Option E: LDH (Lactate Dehydrogenase) is an enzyme found in many tissues, including muscle, but it is not as specific or sensitive for muscle injury as myoglobin.",
    reference: "Guyton and Hall Textbook of Medical Physiology, 13th Edition, Chapter 67, 'Muscle Physiology and Pathophysiology', p. 1056-1060.",
    topic: { id: 2, name: "Pathology" },
    created_at: "2025-05-10 12:30:00"
  },
  {
    question_id: "MCQ002",
    question_text: "Which vitamin deficiency leads to rickets in children?",
    option_a: "Vitamin A",
    option_b: "Vitamin B12",
    option_c: "Vitamin C",
    option_d: "Vitamin D",
    option_e: "Vitamin E",
    correct_option: "D",
    explanation: "Vitamin D deficiency impairs calcium absorption, leading to rickets in children.",
    reference: "Nelson Textbook of Pediatrics",
    topic: { id: 2, name: "Pathology" },
    created_at: "2025-05-15 09:45:00"
  },
  {
    question_id: "MCQ003",
    question_text: "The muscle responsible for abduction of the shoulder is:",
    option_a: "Deltoid",
    option_b: "Biceps brachii",
    option_c: "Trapezius",
    option_d: "Latissimus dorsi",
    option_e: "Supraspinatus",
    correct_option: "A",
    explanation: "The deltoid muscle primarily abducts the arm at the shoulder joint.",
    reference: "Gray's Anatomy",
    topic: { id: 3, name: "Anatomy" },
    created_at: "2025-05-18 14:20:00"
  }
];

JS;
$this->registerJS($js, yii\web\View::POS_END)
  ?>



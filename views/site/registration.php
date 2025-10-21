<?php
$this->title = 'Register';
$planColors = ['primary', 'success', 'danger'];
$planIndex = 1;
use yii\helpers\Url;
$this->registerCssFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', [
    'depends' => [\yii\bootstrap5\BootstrapAsset::class],
]);
?>

<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
    }

    .choices__list--multiple .choices__item {
        background-color: #0e273c;
        border: #0e273c;
    }
</style>

<div class="container mt-5">

    <div class="progress-bar">
        <div class="progress-step is-active"> <!-- Step 1 -->
            <div class="step-count"></div> <!-- Empty for counter -->
            <div class="step-description">Register</div>
        </div>
        <div class="progress-step"> <!-- Step 2 -->
            <div class="step-count"></div>
            <div class="step-description">Select a Plan</div>
        </div>
        <div class="progress-step"> <!-- Step 3 (Active) -->
            <div class="step-count"></div>
            <div class="step-description">Setup Profile</div>
        </div>
    </div>



    <div id="registration-step">
        <form id="register" data-url="<?= Url::to(['site/register-user']) ?>">
            <!-- Step 1: Sign Up -->
            <div class="step active" data-step="1">
                <section class="p-2 mb-5">
                    <div class="container card card-body p-5 shadow border-0 w-50">
                        <h3>Create Account</h3>
                        <div class="row gy-3 mt-2 overflow-hidden">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="John Doe"
                                        required>
                                    <label for="name" class="form-label">Name</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        placeholder="Phone Number" required>
                                    <label for="phone" class="form-label">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="name@example.com" required>
                                    <label for="email" class="form-label">Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" value=""
                                        placeholder="Password" required>
                                    <label for="password" class="form-label">Password</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" name="password" id="password2" value=""
                                        placeholder="Confirm Password" required>
                                    <label for="password2" class="form-label">Confirm Password</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-grid">
                                    <button class="btn btn-primary next-btn" type="button">Next</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            </div>

            <!-- Step 2: Subscription -->
            <div class="step" data-step="2">
                <section id="pricing" class="pricing section pt-5">
                    <div class="container">
                        <div class="row gy-4 justify-content-center">

                            <?php foreach ($plans as $index => $plan): ?>
                                <div class="col-xl-3 col-md-6 d-flex">
                                    <div class="pricing-item  w-100 d-flex flex-column <?= $index === 0 ? 'featured' : '' ?> <?= !$plan['active'] ? 'opacity-50' : '' ?> plan-card"
                                        data-plan="<?= $plan['id'] ?>"
                                        style="<?= !$plan['active'] ? 'pointer-events: none;' : '' ?>">

                                        <div class="mb-3 text-center">
                                            <h3><?= htmlspecialchars($plan['name']) ?></h3>
                                            <h4><sup>$</sup><?= $plan['price'] ?><span> / <?= $plan['duration_days'] ?>
                                                    days</span></h4>
                                        </div>

                                        <ul class="list-unstyled flex-grow-1">
                                            <?php
                                            $decoded = json_decode($plan['features_json'], true);
                                            if (is_string($decoded)) {
                                                $decoded = json_decode($decoded, true);
                                            }
                                            $features = is_array($decoded) ? $decoded : [];
                                            foreach ($features as $f):
                                                $isAvailable = strpos($f, '[x]') === false;
                                                $text = str_replace('[x]', '', $f);
                                                ?>
                                                <li
                                                    class="mb-2 d-flex align-items-center <?= $isAvailable ? '' : 'text-secondary' ?>">
                                                    <i
                                                        class="bi <?= $isAvailable ? 'bi-check-circle-fill text-success' : 'bi-x-circle text-secondary' ?> me-2"></i>
                                                    <?= htmlspecialchars($text) ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>

                                        <!-- hidden radio -->
                                        <input type="radio" class="btn-check plan-radio" name="subscription_id"
                                            id="plan-<?= $plan['id'] ?>" value="<?= $plan['id'] ?>" required>
                                    </div>
                                </div>

                            <?php endforeach; ?>

                            <div class="col-12 text-center mt-4">
                                <button type="button" class="btn btn-primary next-btn px-4">Next</button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Step 3: Profile Config -->
            <div class="step" data-step="3">
                <section class="p-2 mb-5">
                    <div class="container card card-body p-5 shadow border-0 w-50">
                        <h3>Profile</h3>
                        <div class="row gy-3 mt-2 overflow-hidden">
                            <div class="col-12">
                                <label for="" class="form-label">Exam Type</label>
                                <select name="exam_type" class="form-select py-3" id="examSelect" required
                                    data-url="<?= Url::to(['site/get-specialization']) ?>">
                                    <option value="">Select Your Exam</option>
                                    <?php foreach ($exams as $exam): ?>
                                        <option value="<?= $exam['id'] ?>"><?= $exam['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Exam Specialization</label>
                                <select name="specialty_id" class="form-select py-3" id="specializationSelect" required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Expected Exam Date</label>
                                <input type="date" class="form-control py-3" name="expected_exam_date"
                                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">MCQs per day (Recommended: 150 - 180)</label>
                                <input type="number" class="form-control py-3" name="mcqs_per_day" required min="100">
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Subjects you feel weak in (Optional)</label>
                                <select name="weak_subjects[]" id="subjectSelect" class="form-select py-3" id=""
                                    multiple>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Would you like to take a Pre-Evaluation Exam ?</label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="evaluation" id="inlineRadio1"
                                        value="1">
                                    <label class="form-check-label" for="inlineRadio1">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="evaluation" id="inlineRadio2"
                                        value="0">
                                    <label class="form-check-label" for="inlineRadio2">No</label>
                                </div>
                            </div>

                            <div class="col-12 mt-4 ">
                                <div class="d-grid">
                                    <button class="btn btn-primary" type="submit">Finish Profile</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </form>
    </div>
</div>


<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js', [
    'depends' => [\yii\web\JqueryAsset::class],
]);
$js = <<<JS

$(document).on("click", ".plan-card", function () {
    $(".plan-card").removeClass("active");
    $(this).addClass("active");
    $("#plan-" + $(this).data("plan")).prop("checked", true);
});

    $('#register').on('submit', function(e){
        e.preventDefault();

        const form = new FormData(this);

        $.ajax({
            type: "POST",
             url: \$(this).data('url'),
            data: form,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                showToast('User Registration Complete, redirecting...');
                setTimeout(() => {
                    window.location.href = res.redirect
                }, 1000);
            } else {
                if (res.err?.email) {
                showToast('This email is already in use. Please log in or use another.', 'danger');
                } else {
                showToast('Something went wrong. Please try again.', 'danger');
                }
            }
        }

        });
    })
    $('#examSelect').on('change', function(e){
    showloader();
    const id = $(this).val();

    $.ajax({
        type: "POST",
         url: \$(this).data('url'), 
        data: { id: id },
        success: function (res) {
            hideloader();
            const specializationSelect = $('#specializationSelect');
            specializationSelect.empty();

            if (res.data && res.data.length > 0) {
                specializationSelect.append('<option value="">Select</option>');
                res.data.forEach(function(item){
                    specializationSelect.append(
                        `<option value="\${item.id}">\${item.name}</option>`
                    );
                });
            } else {
                specializationSelect.append('<option value="">No specializations found</option>');
            }
        }
    });
});

const subjectSelectChoice = new Choices('#subjectSelect', {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select subjects',
    noChoicesText: 'No subjects available'
});

    function validateCurrentStep() {
         const currentTab = document.querySelector('.step.active');
         const fields = currentTab.querySelectorAll('input, select, textarea');

     for (let field of fields) {
       if (!field.checkValidity()) {
         field.reportValidity();
         return false;
       }
     }

  return true;
}


    let currentStep = 1;

    function showStep(step) {
  $('.step').removeClass('active');
  $(`.step[data-step="\${step}"]`).addClass('active');
}


$(document).on('click', '.next-btn', function () {
  if (!validateCurrentStep()) return;
  currentStep++;
  updateProgressBar(currentStep);
  showStep(currentStep);
});


$(document).on('click', '.back-btn', function () {
  currentStep--;
  showStep(currentStep);
});

function updateProgressBar(step) {
  const steps = document.querySelectorAll('.progress-bar .progress-step');
  
  steps.forEach((el, index) => {
    el.classList.toggle('is-active', index === step - 1);
  });
}


JS;
$this->registerJS($js, yii\web\View::POS_END);
?>
<?php
$this->title = 'Register';
$planColors = ['primary', 'success', 'danger'];
$planIndex = 1;
?>

<style>
    .step {
        display: none;
    }

    .step.active {
        display: block;
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
        <form id="register">
            <!-- Step 1: Sign Up -->
            <div class="step active" data-step="1">
                <section class="p-3 p-md-4 p-xl-5">
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
                <section id="pricing" class="pricing section">
                    <div class="container">
                        <div class="row gy-4 justify-content-center">
                            <div class="col-12 text-center mb-4">
                                <h3>Select a Subscription</h3>
                            </div>

                            <?php foreach ($plans as $index => $plan): ?>
                                <div class="col-xl-3 col-md-6 d-flex">
                                    <div class="pricing-item shadow p-4 w-100 d-flex flex-column <?= $index === 1 ? 'featured' : '' ?>"
                                        style="<?= $index === 1 ? 'scale: 1.05; z-index: 9;' : '' ?>">
                                        <div class="mb-3 text-center">
                                            <h3><?= htmlspecialchars($plan['name']) ?></h3>
                                            <h4><sup>$</sup><?= $plan['price'] ?><span> / <?= $plan['duration_days'] ?>
                                                    days</span></h4>
                                        </div>

                                        <ul class="list-unstyled flex-grow-1">
                                            <?php
                                            $features = json_decode($plan['features_json'], true);
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

                                        <div class="btn-wrap mt-3 text-center">
                                            <input type="radio" class="btn-check" name="subscription_id"
                                                id="plan-<?= $plan['id'] ?>" value="<?= $plan['id'] ?>" required>
                                            <label class="btn btn-outline-primary px-4"
                                                for="plan-<?= $plan['id'] ?>">Select</label>
                                        </div>
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
                <section class="p-3 p-md-4 p-xl-5">
                    <div class="container card card-body p-5 shadow border-0 w-50">
                        <h3>Profile</h3>
                        <div class="row gy-3 mt-2 overflow-hidden">
                            <div class="col-12">
                                <label for="" class="form-label">Exam Type</label>
                                <select name="exam_type" class="form-select py-3" id="examSelect" required>
                                    <option value="">Select Your Exam</option>
                                    <?php foreach ($exams as $exam): ?>
                                        <option value="<?= $exam['id'] ?>"><?= $exam['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Exam Specialization</label>
                                <select name="speciality_id" class="form-select py-3" id="specializationSelect"
                                    required>
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Expected Exam Date</label>
                                <input type="date" class="form-control py-3" name="expected_exam_date"
                                    min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
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
$js = <<<JS
    $('#register').on('submit', function(e){
        e.preventDefault();

        const form = new FormData(this);

        $.ajax({
            type: "POST",
            url: "/site/register-user",
            data: form,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                showToast('User Registration Complete');
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
        url: "/site/get-specialization",
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
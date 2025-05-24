<?php
$this->title = 'Login';
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

        <!-- Step 1: Sign Up -->
        <div class="step active" data-step="1">
            <section class="p-3 p-md-4 p-xl-5">
                <div class="container card card-body p-5 shadow border-0 w-50">
                    <h3>Registration</h3>
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
                                <input type="password" class="form-control" name="password" id="password" value=""
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
                                <button class="btn btn-primary next-btn" type="button">Register</button>
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


                    <div class="row gy-3 justify-content-center">
                        <dov class="col-12 offset-3 mb-4">
                            <h3>Select a Subscription</h3>
                        </dov>

                        <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="pricing-item shadow">
                                <h3>Free</h3>
                                <h4><sup>$</sup>0<span> / month</span></h4>
                                <ul>
                                    <li>Aida dere</li>
                                    <li>Nec feugiat nisl</li>
                                    <li>Nulla at volutpat dola</li>
                                    <li class="na">Pharetra massa</li>
                                    <li class="na">Massa ultricies mi</li>
                                </ul>
                                <div class="btn-wrap">
                                    <input type="radio" class="btn-check" name="options-outlined" id="plan-1"
                                        autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm px-4" for="plan-1">Select</label>
                                </div>
                            </div>
                        </div><!-- End Pricing Item -->

                        <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="pricing-item featured shadow" style="scale: 1.1; z-index: 9;">
                                <h3>Business</h3>
                                <h4><sup>$</sup>19<span> / month</span></h4>
                                <ul>
                                    <li>Aida dere</li>
                                    <li>Nec feugiat nisl</li>
                                    <li>Nulla at volutpat dola</li>
                                    <li>Pharetra massa</li>
                                    <li class="na">Massa ultricies mi</li>
                                </ul>
                                <div class="btn-wrap">
                                    <input type="radio" class="btn-check" name="options-outlined" id="plan-2"
                                        autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm px-4" for="plan-2">Select</label>
                                </div>
                            </div>
                        </div><!-- End Pricing Item -->

                        <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="400">
                            <div class="pricing-item shadow">
                                <h3>Developer</h3>
                                <h4><sup>$</sup>29<span> / month</span></h4>
                                <ul>
                                    <li>Aida dere</li>
                                    <li>Nec feugiat nisl</li>
                                    <li>Nulla at volutpat dola</li>
                                    <li>Pharetra massa</li>
                                    <li>Massa ultricies mi</li>
                                </ul>
                                <div class="btn-wrap">
                                    <input type="radio" class="btn-check" name="options-outlined" id="plan-3"
                                        autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm px-4" for="plan-3">Select</label>
                                </div>
                            </div>
                        </div><!-- End Pricing Item -->

                        <div class="col-6 text-end offset-3 mt-4">
                            <div class="btn btn-primary next-btn px-4">Next</div>
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
                            <select name="" class="form-select py-3" id="">
                                <option value="">MBBS</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="" class="form-label">Exam Specialization</label>
                            <select name="" class="form-select py-3" id="">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-12">
                           <label for="" class="form-label">Expected Exam Date</label>
                           <input type="date" class="form-control py-3">
                        </div>

                        <div class="col-12 mt-4 ">
                            <div class="d-grid">
                                <button class="btn btn-primary back-btn" type="button">Finish Profile</button>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>

    </div>
</div>


<?php
$js = <<<JS
    $('#admin-form').on('submit', function(e){
        e.preventDefault();

        const form = new FormData(this);

        $.ajax({
            type: "POST",
            url: "/site/login-admin",
            data: form,
            processData: false,
            contentType: false,
            success: function (res) {
                res.success ? window.location.href = "/admin" : alert(res.message);
            }
        });
    })

    let currentStep = 1;

    function showStep(step) {
  $('.step').removeClass('active');
  $(`.step[data-step="\${step}"]`).addClass('active');
}


$(document).on('click', '.next-btn', function () {
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
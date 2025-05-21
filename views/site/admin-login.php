<section class="p-3 p-md-4 p-xl-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-11">
                <div class="card border-light-subtle shadow-sm">
                    <div class="row g-0">
                        <div class="col-12 col-md-6">
                            <img class="img-fluid rounded-start object-fit-cover" loading="lazy"
                                src="/dassets/images/authentication/img-auth-bg.svg" alt="Side Banner">
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
                            <div class="col-12 col-lg-11 col-xl-10">
                                <div class="card-body p-3 p-md-4 p-xl-5">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-5">
                                                <div class="text-center mb-4">

                                                    <img src="./assets/img/bsb-logo.svg" alt="App Logo" width="175"
                                                        height="57">

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="admin-form">
                                        <div class="row gy-3 overflow-hidden">
                                            <div class="col-12">
                                                <div class="form-floating mb-3">
                                                    <input type="email" class="form-control" name="email" id="email"
                                                        placeholder="name@example.com" required>
                                                    <label for="email" class="form-label">Email</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating mb-3">
                                                    <input type="password" class="form-control" name="password"
                                                        id="password" value="" placeholder="Password" required>
                                                    <label for="password" class="form-label">Password</label>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button class="btn btn-primary btn-lg" type="submit">Log in
                                                        now</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
JS;
$this->registerJS($js, yii\web\View::POS_END);
?>
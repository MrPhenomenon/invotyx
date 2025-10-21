<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = 'Login';
?>

<section class="p-3 p-md-4 p-xl-5">
    <div class="container">
        <div class="row justify-content-center">
            <!-- Simplified column to center the form card -->
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card border-light-subtle shadow-sm">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-4">
                            
                            <h2 class="fw-bold mt-4">Sign In</h2>
                            <p class="text-muted">Enter your credentials to access your account.</p>
                        </div>

                        <!-- Container for server-side validation errors -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger text-center mb-3">
                                <?= Html::encode($error) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Container for client-side (AJAX) errors -->
                        <div id="ajax-error-message" class="alert alert-danger text-center mb-3" style="display: none;"></div>

                        <form id="user-login" data-url="<?= Url::to(['site/login']) ?>">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken()) ?>
                            
                            <div class="row gy-3">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                                        <label for="email" class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" name="password" id="password" value="" placeholder="Password" required>
                                        <label for="password" class="form-label">Password</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg" type="submit" id="login-button">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                            <span class="button-text">LOGIN</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-12">
                                <!-- A cleaner separator for the social login option -->
                                <hr class="my-4">
                                <div class="d-grid">
                                    <!-- Using Url::to for the Google auth link -->
                                    <a href="<?= Url::to(['site/auth', 'authclient' => 'google']) ?>" title="Google" class="btn btn-outline-primary btn-lg">
                                        <i class="bi bi-google me-2"></i>Login with Google
                                    </a>
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
$('#user-login').on('submit', function(e) {
    e.preventDefault();

    const form = $(this);
    const button = $('#login-button');
    const buttonText = button.find('.button-text');
    const spinner = button.find('.spinner-border');
    const errorMessageDiv = $('#ajax-error-message');
    
    // Provide user feedback
    button.prop('disabled', true);
    spinner.show();
    buttonText.text('LOGGING IN...');
    errorMessageDiv.hide();

    $.ajax({
        type: "POST",
        url: form.data('url'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.success && res.redirectUrl) {
                window.location.href = res.redirectUrl;
            } else {
                showToast(res.message || 'Login failed.', 'danger');
                spinner.hide();
                buttonText.text('LOGIN');
                button.prop('disabled', false);
            }
            hideloader();
        },
    });
});
JS;
$this->registerJS($js, yii\web\View::POS_END);
?>
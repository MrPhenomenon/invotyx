<section class="p-3 p-md-4 p-xl-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-11">
                <div class="card border-light-subtle shadow-sm overflow-hidden">
                    <div class="row g-0">
                        <div class="col-12 col-md-6">
                            <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" loading="lazy"
                                src="/dassets/images/authentication/img-auth-bg.svg" alt="Side Banner" style="max-height: 650px; min-height: 100%;">
                        </div>
                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="card-body p-3 p-md-4 p-xl-5 w-100">
                                <div class="text-center mb-4">
                                    <h2 class="fw-bold">Sign In</h2>
                                </div>
                                <form id="admin-form">
                                    <div class="row gy-3">
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

                                        <!-- Updated Button Section -->
                                        <div class="col-12">
                                            <div class="d-grid mb-3"> 
                                                <button class="btn btn-primary btn-lg" type="submit">LOGIN</button>
                                            </div>

                                            <div class="text-center mb-3">
                                                <small>or</small>
                                            </div>

                                            <div class="d-grid"> 
                                                <a href="/site/auth?authclient=google" title="Google"
                                                    class="google auth-link btn btn-primary btn-lg">
                                                    <i class="bi bi-google me-2"></i>Login with Google
                                                </a>
                                            </div>
                                        </div>
                                        <!-- End Updated Button Section -->

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
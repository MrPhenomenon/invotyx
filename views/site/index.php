<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
    <!-- Hero Section -->
    <section id="hero" class="hero section pb-0 pt-0"> <!-- Removed pb-0, padding will be handled by CSS -->

      <div class="container">
        <div class="row align-items-center">

          <!-- Text Content Column -->
          <div class="col-lg-6 hero-text-content" data-aos="fade-right" data-aos-delay="100">
            <h2>Your personal <br> AI-Powered Study Partner for Exam Success</h2>
            <p>This isn’t just another MCQ app.
              This is your personalized, intelligent, exam-focused preparation companion
            </p>

            <div class="mt-3 d-flex gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="250">
              <span class="badge rounded-pill bg-primary"><i class="bi bi-graph-up-arrow"></i> Adaptive Learning</span>
              <span class="badge rounded-pill bg-primary"><i class="bi bi-bar-chart-fill"></i> Analytics</span>
              <span class="badge rounded-pill bg-primary"><i class="bi bi-lock-fill"></i> Secure Mode</span>
            </div>


            <div class="d-flex mt-5">
              <a href="register" class="btn-get-started">Get Started</a>
            </div>
          </div>

          <!-- Image Content Column -->
          <div class="col-lg-6 hero-image-column text-center text-lg-end" data-aos="fade-left" data-aos-delay="300">
            <div class="hero-image-blob-container">
              <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/hero-bg.png" alt="AI Study Partner" class="img-fluid">
            </div>
          </div>

        </div>
      </div>

    </section>

    <section id="counts" class="section counts light-background">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4">
    
          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="1200" data-purecounter-duration="1" class="purecounter"></span>
              <p>Registered Students</p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="3800" data-purecounter-duration="1" class="purecounter"></span>
              <p>Exams Taken</p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="45000" data-purecounter-duration="1" class="purecounter"></span>
              <p>MCQs Attempted</p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="95" data-purecounter-duration="1" class="purecounter"></span>
              <p>% Student Satisfaction</p>
            </div>
          </div>
  
        </div>
      </div>
    </section>
    

    <!-- SECTION 1: SMART STUDY TOOLKIT - TOP PART -->
    <section class="py-5">
      <div class="container">

        <h2 class="text-center text-uppercase fw-bold mb-5 text-primary" data-aos="fade-up">What Makes Us Different?
        </h2>
        <div class="row align-items-center">
          <div class="col-lg-6 col-md-7 mb-4 mb-lg-0" data-aos="fade-right">
            <div class="card shadow-sm border-0 p-3 mb-3">
              <div class="card-body d-flex align-items-center">
                <i class="bi bi-journal-text fs-2 text-primary me-3"></i>
                <div>
                  <h5 class="card-title fw-bold mb-1">Topic-wise Study</h5>
                  <p class="card-text text-muted small mb-0">Practice MCQs by topic</p>
                </div>
              </div>
            </div>
            <div class="card shadow-sm border-0 p-3 mb-3">
              <div class="card-body d-flex align-items-center">
                <i class="bi bi-file-earmark-medical fs-2 text-primary me-3"></i>
                <div>
                  <h5 class="card-title fw-bold mb-1">Mock Exams</h5>
                  <p class="card-text text-muted small mb-0">Structured by medical System</p>
                </div>
              </div>
            </div>
            <div class="card shadow-sm border-0 p-3">
              <div class="card-body d-flex align-items-center">
                <i class="bi bi-sliders fs-2 text-primary me-3"></i>
                <div>
                  <h5 class="card-title fw-bold mb-1">Practice Mode & Exam Mode</h5>
                  <p class="card-text text-muted small mb-0">Simulate real exam conditions</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-5 text-center text-md-end shadow-sm" data-aos="fade-left" data-aos-delay="200">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/dashboard.png" alt="Image of User Panel will go here" class="img-fluid"
              style="max-height: 500px;">
            <!-- Replace with your actual image -->
          </div>
        </div>
      </div>
    </section>


    <!-- SECTION 2: SMART STUDY TOOLKIT - FEATURES -->
    <section class="py-5 bg-primary">
      <div class="container">
        <h2 class="text-center text-uppercase fw-bold mb-5 text-white" data-aos="fade-up">Your Smart Study Toolkit</h2>
        <div class="row gy-4">
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card shadow-sm border-0 p-3 h-100 text-center">
              <div class="card-body d-flex flex-column align-items-center">
                <i class="bi bi-bank fs-1 text-primary mb-2"></i>
                <h5 class="card-title fw-bold mb-1">MCQ Bank</h5>
                <p class="card-text text-muted small">Authentic, reference backed questions</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card shadow-sm border-0 p-3 h-100 text-center">
              <div class="card-body d-flex flex-column align-items-center">
                <i class="bi bi-pc-display fs-1 text-primary mb-2"></i>
                <h5 class="card-title fw-bold mb-1">Mock Exams</h5>
                <p class="card-text text-muted small">Two full mocks before exam</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card shadow-sm border-0 p-3 h-100 text-center">
              <div class="card-body d-flex flex-column align-items-center">
                <i class="bi bi-bar-chart-line fs-1 text-primary mb-2"></i>
                <h5 class="card-title fw-bold mb-1">Analytics</h5>
                <p class="card-text text-muted small">Progress & performance tracking</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="card shadow-sm border-0 p-3 h-100 text-center">
              <div class="card-body d-flex flex-column align-items-center">
                <i class="bi bi-pie-chart fs-1 text-primary mb-2"></i> <!-- Or bi-calendar-check for planning -->
                <h5 class="card-title fw-bold mb-1">Study Planner</h5>
                <p class="card-text text-muted small">Personalized plans based-on weak areas</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  

    <section id="features-list" class="features-list-section py-5">
      <div class="container">
        <div class="text-center pb-5">
          <h2 class="fw-bold">What Makes Us Different?</h2>
        </div>
        <div class="row gy-4">

          <!-- Feature 1: High-Yield MCQs -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-patch-check-fill fs-2 text-primary"></i>
              </div>
              <div>
                <h4 class="fw-bold">High-Yield, Exam-Focused MCQs</h4>
                <p class="text-muted">
                  Every question is carefully crafted to reflect real exam patterns — high yield, authentic, and
                  thoroughly referenced from trusted medical literature.
                </p>
              </div>
            </div>
          </div>

          <!-- Feature 2: Chapter-wise & System-wise Prep -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-journals fs-2 text-primary"></i>
              </div>
              <div>
                <h4 class="fw-bold">Chapter-wise & System-wise Prep</h4>
                <p class="text-muted">
                  Master one topic at a time or zoom out to tackle an entire system. Flex your preparation your way.
                </p>
              </div>
            </div>
          </div>

          <!-- Feature 3: Practice Mode & Exam Mode -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-toggles2 fs-2 text-primary"></i> <!-- or bi-sliders -->
              </div>
              <div>
                <h4 class="fw-bold">Practice Mode & Exam Mode</h4>
                <p class="text-muted mb-2">
                  Switch between learning and testing:
                </p>
                <ul class="list-unstyled text-muted ps-3">
                  <li class="mb-1"><i class="bi bi-check-lg text-success me-2"></i><strong>Practice Mode:</strong> Learn
                    with explanations</li>
                  <li><i class="bi bi-check-lg text-success me-2"></i><strong>Exam Mode:</strong> Simulated environment,
                    no hints, strict timer — just like the real thing</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Feature 4: Mock Exams -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-pc-display-horizontal fs-2 text-primary"></i> <!-- or bi-puzzle-fill -->
              </div>
              <div>
                <h4 class="fw-bold">Mock Exams with Real Exam Simulation</h4>
                <p class="text-muted">
                  Timed, two-part exams with AI-enforced trust breach detection (tab switch alerts, resume on
                  reconnect).
                  Feel the pressure before the real thing — and master it.
                </p>
              </div>
            </div>
          </div>

          <!-- Feature 5: Smart Performance Analytics -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-bar-chart-line-fill fs-2 text-primary"></i>
              </div>
              <div>
                <h4 class="fw-bold">Smart Performance Analytics</h4>
                <p class="text-muted">
                  See how you perform, what your weak areas are, and how much time you're spending — with detailed
                  breakdowns after every session.
                </p>
              </div>
            </div>
          </div>

          <!-- Feature 6: Personalized Study Plans -->
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="feature-item d-flex align-items-start">
              <div class="feature-icon-wrap me-3">
                <i class="bi bi-calendar-check-fill fs-2 text-primary"></i>
              </div>
              <div>
                <h4 class="fw-bold">Personalized Study Plans</h4>
                <p class="text-muted">
                  The app learns from your progress and builds a dynamic plan tailored to your strengths, weaknesses,
                  and goals.
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- SECTION 5: CALL TO ACTION -->
    <section class="py-5 bg-light">
      <div class="container text-center" data-aos="fade-up" data-aos-delay="100">
        <h2 class="text-uppercase fw-bold mb-4 text-primary">Start Your Journey Now</h2>
        <a href="#" class="btn btn-primary btn-lg rounded-pill px-4 py-2">Register & Start Free Trial</a>
      </div>
    </section>

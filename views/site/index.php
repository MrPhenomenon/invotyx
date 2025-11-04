<?php

use yii\web\View;

/** @var yii\web\View $this */

$this->title = 'Part 1 AI - Smart Study Partner';

?>
<section id="hero" class="hero-section-new p-0">
  <div class="container" style="max-width: 1500px">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-delay="100">
        <h1>Your personal <br> AI-Powered Study Partner for Exam Success</h1>
        <p>
         This isn’t just another MCQ app.
          This is your personalized, intelligent, exam-focused preparation companion.
        </p>
        <div class="my-3 d-flex gap-3 flex-wrap aos-init aos-animate" data-aos="fade-up" data-aos-delay="250">
          <span class="badge rounded-pill bg-danger"><i class="bi bi-graph-up-arrow"></i> Adaptive Learning</span>
          <span class="badge rounded-pill bg-danger"><i class="bi bi-bar-chart-fill"></i> Analytics</span>
          <span class="badge rounded-pill bg-danger"><i class="bi bi-lock-fill"></i> Secure Mode</span>
        </div>
        <a href="register" class="btn-hero mt-2" data-aos="fade-up" data-aos-delay="300">Get Started</a>
      </div>
      <div class="col-lg-6 text-center hero-image-blob-container" data-aos="fade-left" data-aos-delay="200">
        <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/hero-bg(2).png" class="img-fluid"
          alt="AI Study Partner Dashboard">
      </div>
    </div>
  </div>
</section>


<section id="usp" class="usp-section py-5">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">Seamlessly Integrated Study Experience</h2>
      <p class="section-subheading">Unlock your full potential with an intelligent platform designed for ultimate exam preparation.</p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="usp-item">
          <div class="usp-icon"><i class="bi bi-cpu"></i></div>
          <div class="usp-content">
            <h3>AI-Driven Personalization</h3>
            <p>Our algorithms learn your strengths and weaknesses to deliver a truly custom study path.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="usp-item">
          <div class="usp-icon"><i class="bi bi-search"></i></div>
          <div class="usp-content">
            <h3>Detailed Performance Insights</h3>
            <p>Gain clarity with in-depth analytics, helping you focus exactly where it matters most.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="usp-item">
          <div class="usp-icon"><i class="bi bi-clock-history"></i></div>
          <div class="usp-content">
            <h3>Time-Efficient Study</h3>
            <p>Optimize your study hours with high-yield questions and adaptive scheduling.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="app-screenshots" class="app-screenshots-section">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">A Glimpse Inside Your Study Partner</h2>
      <p class="section-subheading">
        Discover the intuitive interface and powerful features designed to simplify your exam preparation.
      </p>
    </div>

    <div class="row gy-4 mt-5 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="screenshot-card text-center h-100">
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-1.jpg"
             class="glightbox"
             data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-1.jpg"
                 class="img-fluid rounded shadow"
                 alt="App Dashboard Overview">
          </a>
          <h5 class="mt-3">Dashboard Overview</h5>
          <p>Your personalized hub to track progress and access study modules.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="screenshot-card text-center h-100">
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-2.jpg"
             class="glightbox"
             data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-2.jpg"
                 class="img-fluid rounded shadow"
                 alt="Adaptive MCQ Practice Interface">
          </a>
          <h5 class="mt-3">Adaptive Questions</h5>
          <p>Engage with AI-powered questions tailored to your learning needs.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="screenshot-card text-center h-100">
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-3.jpg"
             class="glightbox"
             data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-3.jpg"
                 class="img-fluid rounded shadow"
                 alt="Performance Analytics Charts">
          </a>
          <h5 class="mt-3">Performance Analytics</h5>
          <p>Visualize your strengths and weaknesses with detailed reports.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<section id="features" class="features-grid-section py-5">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">Tools Designed for Your Academic Excellence</h2>
      <p class="section-subheading">From comprehensive question banks to secure mock exams, we've got you covered.</p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="feature-card">
          <i class="bi bi-book icon"></i>
          <h3>Extensive MCQ Library</h3>
          <p>Access thousands of expert-vetted, referenced multiple-choice questions covering all key topics.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="feature-card">
          <i class="bi bi-award icon"></i>
          <h3>Authentic Mock Exams</h3>
          <p>Prepare with full-length simulated exams under realistic conditions, mirroring your actual test.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-card">
          <i class="bi bi-graph-up icon"></i>
          <h3>Smart Progress Tracking</h3>
          <p>Monitor your learning journey with intuitive charts and graphs that highlight your improvements.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
        <div class="feature-card">
          <i class="bi bi-calendar-event icon"></i>
          <h3>Personalized Study Plans</h3>
          <p>Let AI craft a dynamic study schedule, ensuring you cover all essential material efficiently.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
        <div class="feature-card">
          <i class="bi bi-lightbulb icon"></i>
          <h3>Detailed Explanations</h3>
          <p>Reinforce learning with clear, comprehensive explanations for every question, enhancing understanding.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
        <div class="feature-card">
          <i class="bi bi-lock icon"></i>
          <h3>Secure Exam Environment</h3>
          <p>Practice in a secure mode with trust-breach detection to build confidence for the real exam day.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="how-it-works" class="process-section">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">Achieve Your Goals in 3 Simple Steps</h2>
      <p class="section-subheading">Getting started with your AI-powered study partner is easy and intuitive.</p>
    </div>

    <div class="position-relative d-none d-md-block">
      <div class="process-line"></div>
    </div>

    <div class="row justify-content-center mt-5 position-relative gy-5"> <!-- Added mt-5 for better spacing -->
      <div class="col-md-4 d-flex justify-content-center">
        <div class="process-step-item" data-aos="fade-up" data-aos-delay="100">
          <div class="step-number">1</div>
          <h3>Register & Onboard</h3>
          <p>Sign up, complete a brief profile, and let our AI begin understanding your learning style.</p>
        </div>
      </div>
      <div class="col-md-4 d-flex justify-content-center">
        <div class="process-step-item" data-aos="fade-up" data-aos-delay="200">
          <div class="step-number">2</div>
          <h3>Engage & Adapt</h3>
          <p>Dive into practice questions and mock exams. Our platform adapts to your performance in real-time.</p>
        </div>
      </div>
      <div class="col-md-4 d-flex justify-content-center">
        <div class="process-step-item" data-aos="fade-up" data-aos-delay="300">
          <div class="step-number">3</div>
          <h3>Succeed with Confidence</h3>
          <p>Build exam readiness, track your mastery, and confidently achieve your academic objectives.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<section id="testimonials" class="testimonials-section">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">What Our Students Say</h2>
      <p class="section-subheading">Hear firsthand from those who achieved their goals with our AI study partner.</p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="testimonial-card">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "Part 1 AI completely changed my study strategy. The adaptive questions helped me target my weak spots,
            and the mock exams felt exactly like the real thing. Highly recommend!"
          </p>
          <div class="author-info">
            <img
              src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-1.jpg"
              alt="Student 1">
            <div>
              <h4>Dr. Hamza</h4>
              <span>House Officer, Pakistan Institute of Medical Sciences</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="testimonial-card">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "The analytics dashboard is a game-changer! I finally understood where I was spending too much time
            and which topics needed more attention. My scores improved dramatically."
          </p>
          <div class="author-info">
            <img
              src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-2.jpg"
              alt="Student 2">
            <div>
              <h4>Dr. Umar</h4>
              <span>House Officer, Pakistan Institute of Medical Sciences</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="testimonial-card">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "As a busy professional, the dynamic study planner was invaluable. It kept me organized and ensured
            I made progress even with limited time. Couldn't have passed without it."
          </p>
          <div class="author-info">
            <img
              src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-3.jpg"
              alt="Student 3">
            <div>
              <h4>Dr. Sumaira</h4>
              <span>Medical Officer, Tehsil Headquarter Hospital Gujjar Khan</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<section id="cta-bottom" class="cta-section-new">
  <div class="container" data-aos="zoom-in">
    <h2>Don't Just Study, Master Your Future</h2>
    <p>
      Experience the next generation of exam preparation. Our AI study partner is here to provide clarity, confidence,
      and the competitive edge you need to excel.
    </p>
    <a href="register" class="btn-cta">Enroll and Start Learning</a>
  </div>
</section>

<?php $this->registerJS("document.addEventListener('DOMContentLoaded', function () {
    GLightbox({
      selector: '.glightbox',
      touchNavigation: true,
      loop: true,
      zoomable: true,
      openEffect: 'zoom',
      closeEffect: 'fade'
    });
  });", View::POS_END) ?>
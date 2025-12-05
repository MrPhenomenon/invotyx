<?php

use yii\web\View;

/** @var yii\web\View $this */
\yii\bootstrap5\BootstrapPluginAsset::register($this);
$this->title = 'Part1PK | FCPS Part 1 & MD/MS Preparation App | 2026 Syllabus';
$this->params['meta_description'] = 'Pass FCPS Part 1 and MD/MS exams with Pakistan’s #1 AI Practice App. 10,000+ verified MCQs, error-free past papers, and performance analytics. Affordable & smart.';
?>

<section id="hero" class="hero-section-new p-0">
  <div class="container" style="max-width: 1500px">
    <div class="row align-items-center gy-5">
      <div class="col-lg-6 hero-content" data-aos="fade-right" data-aos-delay="100">
        <h1>The Smartest Way to Pass FCPS, MD/MS Part 1 Exams</h1>
        <p>
          Stop memorizing static books with wrong keys.
          Master your FCPS Part 1 & MD/MS with Pakistan’s first AI-Powered Practice Platform.
        </p>
        <div class="my-3 d-flex gap-3 flex-wrap aos-init aos-animate" data-aos="fade-up" data-aos-delay="250">
          <span class="badge rounded-pill bg-success"><i class="bi bi-check2-circle"></i> Covers FCPS Part 1 & MD/MS
          </span>
          <span class="badge rounded-pill bg-success"><i class="bi bi-check2-circle"></i> 10,000+ Verified MCQs (No
            Wrong Keys)</span>
          <span class="badge rounded-pill bg-success"><i class="bi bi-check2-circle"></i> Smart Analytics for
            Personalized Prep</span>
        </div>
        <a href="register" class="btn-hero mt-2" data-aos="fade-up" data-aos-delay="300">Start Free Mock Exam</a>
      </div>
      <div class="col-lg-6 text-center hero-image-blob-container" data-aos="fade-left" data-aos-delay="200">
        <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/hero-bg(2).png" class="img-fluid"
          alt="AI Study Partner Dashboard">
      </div>
    </div>
  </div>
</section>

<!-- SECTION 2: THE PROBLEM VS. SOLUTION -->
<section id="usp" class="usp-section py-5">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">Why Top Candidates Are Ditching Static Books</h2>
      <p class="section-subheading">For years, doctors have wasted money on expensive, static books that are full of
        printing errors and "wrong keys."</p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="usp-item h-100">
          <div class="usp-icon"><i class="bi bi-book-half"></i></div>
          <div class="usp-content">
            <h3>The "Book" Problem</h3>
            <p>Traditional past paper books are outdated the moment they are printed. They
              lack explanations and promote passive reading.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-8 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="usp-item h-100">
          <div class="usp-icon"><i class="bi bi-lightning-charge"></i></div>
          <div class="usp-content">
            <h3>The Part1PK Advantage</h3>
            <p>We digitize the high-yield concepts from these sources but fix the errors.</p>
            <p>
            <ul class="ps-3">
              <li><strong>Practice Mode Only: </strong>No boring theory. Just pure, active recall.</li>
              <li><strong>Cost-Effective: </strong>Get access to 100+ books' worth of questions for the price of a
                single lunch.</li>
              <li><strong>Always Updated: </strong>CPSP changed a guideline yesterday? Our App updates today.</li>
            </ul>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- SECTION 4: SMART FEATURES -->
<section id="features" class="features-grid-section py-5">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">More Than Just MCQs – A Personal Coach</h2>
      <p class="section-subheading">Our features are designed to detect your weaknesses and force you to master them.
      </p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="feature-card h-100">
          <i class="bi bi-graph-up-arrow icon"></i>
          <h3>Advanced Analytics</h3>
          <p>Don't just guess your progress. "You are 90% ready in Anatomy but only 40% in Biostats." See your
            percentile rank against other Pakistani doctors.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="feature-card h-100">
          <i class="bi bi-crosshair icon"></i>
          <h3>Weakness Targeting</h3>
          <p>The App detects questions you get wrong and forces you to repeat them until mastered. Create custom mocks
            (e.g., "50 Questions on Upper Limb only").</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="feature-card h-100">
          <i class="bi bi-piggy-bank icon"></i>
          <h3>Cost-Effective</h3>
          <p>Why buy 10 different books costing Rs. 20,000+? Part1PK gives you access to the entire medical database for
            a fraction of the cost.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 6: APP SCREENSHOTS -->
<section id="app-screenshots" class="app-screenshots-section bg-light">
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
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-1.jpg" class="glightbox"
            data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-1.jpg" class="img-fluid rounded shadow"
              alt="App Dashboard Overview">
          </a>
          <h5 class="mt-3">Dashboard Overview</h5>
          <p>Your personalized hub to track progress and access study modules.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="screenshot-card text-center h-100">
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-2.jpg" class="glightbox"
            data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-2.jpg" class="img-fluid rounded shadow"
              alt="Adaptive MCQ Practice Interface">
          </a>
          <h5 class="mt-3">Adaptive Questions</h5>
          <p>Engage with AI-powered questions tailored to your learning needs.</p>
        </div>
      </div>

      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="screenshot-card text-center h-100">
          <a href="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-3.jpg" class="glightbox"
            data-gallery="app-screens">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/app-screenshot-3.jpg" class="img-fluid rounded shadow"
              alt="Performance Analytics Charts">
          </a>
          <h5 class="mt-3">Performance Analytics</h5>
          <p>Visualize your strengths and weaknesses with detailed reports.</p>
        </div>
      </div>
    </div>
  </div>
</section>



<!-- SECTION 8: TESTIMONIALS -->
<section id="testimonials" class="testimonials-section bg-light">
  <div class="container">
    <div class="text-center" data-aos="fade-up">
      <h2 class="section-heading-new">What Our Students Say</h2>
      <p class="section-subheading">Hear firsthand from those who achieved their goals with our AI study partner.</p>
    </div>

    <div class="row gy-4 mt-3">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="testimonial-card h-100">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "Part 1 AI completely changed my study strategy. I was initially unsure about what is FCPS Part 1 exactly,
            but this platform clarified everything. The mock exams replicate how many MCQs in FCPS Part 1 actually
            appear on test day, and the FCPS Part 1 MCQs distribution covers every topic perfectly. Highly recommend!"
          </p>
          <div class="author-info">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-1.jpg" alt="Student 1">
            <div>
              <h4>Dr. Hamza</h4>
              <span>House Officer, Pakistan Institute of Medical Sciences</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="testimonial-card h-100">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "The analytics dashboard is a game-changer! I finally understood where I was spending too much time,
            specifically on tricky Anatomy MCQs for FCPS Part 1. Being able to review detailed FCPS Part 1 MCQs with
            answers immediately helped me see which topics needed more attention. My scores improved dramatically."
          </p>
          <div class="author-info">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-2.jpg" alt="Student 2">
            <div>
              <h4>Dr. Umar</h4>
              <span>House Officer, Pakistan Institute of Medical Sciences</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
        <div class="testimonial-card h-100">
          <i class="bi bi-quote quote-icon"></i>
          <p>
            "As a busy professional, the dynamic study planner was invaluable. Being able to switch between FCPS Part 1
            online MCQs and the downloadable FCPS Part 1 MCQs PDF meant I could study anywhere. It kept me organized and
            ensured I made progress even with limited time."
          </p>
          <div class="author-info">
            <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/avatar-3.jpg" alt="Student 3">
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

<!-- SECTION 3: EXAM INTELLIGENCE (SCHEDULES) -->
<section id="exam-schedules" class="py-5">
  <div class="container">
    <div class="text-center mb-2" data-aos="fade-up">
      <h2 class="section-heading-new">Exam Schedules & Logistics (2026)</h2>
      <p class="section-subheading">Success requires knowing your timeline. We track both CPSP and University schedules.
      </p>

      <button id="btn-schedule" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm" type="button"
        data-bs-toggle="collapse" data-bs-target="#scheduleCollapse" aria-expanded="false"
        aria-controls="scheduleCollapse">
        <span>Show Exam Calendar</span> <i class="bi bi-chevron-down ms-2"></i>
      </button>
    </div>

    <div class="collapse" id="scheduleCollapse">
      <div class="collapse-inner mt-4">
        <div class="row">
          <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-header bg-primary">
                <h5 class="text-white mb-0"><i class="bi bi-calendar-check me-2"></i> FCPS Part 1 (CPSP) – 2026</h5>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Paper 1:</strong> January 5, 2026</span>
                    <span class="badge bg-primary rounded-pill">Confirmed</span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Paper 2:</strong> April 6, 2026</span>
                    <span class="badge bg-primary rounded-pill">Confirmed</span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Paper 3:</strong> July 6, 2026</span>
                    <span class="badge bg-primary rounded-pill">Confirmed</span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><strong>Paper 4:</strong> October 5, 2026</span>
                    <span class="badge bg-primary rounded-pill">Confirmed</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
              <div class="card-header bg-primary">
                <h5 class="text-white mb-0"><i class="bi bi-mortarboard me-2"></i> MD/MS Part 1 / Entry Tests</h5>
              </div>
              <div class="card-body">
                <p>MD/MS exams are university-specific (UHS, SZABMU, DUHS, KMU), but most follow a similar pattern.</p>
                <ul class="list-unstyled">
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Common
                      Exams:</strong>
                    JCAT (Punjab), SZABMU Entry Test (Islamabad), DUHS GAT (Karachi).</li>
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Typical
                      Schedule:</strong> Usually held twice a year (Spring & Autumn).</li>
                  <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>Syllabus
                      Overlap:</strong> 80% of the MD/MS syllabus is identical to FCPS Part 1.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 5: EXAM LOGISTICS & SYLLABUS -->
<section id="syllabus-details" class="py-5">
  <div class="container">
    <div class="text-center mb-2" data-aos="fade-up">
      <h2 class="section-heading-new">CPSP FCPS Part 1 Exam Guide (2026)</h2>
      <p class="section-subheading">Success isn't just about knowledge; it's about understanding the battlefield. <br>
        Below are the confirmed details for the <strong>2026 sessions</strong>.</p>

      <button id="btn-syllabus" class="btn btn-primary rounded-pill px-4 mt-3 shadow-sm" type="button"
        data-bs-toggle="collapse" data-bs-target="#syllabusCollapse" aria-expanded="false"
        aria-controls="syllabusCollapse">
        <span>View Full Syllabus & Rules</span> <i class="bi bi-chevron-down ms-2"></i>
      </button>
    </div>

    <div class="collapse" id="syllabusCollapse">

      <div class="collapse-inner mt-4">
        <div class="row">
          <div class="col-lg-12">
            <h4 class="mb-3">Key Exam Logistics</h4>
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class="table-dark">
                  <tr>
                    <th>Feature</th>
                    <th>Official CPSP Rule</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Exam Format</td>
                    <td>Computer-Based Testing (CBT)</td>
                  </tr>
                  <tr>
                    <td>Total Papers</td>
                    <td>2 Papers (Paper I & Paper II)</td>
                  </tr>
                  <tr>
                    <td>Questions</td>
                    <td>200 MCQs Total (100 per paper)</td>
                  </tr>
                  <tr>
                    <td>Duration</td>
                    <td>2 Hours per paper (Total 4 Hours)</td>
                  </tr>
                  <tr>
                    <td>Passing Criteria</td>
                    <td>75% Aggregate (e.g., 150/200 correct)</td>
                  </tr>
                  <tr>
                    <td>Negative Marking</td>
                    <td>NO Negative Marking <span class="text-success">(Always attempt 100% of questions)</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="col-lg-12">
            <h4>Paper Breakdown & Syllabus</h4>
            <p>The exam is split into two distinct sessions held on the same day.</p>
            <div class="row gy-4">
              <div class="col-lg-6">
                <div class="card mb-3 border-0 shadow h-100">
                  <div class="card-body border-start border-4 border-primary bg-white">
                    <h5 class="card-title text-primary">Paper I: Basic Medical Sciences</h5>
                    <div class="d-flex align-items-start mb-2">
                      <div><strong>Focus:</strong> Anatomy, Physiology, Pathology.</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card mb-4 border-0 shadow h-100">
                  <div class="card-body border-start border-4 border-primary bg-white">
                    <h5 class="card-title text-primary">Paper II: Specialty Oriented</h5>
                    <ul class="list-unstyled mb-0 small">
                      <li class="mb-2"><strong>Medicine & Allied:</strong> Pharmacology, Pathology, Biostatistics.</li>
                      <li class="mb-2"><strong>Surgery & Allied:</strong> General Surgery, Trauma, Anatomy.</li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <div class="card mb-4 border-0 shadow h-100">
                  <div class="card-body border-start border-4 border-primary bg-white">
                    <h5 class="card-title text-primary">Also Covering Essential Minor Subjects</h5>
                    <p>Our App ensures you are covered with dedicated, high-yield question banks for:</p>
                    <ul class="list-unstyled small mb-0">
                      <li class="mb-2"><strong><i class="bi bi-check-circle-fill text-success me-1"></i> Biochemistry &
                          Genetics</strong></li>
                      <li class="mb-2"><strong><i class="bi bi-check-circle-fill text-success me-1"></i> Microbiology &
                          Immunology</strong></li>
                      <li class="mb-2"><strong><i class="bi bi-check-circle-fill text-success me-1"></i> Biostatistics &
                          Epidemiology</strong></li>
                      <li class="mb-2"><strong><i class="bi bi-check-circle-fill text-success me-1"></i>
                          Pharmacology</strong></li>
                      <li><strong><i class="bi bi-check-circle-fill text-success me-1"></i> Embryology &
                          Histology</strong></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 9: FAQ -->
<section id="faq" class="py-5">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <h2 class="section-heading-new">Frequently Asked Questions</h2>
      <p class="section-subheading">Common queries about FCPS Part 1 & MD/MS preparation.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="accordion accordion-flush" id="faqAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header" id="faq-heading-1">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq-collapse-1" aria-expanded="false" aria-controls="faq-collapse-1">
                Is Part1PK useful for MD/MS exams like JCAT?
              </button>
            </h2>
            <div id="faq-collapse-1" class="accordion-collapse collapse" aria-labelledby="faq-heading-1"
              data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                <strong>Absolutely.</strong> The Basic Sciences component (Anatomy, Physiology, Pathology) for MD/MS
                exams is nearly identical to FCPS Part 1. Our App covers the syllabus for JCAT (Punjab), SZABMU, and
                other university entry tests.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="faq-heading-2">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq-collapse-2" aria-expanded="false" aria-controls="faq-collapse-2">
                Are the exam dates for 2026 confirmed?
              </button>
            </h2>
            <div id="faq-collapse-2" class="accordion-collapse collapse" aria-labelledby="faq-heading-2"
              data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Yes, the FCPS Part 1 dates (Jan, Apr, July, Oct) are provisional dates released by CPSP. MD/MS dates
                vary by university, but our App sends you notifications when schedules are announced.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="faq-heading-3">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq-collapse-3" aria-expanded="false" aria-controls="faq-collapse-3">
                How is this better than buying past paper books?
              </button>
            </h2>
            <div id="faq-collapse-3" class="accordion-collapse collapse" aria-labelledby="faq-heading-3"
              data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                Books are static, heavy, and often contain unverified "wrong keys." Part1PK is cheaper, error-free, and
                uses AI to track your weak areas—something a book can never do.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="faq-heading-4">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#faq-collapse-4" aria-expanded="false" aria-controls="faq-collapse-4">
                Do you have a theory section?
              </button>
            </h2>
            <div id="faq-collapse-4" class="accordion-collapse collapse" aria-labelledby="faq-heading-4"
              data-bs-parent="#faqAccordion">
              <div class="accordion-body">
                No, we focus 100% on <strong>Active Practice</strong>. We believe the best way to pass is by solving
                questions, not passively reading notes. We offer Mock Exams and Topic-wise practice to boost recall.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SECTION 10: CTA -->
<section id="cta-bottom" class="cta-section-new">
  <div class="container" data-aos="zoom-in">
    <h2>Don't Just Study, Master Your Future</h2>
    <p>
      Experience a smarter way to practice FCPS Part 1 MCQs with answers. We help you stay ahead of FCPS Part 1 dates
      and provide the clarity, confidence, and competitive edge you need to excel.
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
  });
  function setupCollapseButton(buttonId, collapseId, showText, hideText) {
    const button = document.getElementById(buttonId);
    const collapseElement = document.getElementById(collapseId);
    
    if (!button || !collapseElement) return;

    const span = button.querySelector('span');
    const icon = button.querySelector('i');

    collapseElement.addEventListener('show.bs.collapse', function () {
      span.textContent = hideText;
      icon.classList.remove('bi-chevron-down');
      icon.classList.add('bi-chevron-up');
    });

    collapseElement.addEventListener('hide.bs.collapse', function () {
      span.textContent = showText;
      icon.classList.remove('bi-chevron-up');
      icon.classList.add('bi-chevron-down');
    });
  }

  setupCollapseButton('btn-schedule', 'scheduleCollapse', 'Show Exam Calendar', 'Hide Exam Calendar');
  setupCollapseButton('btn-syllabus', 'syllabusCollapse', 'View Full Syllabus & Rules', 'Hide Syllabus & Rules');
  ", View::POS_END) ?>
<style>
    :root {
        --primary-blue: #0e273c;
        --dark-blue: #0e273c;
        --light-grey: #f8f9fa;
        --dark-grey: #6c757d;
        --text-color: #343a40;
        --success-green: #28a745;
        --warning-red: #dc3545;
    }

    /* --- Welcome Header --- */
    .welcome-header {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding: 15px;
        background-color: #ffffff;
        box-shadow: var(--card-shadow);
    }

    .card {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
    }

    .welcome-header .profile-pic {
        width: 50px;
        height: 50px;
        background-color: var(--medium-grey);
        border-radius: 50%;
        margin-right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--dark-grey);
    }

    .welcome-header h1 {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--dark-blue);
        margin: 0;
    }

    .welcome-header h1 span {
        font-weight: 400;
    }

    /* --- Dashboard Grid --- */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* --- Specific Card Layouts --- */
    .full-width {
        grid-column: 1 / -1;
        /* Makes card span full width of the grid */
    }

    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--dark-blue);
        line-height: 1.1;
    }

    .stat-card .stat-label {
        font-size: 0.9rem;
        color: var(--dark-grey);
        margin-top: 5px;
    }

    .subscription-info p,
    .countdown-info p {
        margin: 4px 0;
        font-size: 0.95rem;
    }

    .subscription-info p strong,
    .countdown-info p strong {
        color: #000;
    }

    /* Progress Summary Card */
    .progress-summary-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 15px;
        text-align: center;
    }

    .progress-bar-container {
        width: 100%;
        background-color: var(--medium-grey);
        border-radius: 50px;
        height: 12px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .progress-bar-fill {
        height: 100%;
        background-color: var(--primary-blue);
        border-radius: 50px;
    }

    .item-list {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;

    }

    .card-header i {
        font-size: 18px;
        margin-right: 10px;
    }

    .item-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .item-list li:last-child {
        border-bottom: none;
    }

    .item-list .item-topic {
        font-weight: 500;
    }

    .item-list .item-meta {
        font-size: 0.85rem;
        color: var(--dark-grey);
    }

    .item-list .score-badge {
        font-weight: 600;
        padding: 3px 8px;
        font-size: 0.9rem;
    }

    .score-badge.good {
        background-color: #e9f7ef;
        color: var(--success-green);
    }

    .score-badge.average {
        background-color: #fff8e1;
        color: #f59e0b;
    }

    /* Study Plan Card */
    .study-plan-list .item-topic i {
        color: var(--primary-blue);
        margin-right: 8px;
    }

    .study-plan-list .item-meta {
        color: var(--primary-blue);
        font-weight: 500;
    }

    /* Bookmarks Card */
    .bookmarks-list .continue-link {
        text-decoration: none;
        color: var(--primary-blue);
        font-weight: 500;
        font-size: 0.9rem;
    }

    .bookmarks-list .continue-link:hover {
        text-decoration: underline;
    }

    /* Trust Breach Card */
    .warning-card .card-header i {
        color: var(--warning-red);
    }

    .warning-card {
        border: 1px solid var(--warning-red);
        background-color: #fff5f5;
    }

    .warning-card p {
        font-weight: 500;
        color: #c53030;
    }

    /* Responsive Grid */
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .welcome-header h1 {
            font-size: 1.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
        }

        body {
            padding: 10px;
        }

        .dashboard-grid {
            gap: 15px;
        }
    }
</style>
<div class="container py-4">

    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center bg-white p-3">
                    <div class="me-3 rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                        style="width:50px;height:50px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <h1 class="mb-0 fs-4 fw-semibold text-primary">Hello, <span class="fw-normal">Maria!</span></h1>
                </div>
            </div>

        </div>
    </div>

    <div class="row g-4">

        <!-- Active Subscription Card -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-arrow-repeat"></i> Active Subscription
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Plan:</strong> Gold Plan</p>
                    <p><strong>Ends:</strong> Aug 30, 2025</p>
                </div>
            </div>
        </div>

        <!-- Exam Date Countdown Card -->
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event-fill"></i> Exam Date Countdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="fs-2 fw-bold text-primary">73</div>
                    <p class="mb-0 text-muted">Days Left Until Sep 19, 2025</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 bg-primary text-white shadow-sm">
                <div class="card-header bg-transparent border-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clipboard-list-fill me-2"></i> Upcoming Mock Exam
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center bg-transparent text-white px-0">
                        <div>
                            <div class="fw-bold">Full-Length Exam</div>
                            <small>July 01, 2024</small>
                        </div>
                        <span class="badge bg-light text-primary rounded-pill">38 days</span>
                    </div>
                    <div class="d-grid mt-3">
                        <a href="#" class="btn btn-light fw-bold text-primary">Attempt Now</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Study Progress Summary Card -->
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart-fill"></i> Study Progress Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar bg-primary" style="width: 74%;"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <div class="fw-bold">432</div>
                            <div class="text-muted small">MCQs Attempted</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-success">74%</div>
                            <div class="text-muted small">Correct</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold">21</div>
                            <div class="text-muted small">Bookmarked</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recently Practiced MCQs Card -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-stopwatch-fill"></i> Recently Practiced
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Cardiology</div>
                                <div class="text-muted small">Oct 26, 2023</div>
                            </div>
                            <span class="badge bg-success rounded-pill">8/10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Gastrointestinal</div>
                                <div class="text-muted small">Oct 25, 2023</div>
                            </div>
                            <span class="badge bg-warning text-dark rounded-pill">6/10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Neurology</div>
                                <div class="text-muted small">Oct 24, 2023</div>
                            </div>
                            <span class="badge bg-success rounded-pill">9/10</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Study Plan Today Card -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-book-fill"></i> Today's Study Plan
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-book-open text-primary me-2"></i>Renal Physiology</span>
                            <span class="text-primary fw-semibold">15 MCQs</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-vial text-primary me-2"></i>Mock Exam 2</span>
                            <span class="text-primary fw-semibold">Scheduled</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-star text-primary me-2"></i>Review Bookmarks</span>
                            <span class="text-primary fw-semibold">5 MCQs</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bookmarked MCQs Card -->
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bookmark-check-fill"></i> Continue Bookmarked Questions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Question on Glycolysis Pathway</span>
                            <a href="#" class="btn btn-link p-0">Continue <i class="fas fa-arrow-right"></i></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Case study: Myocardial Infarction</span>
                            <a href="#" class="btn btn-link p-0">Continue <i class="fas fa-arrow-right"></i></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Identifying EKG abnormalities</span>
                            <a href="#" class="btn btn-link p-0">Continue <i class="fas fa-arrow-right"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Trust Breach Notices Card -->
        <div class="col-12">
            <div class="card border-danger bg-light">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i> Trust Breach Notices
                    </h5>
                </div>
                <div class="card-body">
                    <p class="fw-semibold text-danger mb-0">
                        You have 2 reported violations from your last mock exam. Please review our academic integrity
                        policy.
                    </p>
                </div>
            </div>
        </div>

    </div> <!-- end .row -->

</div> <!-- end .container -->
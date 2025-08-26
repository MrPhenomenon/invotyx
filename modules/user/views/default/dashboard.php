<div class="dashboard-container">

    <header class="welcome-header">
        <div class="profile-pic"><i class="fas fa-user"></i></div>
        <h1>Hello, <span>Maria!</span></h1>
    </header>

    <div class="dashboard-grid">

        <!-- Active Subscription Card -->
        <div class="card subscription-info">
            <div class="card-header">
                <i class="fas fa-sync-alt"></i>
                <h3>Active Subscription</h3>
            </div>
            <div class="card-content">
                <p><strong>Plan:</strong> Gold Plan</p>
                <p><strong>Ends:</strong> Aug 30, 2025</p>
            </div>
        </div>

        <!-- Exam Date Countdown Card -->
        <div class="card stat-card countdown-info">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i>
                <h3>Exam Date Countdown</h3>
            </div>
            <div class="card-content">
                <div class="stat-value">73</div>
                <p class="stat-label">Days Left Until Sep 19, 2025</p>
            </div>
        </div>

        <!-- Study Progress Summary Card -->
        <div class="card full-width">
            <div class="card-header">
                <i class="fas fa-chart-line"></i>
                <h3>Study Progress Summary</h3>
            </div>
            <div class="card-content">
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: 74%;"></div>
                </div>
                <div class="progress-summary-stats">
                    <div class="stat-item">
                        <div class="stat-value small">432</div>
                        <div class="stat-label">MCQs Attempted</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value small" style="color: var(--success-green);">74%</div>
                        <div class="stat-label">Correct</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value small">21</div>
                        <div class="stat-label">Bookmarked</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recently Practiced MCQs Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-stopwatch"></i>
                <h3>Recently Practiced</h3>
            </div>
            <ul class="item-list">
                <li>
                    <div>
                        <div class="item-topic">Cardiology</div>
                        <div class="item-meta">Oct 26, 2023</div>
                    </div>
                    <span class="score-badge good">8/10</span>
                </li>
                <li>
                    <div>
                        <div class="item-topic">Gastrointestinal</div>
                        <div class="item-meta">Oct 25, 2023</div>
                    </div>
                    <span class="score-badge average">6/10</span>
                </li>
                <li>
                    <div>
                        <div class="item-topic">Neurology</div>
                        <div class="item-meta">Oct 24, 2023</div>
                    </div>
                    <span class="score-badge good">9/10</span>
                </li>
            </ul>
        </div>

        <!-- Study Plan Today Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-brain"></i>
                <h3>Today's Study Plan</h3>
            </div>
            <ul class="item-list study-plan-list">
                <li>
                    <div class="item-topic"><i class="fas fa-book-open"></i> Renal Physiology</div>
                    <span class="item-meta">15 MCQs</span>
                </li>
                <li>
                    <div class="item-topic"><i class="fas fa-vial"></i> Mock Exam 2</div>
                    <span class="item-meta">Scheduled</span>
                </li>
                <li>
                    <div class="item-topic"><i class="fas fa-star"></i> Review Bookmarks</div>
                    <span class="item-meta">5 MCQs</span>
                </li>
            </ul>
        </div>

        <!-- Bookmarked MCQs Card -->
        <div class="card full-width">
            <div class="card-header">
                <i class="fas fa-bookmark"></i>
                <h3>Continue Bookmarked Questions</h3>
            </div>
            <ul class="item-list bookmarks-list">
                <li>
                    <div class="item-topic">Question on Glycolysis Pathway</div>
                    <a href="#" class="continue-link">Continue <i class="fas fa-arrow-right"></i></a>
                </li>
                <li>
                    <div class="item-topic">Case study: Myocardial Infarction</div>
                    <a href="#" class="continue-link">Continue <i class="fas fa-arrow-right"></i></a>
                </li>
                <li>
                    <div class="item-topic">Identifying EKG abnormalities</div>
                    <a href="#" class="continue-link">Continue <i class="fas fa-arrow-right"></i></a>
                </li>
            </ul>
        </div>

        <!-- Trust Breach Notices Card -->
        <div class="card warning-card full-width">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Trust Breach Notices</h3>
            </div>
            <div class="card-content">
                <p>You have 2 reported violations from your last mock exam. Please review our academic integrity policy.
                </p>
            </div>
        </div>

    </div> <!-- end .dashboard-grid -->

</div> <!-- end .dashboard-container -->
<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = "About Part1PK | Pakistan's #1 FCPS Part 1 AI Platform";
$this->params['meta_description'] = 'Learn how Part1PK is revolutionizing CPSP exam prep. We replace static PDFs with verified AI-powered MCQs to help you pass FCPS Part 1 in the first attempt.';

$this->registerCss("
    /* --- THEME COLORS & VARIABLES --- */
    .theme-bg-blue {
        background-color: var(--blue-back, #0e273c);
        color: var(--contrast-color, #ffffff);
    }
    .text-accent {
        color: var(--accent-color, #0e273c) !important;
    }
    .text-highlight {
        color: var(--nav-hover-color, #87cefa) !important;
    }
    
    /* --- TYPOGRAPHY --- */
    .section-title {
        position: relative;
        padding-bottom: 15px;
        font-weight: 700;
        color: var(--accent-color, #0e273c);
    }
    .section-title::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--nav-hover-color, #87cefa);
        margin: 15px auto;
        border-radius: 2px;
    }
    .text-justify {
        text-align: justify;
        color: var(--default-color, #444);
    }

    /* --- HERO SECTION --- */
    .hero-wrap {
        background-color: var(--blue-back, #0e273c); /* Fallback/Solid color */
        color: var(--contrast-color, #fff);
        padding: 120px 0;
        margin-bottom: 20px;
    }
    
    /* --- CARDS (Why Doctors Trust Us) --- */
    .feature-card {
        border: 1px solid rgba(0,0,0,0.05);
        background: var(--surface-color, #fff);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(14, 39, 60, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        text-align: center;
        padding: 30px 20px;
    }
    .feature-card:hover {
        transform: translateY(-10px);
        border-bottom: 5px solid var(--accent-color, #0e273c);
    }
    .feature-icon {
        font-size: 2.5rem;
        color: var(--accent-color, #0e273c);
        margin-bottom: 25px;
        background: #f0f8ff;
        width: 90px;
        height: 90px;
        line-height: 90px;
        border-radius: 50%;
        display: inline-block;
        transition: 0.3s;
    }
    .feature-card:hover .feature-icon {
        background: var(--accent-color, #0e273c);
        color: var(--nav-hover-color, #87cefa);
    }

    /* --- STORY SECTION LAYOUT --- */
    .story-img {
        border-radius: 15px;
        box-shadow: 10px 10px 0px #0e273c;
        width: 100%;
        height: auto;
        object-fit: cover;
    }
    
    /* New Feature List Layout (Flexbox) */
    .feature-list-item {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
    }
    .icon-box-small {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: rgba(135, 206, 250, 0.1);
        color: var(--nav-hover-color, #87cefa);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }
    .feature-list-item:hover .icon-box-small {
        background-color: var(--nav-hover-color, #87cefa);
        color: #fff;
        transform: scale(1.1);
    }

    /* --- VALUE BOXES --- */
    .value-box {
        background: rgba(255, 255, 255, 0.05);
        padding: 30px;
        border-radius: 8px;
        height: 100%;
        border-left: 5px solid var(--nav-hover-color, #87cefa);
    }
    .value-box h4 {
        font-weight: 700;
        margin-bottom: 15px;
        color: #fff;
    }
");
?>

<div class="site-about">

    <!-- HERO SECTION -->
    <div class="hero-wrap text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h1 class="font-weight-bold mb-3 text-white">Revolutionizing Medical Education in Pakistan</h1>
                    <p class="lead mb-5" style="color: #e0e0e0; font-size: 1.3rem;">
                        We are on a mission to help every Pakistani doctor clear their FCPS Part 1 exam in the first attempt—without the confusion of outdated books and "wrong keys."
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- MISSION SECTION -->
    <section class="section-padding container my-5">
        <div class="text-center">
            <h6 class="text-uppercase font-weight-bold letter-spacing-2">OUR MISSION</h6>
            <h2 class="section-title">Empowering the Next Generation of Specialists</h2>
        </div>
        <div class="row justify-content-center mt-4">
            <div class="col-lg-9 text-center">
                <p class="lead text-muted mb-4 font-italic">
                    "At Part1PK, we believe that becoming a specialist should test your medical knowledge, not your ability to memorize incorrect answer keys."
                </p>
                <p class="text-justify" style="font-size: 1.1rem;">
                    The journey to a CPSP Fellowship is grueling. For decades, candidates have relied on static FCPS Part 1 MCQs PDF files circulated on WhatsApp, often filled with unverified answers and zero explanations. 
                    <strong class="text-accent">We built Part1PK to change that.</strong>
                </p>
                <p class="text-justify font-weight-bold" style="font-size: 1.1rem;">
                    Our Mission is simple: To replace rote learning with AI-driven intelligence, providing an adaptive learning platform that evolves with the CPSP 2026 Syllabus.
                </p>
            </div>
        </div>
    </section>

    <!-- STORY SECTION -->
    <section class="section-padding py-5" style="background-color: #f9f9f9;">
        <div class="container">
            <div class="row align-items-center">
                <!-- Image -->
                <div class="col-md-6 mb-5 mb-md-0 order-md-2">
                    <img src="<?= Yii::getAlias('@web') ?>/siteassets/img/doctors-collab.jpg" alt="Our Story" class="story-img">
                </div>

                <!-- Text -->
                <div class="col-md-6 order-md-1 pr-md-5">
                    <h6 class="text-uppercase font-weight-bold">OUR STORY</h6>
                    <h2 class="mb-4 text-accent font-weight-bold">From "Static PDFs" to<br>"Active Intelligence"</h2>
                    <p class="text-muted">
                        The idea for Part1PK was born in a hospital cafeteria in Lahore. A group of House Officers was debating a controversial question from a "Golden File." Three different books gave three different answers. The confusion was wasting precious time.
                    </p>
                    <p class="text-muted">
                        We realized that Pakistani doctors deserve better.
                    </p>
                    <p class="text-muted">
                        We gathered a team of FCPS Supervisors, Residents, and Software Engineers to build a solution that does three things:
                    </p>

                    <!-- Feature List with New Layout -->
                    <div class="mt-4">
                        <!-- Item 1 -->
                        <div class="feature-list-item">
                            <div class="icon-box-small">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="text-accent font-weight-bold mb-1">Verifies every answer</h5>
                                <p class="mb-0 text-muted small">Against standard texts (Snell, Guyton, Robbins).</p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="feature-list-item">
                            <div class="icon-box-small">
                                <i class="fas fa-lightbulb fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="text-accent font-weight-bold mb-1">Explains the "Why"</h5>
                                <p class="mb-0 text-muted small">Detailed reasoning behind every option.</p>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="feature-list-item">
                            <div class="icon-box-small">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="text-accent font-weight-bold mb-1">Tracks Progress</h5>
                                <p class="mb-0 text-muted small">So you know exactly when you are ready for the exam.</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-accent font-italic mt-3">
                        Today, Part1PK is the first platform in Pakistan to use Artificial Intelligence to predict your weak subjects and force you to master them.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY DOCTORS TRUST US -->
    <section class="section-padding container my-5">
        <div class="text-center">
            <h6 class="text-uppercase font-weight-bold">WHY DOCTORS TRUST US</h6>
            <h2 class="section-title">What Sets Part1PK Apart?</h2>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                We are not just another question bank. We are your digital study partner.
            </p>
        </div>

        <div class="row mt-5">
            <!-- Feature 1 -->
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-accent">Verified by Experts, Not Interns</h4>
                        <p class="card-text text-muted">
                            Unlike other apps that copy-paste data, our content goes through a rigorous 3-Step Verification Process by FCPS-II trainees and consultants. If a guideline changes in the CPSP syllabus, we update our App within 48 hours.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-laptop-medical"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-accent">The "No Negative Marking" Strategy</h4>
                        <p class="card-text text-muted">
                            We train you specifically for the Pakistan CPSP format. Our mock exams simulate the real environment—200 MCQs, 4 Hours, and No Negative Marking logic—so you walk into the exam hall feeling at home.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-4 mb-4">
                <div class="card feature-card">
                    <div class="card-body">
                        <div class="feature-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h4 class="card-title font-weight-bold text-accent">Covering Every Niche</h4>
                        <p class="card-text text-muted">
                            We don't leave the "minor" specialists behind. Whether you are preparing for FCPS Part 1 Dentistry, Anesthesia, or Radiology, we have dedicated, high-yield modules just for you.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- VALUES SECTION -->
    <section class="theme-bg-blue section-padding py-5">
        <div class="container">
            <h6 class="text-uppercase text-center font-weight-bold">OUR VALUES</h6>
            <h2 class="mb-5 text-center font-weight-bold text-white">Core Values That Drive Us</h2>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="value-box">
                        <i class="fas fa-bullseye fa-2x mb-3 text-highlight"></i>
                        <h4>Accuracy First</h4>
                        <p class="text-white-50">
                            We would rather have fewer questions than incorrect ones. Quality over quantity.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="value-box">
                        <i class="fas fa-hand-holding-heart fa-2x mb-3 text-highlight"></i>
                        <h4>Accessibility</h4>
                        <p class="text-white-50">
                            Quality medical education shouldn't be expensive. We keep our platform affordable for every House Officer in Pakistan.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="value-box">
                        <i class="fas fa-rocket fa-2x mb-3 text-highlight"></i>
                        <h4>Innovation</h4>
                        <p class="text-white-50">
                            We constantly update our algorithms to match the evolving difficulty of the FCPS Part 1 exam.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
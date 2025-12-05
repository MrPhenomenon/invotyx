<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Part1PK Guides & Syllabus Updates';

$this->registerCss("
    .theme-bg-blue { background-color: var(--blue-back, #0e273c); }
    .text-accent { color: var(--accent-color, #0e273c); }
    .text-highlight { color: var(--accent-color, #0e273c); }
    
    .blog-card {
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        border-radius: 10px;
        overflow: hidden;
    }
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(14, 39, 60, 0.15);
    }
    .blog-img-wrap {
        height: 200px;
        overflow: hidden;
        background: #f0f0f0;
    }
    .blog-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .blog-category {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: var(--accent-color, #0e273c);
        text-transform: uppercase;
        margin-bottom: 10px;
        display: block;
    }
    .read-more-btn {
        color: var(--accent-color, #0e273c);
        font-weight: 700;
        text-decoration: none;
        border-bottom: 2px solid var(--accent-color, #0e273c);
        transition: 0.2s;
    }
    .read-more-btn:hover {
        text-decoration: none;
        padding: 2px 5px;
    }
");

$posts = [
    [
        'slug' => 'how-to-pass-fcps-part-1-first-attempt',
        'title' => 'How to Pass FCPS Part 1 in First Attempt (3-Month Plan) ',
        'excerpt' => 'Clear your CPSP exam in the first attempt with this proven 3-month study schedule. Includes verified book lists, daily routines, and 2026 exam tips.',
        'image' => 'https://placehold.co/600x400/0e273c/87cefa?text=FCPS+Study+Plan',
        'category' => 'Study Guide'
    ],
    [
        'slug' => 'jcat-syllabus-pattern-2026',
        'title' => 'JCAT Syllabus & Exam Pattern 2026: MD/MS & MDS (Official Breakdown)',
        'excerpt' => 'Preparing for JCAT 2026? Get the official syllabus for Paper 1 (Basic) and Paper 2 (Clinical). Check passing marks, negative marking rules, and recommended books.',
        'image' => 'https://placehold.co/600x400/0e273c/ffffff?text=JCAT+Syllabus',
        'category' => 'Exam Updates'
    ],
    [
        'slug' => 'cpsp-fcps-part-1-syllabus-2026-changes',
        'title' => 'CPSP FCPS Part 1 Syllabus 2026: Official Changes & Exam Schedule',
        'excerpt' => 'Is the FCPS Part 1 syllabus changing in 2026? We clarify the rumors vs. reality. Check the official CPSP Exam Schedule 2026 and confirmed dates here.',
        'image' => 'https://placehold.co/600x400/e9ecef/0e273c?text=CPSP+Updates',
        'category' => 'News'
    ]
];
?>

<div class="site-blog-index">
    
    <div class="container text-center py-5">
        <h1 class="display-4 font-weight-bold" style="color: var(--accent-color, #0e273c);">Latest Medical Guides</h1>
        <p class="lead text-muted">Verified syllabus updates, study plans, and exam strategies.</p>
    </div>

    <div class="container pb-5">
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-4 mb-4">
                    <div class="card blog-card h-100">
                        <div class="blog-img-wrap">
                            <img src="/siteassets/img/<?= $post['slug'] ?>.jpg" alt="<?= Html::encode($post['title']) ?>" class="blog-img">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <span class="blog-category"><?= $post['category'] ?></span>
                            <h5 class="card-title font-weight-bold" style="color: var(--accent-color, #0e273c);">
                                <?= Html::a($post['title'], ['guides/view', 'slug' => $post['slug']], ['class' => 'text-dark text-decoration-none']) ?>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= $post['excerpt'] ?>
                            </p>
                            <div class="mt-3">
                                <?= Html::a('Read Full Guide <i class="fas fa-arrow-right ml-1 small"></i>', ['guides/view', 'slug' => $post['slug']], ['class' => 'read-more-btn']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
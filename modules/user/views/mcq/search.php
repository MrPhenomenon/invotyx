<?php use yii\helpers\Url; use yii\helpers\Html; ?>

<form action="<?= Url::to(['mcqs/search']) ?>" method="get" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search MCQs..."
               value="<?= Html::encode($searchTerm) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>
</form>

<?php if (empty($mcqs)): ?>
    <div class="alert alert-warning">No results found.</div>
<?php endif; ?>

<?php foreach ($mcqs as $index => $mcq): ?>
    <div class="card mb-4 mcq-card">
        <div class="card-header">
            <h5 class="mb-0 lh-base">Question <?= $index + 1 ?>:
                <span class="fw-bold"><?= nl2br(Html::encode($mcq->question_text)) ?></span>
            </h5>
        </div>

        <div class="card-body">
            <?php if ($mcq->image_path): ?>
                <div class="mb-3 text-center">
                    <?= Html::img(Url::to('@web/path/to/your/images/' . $mcq->image_path), [
                        'class' => 'img-fluid rounded border',
                        'style' => 'max-height: 300px;',
                        'alt' => 'Question Image'
                    ]) ?>
                </div>
            <?php endif; ?>

            <!-- Options List -->
            <ol type="A" class="list-group">
                <?php
                $options = array_filter([
                    'A' => $mcq->option_a,
                    'B' => $mcq->option_b,
                    'C' => $mcq->option_c,
                    'D' => $mcq->option_d,
                    'E' => $mcq->option_e,
                ]);
                foreach ($options as $key => $text):
                    $class = 'list-group-item';
                    if ($key == $mcq->correct_option) {
                        $class .= ' list-group-item-success';
                    }
                    ?>
                    <li class="<?= $class ?>"><?= Html::encode($text) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>

        <?php if (!empty($mcq->explanation) || !empty($mcq->reference)): ?>
            <div class="card-footer bg-light-subtle">
                <?php if (!empty($mcq->explanation)): ?>
                    <div>
                        <h6><i class="bi bi-lightbulb-fill me-1"></i> Explanation</h6>
                        <blockquote class="blockquote text-md">
                            <?= nl2br(Html::encode($mcq->explanation)) ?>
                        </blockquote>
                    </div>
                <?php endif; ?>
                <?php if (!empty($mcq->explanation) && !empty($mcq->reference)): ?>
                    <hr>
                <?php endif; ?>
                <?php if (!empty($mcq->reference)): ?>
                    <div>
                        <h6 class="mt-2"><i class="bi bi-book-fill me-1"></i> Reference</h6>
                        <p class="small text-muted mb-0"><?= Html::encode($mcq->reference) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

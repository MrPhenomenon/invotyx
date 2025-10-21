<?php
use yii\helpers\Url;
use yii\helpers\Html;

$get = Yii::$app->request->get();
?>
<form id="search-form" method="GET" action="<?= Url::to(['mcq/manage']) ?>">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Question ID</label>
            <input type="text" name="question_id" class="form-control" placeholder="Enter ID"
                value="<?= Html::encode($get['question_id'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Organ System</label>
            <select name="organ_system" class="form-select">
                <option value="">All Organ Systems</option>
                <?php foreach ($organSystems as $organSystem): ?>
                    <option value="<?= $organSystem['id'] ?>" <?= (isset($get['organ_system']) && $get['organ_system'] == $organSystem['id']) ? 'selected' : '' ?>>
                        <?= Html::encode($organSystem['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Subject</label>
            <select name="subject" class="form-select">
                <option value="">All Subjects</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= $subject['id'] ?>" <?= (isset($get['subject']) && $get['subject'] == $subject['id']) ? 'selected' : '' ?>>
                        <?= Html::encode($subject['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Chapter</label>
            <select name="chapter" class="form-select">
                <option value="">All Chapters</option>
                <?php foreach ($chapters as $chapter): ?>
                    <option value="<?= $chapter['id'] ?>" <?= (isset($get['chapter']) && $get['chapter'] == $chapter['id']) ? 'selected' : '' ?>>
                        <?= Html::encode($chapter['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Topic</label>
            <select name="topic" class="form-select">
                <option value="">All Topics</option>
                <?php foreach ($topics as $topic): ?>
                    <option value="<?= $topic['id'] ?>" <?= (isset($get['topic']) && $get['topic'] == $topic['id']) ? 'selected' : '' ?>>
                        <?= Html::encode($topic['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Question Text</label>
            <input type="text" name="question_text" class="form-control" placeholder="Enter Question Text"
                value="<?= Html::encode($get['question_text'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                <a href="<?= Url::to(['mcq/manage']) ?>" class="btn btn-outline-secondary"><i
                        class="fas fa-undo"></i></a>
            </div>
        </div>
    </div>
</form>
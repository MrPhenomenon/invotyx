<?php
use app\models\Mcqs;
use yii\helpers\Html;
/** @var int|string $index */
?>

<div class="mb-3">
    <label class="form-label fw-bold">Paste Full MCQ</label>
    <textarea class="form-control full-mcq-input" rows="6" placeholder="
    Question ID: 
    Question:
    Organ System:
    Subject:
    Chapter:
    Topic
    A.
    B.
    C.
    D.
    E.
    Answer: C
    Explanation:
    Reference:
    Difficulty Level:
    Tags: "></textarea>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2 btn-parse-mcq">Parse</button>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">MCQ ID</label>
    <input name="mcqs[<?= $index ?>][question_id]" class="form-control" required></input>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Question</label>
    <textarea name="mcqs[<?= $index ?>][question_text]" class="form-control" rows="3" required
        placeholder=""></textarea>
</div>

<div class="row mb-3">
    <div class="col-3">
        <label class="form-label fw-bold">Organ System</label>
        <select name="mcqs[<?= $index ?>][organ_sys]" class="form-select">
            <?php foreach ($organsys as $organ): ?>
                <option value="<?= $organ['id'] ?>"><?= htmlspecialchars($organ['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-3">
        <label class="form-label fw-bold">Subject</label>

        <select name="mcqs[<?= $index ?>][subject]" class="form-select">
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-3">
        <label class="form-label fw-bold">Chapter</label>
        <select name="mcqs[<?= $index ?>][chapter]" class="form-select">
            <?php foreach ($chapters as $chapter): ?>
                <option value="<?= $chapter['id'] ?>"><?= htmlspecialchars($chapter['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-3">
        <label class="form-label fw-bold">Topic</label>
        <select name="mcqs[<?= $index ?>][topic]" class="form-select">
            <?php foreach ($topics as $topic): ?>
                <option value="<?= $topic['id'] ?>"><?= htmlspecialchars($topic['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Options (Select the correct option)</label>
    <div class="row g-2">
        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $option): ?>
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" value="<?= $option ?>"
                            name="mcqs[<?= $index ?>][correct_option]" required title="Select as correct answer">
                    </div>
                    <input type="text" class="form-control" name="mcqs[<?= $index ?>][option_<?= $option ?>]"
                        placeholder="Option <?= strtoupper($option) ?>" required>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Explanation</label>
    <textarea name="mcqs[<?= $index ?>][explanation]" class="form-control explanation-field" rows="3"></textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Reference</label>
    <textarea name="mcqs[<?= $index ?>][reference]" class="form-control reference-field" rows="2"></textarea>
</div>

<div class="row">
    <div class="col-6">
        <div class="mb-3">
            <label class="form-label fw-bold">Difficulty Level</label>
            <select name="mcqs[<?= $index ?>][difficulty_level]" class="form-select">
                <?php foreach (Mcqs::optsDifficultyLevel() as $value => $label): ?>
                    <option value="<?= $value ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-6">
        <div class="mb-3">
            <label class="form-label fw-bold">Tags (Optional. Saperated by comma)</label>
            <input name="mcqs[<?= $index ?>][tags]" class="form-control"
                placeholder="Tested Concept, Year, Specialty"></input>
        </div>
    </div>
</div>
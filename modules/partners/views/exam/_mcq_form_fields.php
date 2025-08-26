<?php
use yii\helpers\Html;
/** @var int|string $index */
?>

<div class="mb-3">
    <label class="form-label fw-bold">Paste Full MCQ</label>
    <textarea class="form-control full-mcq-input" rows="6"
        placeholder="Paste full MCQ with options, answer, explanation, reference..."></textarea>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2 btn-parse-mcq">Parse</button>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Question</label>
    <textarea name="PartnerMcqs[<?= $index ?>][question]" class="form-control" rows="3" required></textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Options (Select the correct option)</label>
    <div class="row g-2">
        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $option): ?>
            <div class="col-md-6">
                <div class="input-group">
                    <div class="input-group-text">
                        <input class="form-check-input mt-0" type="radio" value="<?= $option ?>"
                            name="PartnerMcqs[<?= $index ?>][correct_option]" required title="Select as correct answer">
                    </div>
                    <input type="text" class="form-control" name="PartnerMcqs[<?= $index ?>][option_<?= $option ?>]"
                        placeholder="Option <?= strtoupper($option) ?>" required>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Explanation</label>
    <textarea name="PartnerMcqs[<?= $index ?>][explanation]" class="form-control explanation-field" rows="3"></textarea>
</div>

<div class="mb-3">
    <label class="form-label fw-bold">Reference</label>
    <textarea name="PartnerMcqs[<?= $index ?>][reference]" class="form-control reference-field" rows="2"></textarea>
</div>

<div class="image-upload-wrapper">
    <label class="form-label">Image (Optional)</label>
    <input type="file" name="PartnerMcqs[<?= $index ?>][image_url]" class="form-control image-upload-input"
        accept="image/*">
    <div class="image-preview-container">
        <img src="#" alt="Image Preview" class="image-preview" />
        <button type="button" class="btn btn-sm btn-danger btn-remove-image" title="Remove Image"><i
                class="bi bi-x"></i></button>
    </div>
</div>
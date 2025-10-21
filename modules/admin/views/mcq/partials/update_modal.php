<?php
use yii\helpers\Html;
?>
<div class="modal fade" id="updateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="updateForm" data-url="<?= $updateUrl ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Update MCQ</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mcq_id" id="mcq-id">
                    <div class="row g-3">
                        <div class="col-md-12"><label class="form-label">Question ID</label>
                        <input type="text" name="mcq_question_id" class="form-control" required>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Organ System</label>
                            <select name="mcq_organ_system_id" class="form-select">
                                <option value="">Select Organ System</option>
                                <?php foreach ($organSystems as $organSystem): ?>
                                    <option value="<?= $organSystem['id'] ?>"><?= Html::encode($organSystem['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <select name="mcq_subject_id" class="form-select">
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>"><?= Html::encode($subject['name']) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6"><label class="form-label">Chapter</label>
                        <select name="mcq_chapter_id"
                                class="form-select" required><?php foreach ($chapters as $chapter): ?>
                                    <option value="<?= $chapter['id'] ?>"><?= Html::encode($chapter['name']) ?></option><?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-md-6"><label class="form-label">Topic</label>
                        <select name="mcq_topic_id"
                                class="form-select" required><?php foreach ($topics as $topic): ?>
                                    <option value="<?= $topic['id'] ?>"><?= Html::encode($topic['name']) ?></option><?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-12"><label class="form-label">Question Text</label><textarea
                                name="mcq_question_text" class="form-control" rows="4" required></textarea></div>
                        <div class="col-12"><label class="form-label">Options</label></div>
                        <?php foreach (['a', 'b', 'c', 'd', 'e'] as $opt): ?>
                            <div class="col-12">
                                <div class="input-group"><span
                                        class="input-group-text"><?= strtoupper($opt) ?>.</span><input type="text"
                                        name="mcq_option_<?= $opt ?>" class="form-control"
                                        <?= ($opt == 'd' || $opt == 'e') ? '' : 'required'?>
                                        ></div>
                            </div>
                        <?php endforeach; ?>
                        <div class="col-md-6"><label class="form-label">Correct Option</label><select
                                name="mcq_correct_option" class="form-select" required>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                                <option value="e">E</option>
                            </select></div>
                        <div class="col-md-6"><label class="form-label">Reference</label><input type="text"
                                name="mcq_reference" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Explanation</label><textarea
                                name="mcq_explanation" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tags</label>
                                <input type="text" name="mcq_tags" class="form-control">
                            </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary"><i
                            class="fas fa-save me-2"></i>Save Changes</button></div>
            </form>
        </div>
    </div>
</div>
<?php
use yii\helpers\Html;
?>
<?php foreach ($mcqs as $mcq): ?>
    <tr>
        <td><span class="badge bg-secondary"><?= Html::encode($mcq->question_id) ?></span></td>

        <td><?= Html::encode($mcq->organSystemName ?? 'N/A') ?></td>

        <td><?= Html::encode($mcq->subjectName ?? 'N/A') ?></td>

        <td><?= Html::encode($mcq->chapterName ?? 'N/A') ?></td>

        <td><?= Html::encode($mcq->topicName ?? 'N/A') ?></td>

        <td><span class="question-text"
                title="<?= Html::encode($mcq->question_text) ?>"><?= Html::encode($mcq->question_text) ?></span>
        </td>
        <td><?= Yii::$app->formatter->asDate($mcq->created_at) ?></td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item view-details" href="#"
                           data-mcq="<?= htmlspecialchars(json_encode($mcq), ENT_QUOTES, 'UTF-8') ?>"><i
                                class="fas fa-eye me-2"></i>Details</a></li>
                    <li><a class="dropdown-item update-mcq" href="#"
                           data-mcq-id="<?= $mcq->id ?>"
                           data-question-id="<?= Html::encode($mcq->question_id) ?>"
                           data-topic-id="<?= $mcq->hierarchy->topic_id ?>"
                           data-subject-id="<?= $mcq->hierarchy->subject_id ?>"
                           data-organ-system-id="<?= $mcq->hierarchy->organsys_id ?>"
                           data-chapter-id="<?= $mcq->hierarchy->chapter_id ?>"
                           data-correct-option="<?= $mcq->correct_option ?>"
                           data-question-text="<?= Html::encode($mcq->question_text) ?>"
                           data-option-a="<?= Html::encode($mcq->option_a) ?>"
                           data-option-b="<?= Html::encode($mcq->option_b) ?>"
                           data-option-c="<?= Html::encode($mcq->option_c) ?>"
                           data-option-d="<?= Html::encode($mcq->option_d) ?>"
                           data-option-e="<?= Html::encode($mcq->option_e) ?>"
                           data-explanation="<?= Html::encode($mcq->explanation) ?>"
                           data-reference="<?= Html::encode($mcq->reference) ?>"
                           data-tags="<?= Html::encode($mcq->tags) ?>"
                        ><i class="fas fa-edit me-2"></i>Update</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item btn-delete text-danger" href="#"
                           data-id="<?= $mcq->id ?>"
                           data-item="MCQ <?= Html::encode($mcq->question_id) ?>"
                           data-url="<?= $mcqDeleteUrl ?>"><i
                                class="fas fa-trash-alt me-2"></i>Delete</a></li>
                </ul>
            </div>
        </td>
    </tr>
<?php endforeach; ?>
<?php

namespace app\modules\admin\controllers;

use app\models\Chapters;
use app\models\Hierarchy;
use app\models\OrganSystems;
use app\models\Subjects;
use app\models\Topics;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;
use yii\web\Response;
use app\models\Mcqs;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * Mcq controller for the `admin` module
 */
class McqController extends AdminBaseController
{
    protected function allowedRoles(): array
    {
        return ['Super Admin', 'Content Manager'];
    }

    public function actionAdd()
    {
        $topics = Topics::find()->asArray()->all();
        $chapters = Chapters::find()->asArray()->all();
        $subjects = Subjects::find()->asArray()->all();
        $organsys = OrganSystems::find()->asArray()->all();
        return $this->render('add', [
            'topics' => $topics,
            'chapters' => $chapters,
            'subjects' => $subjects,
            'organsys' => $organsys,
        ]);
    }

    public function actionImportMcq()
    {
        return $this->render('file');
    }

    public function actionManage()
    {
        $query = Mcqs::find()
            ->joinWith('hierarchy');

        $filters = Yii::$app->request->get();

        if (!empty($filters['question_id'])) {
            $query->andWhere(['like', 'question_id', $filters['question_id']]);
        }
        if (!empty($filters['organ_system'])) {
            $query->andWhere(['hierarchy.organsys_id' => $filters['organ_system']]);
        }
        if (!empty($filters['subject'])) {
            $query->andWhere(['hierarchy.subject_id' => $filters['subject']]);
        }
        if (!empty($filters['chapter'])) {
            $query->andWhere(['hierarchy.chapter_id' => $filters['chapter']]);
        }
        if (!empty($filters['topic'])) {
            $query->andWhere(['hierarchy.topic_id' => $filters['topic']]);
        }
        if (!empty($filters['question_text'])) {
            $query->andWhere(['like', 'question_text', $filters['question_text']]);
        }

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 40,
        ]);

        $mcqs = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('manage', [
            'mcqs' => $mcqs,
            'pagination' => $pagination,
            'filters' => $filters,
            'subjects' => Subjects::find()->all(),
            'organSystems' => OrganSystems::find()->all(),
            'chapters' => Chapters::find()->all(),
            'topics' => Topics::find()->all(),
        ]);
    }

    public function actionSaveMultiple()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $postData = Yii::$app->request->post('mcqs', []);
        $userId = Yii::$app->user->id;
        $successCount = 0;
        $duplicateCount = 0;

        foreach ($postData as $mcqData) {
            $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $mcqData['question_text'] ?? '')));
            $hash = hash('sha256', $normalized);

            if (Mcqs::find()->where(['question_hash' => $hash])->exists()) {
                $duplicateCount++;
                continue;
            }

            $hierarchy = Hierarchy::find()
                ->where(['topic_id' => $mcqData['topic'], 'chapter_id' => $mcqData['chapter'], 'subject_id' => $mcqData['subject'], 'organsys_id' => $mcqData['organ_sys']])
                ->one();
            if ($hierarchy) {
                $hierarchy_id = $hierarchy->id;
            } else {
                $hierarchy = new Hierarchy();
                $hierarchy->topic_id = $mcqData['topic'];
                $hierarchy->chapter_id = $mcqData['chapter'];
                $hierarchy->subject_id = $mcqData['subject'];
                $hierarchy->organsys_id = $mcqData['organ_sys'];
                $hierarchy->save();
                $hierarchy_id = $hierarchy->id;
            }

            $mcq = new Mcqs();
            $mcq->question_id = $mcqData['question_id'];
            $mcq->question_text = $mcqData['question_text'];
            $mcq->question_hash = $hash;
            $mcq->option_a = $mcqData['option_a'];
            $mcq->option_b = $mcqData['option_b'];
            $mcq->option_c = $mcqData['option_c'];
            $mcq->option_d = $mcqData['option_d'];
            $mcq->option_e = $mcqData['option_e'];
            $mcq->correct_option = strtoupper($mcqData['correct_option']);
            $mcq->explanation = $mcqData['explanation'] ?? null;
            $mcq->reference = $mcqData['reference'] ?? null;
            $mcq->hierarchy_id = $hierarchy_id;
            $mcq->tags = $mcqData['tags'] ?? null;
            $mcq->created_by = Yii::$app->admin->identity->id;

            if ($mcq->save()) {
                $successCount++;
            } else {
                return [
                    'success' => false,
                    'err' => $mcq->errors,
                    'message' => "{$successCount} MCQs saved, {$duplicateCount} duplicates skipped."
                ];
            }
        }
        if ($duplicateCount > 0) {
            return [
                'success' => 'warning',
                'saved' => $successCount,
                'message' => "{$successCount} MCQs saved, {$duplicateCount} duplicates skipped."
            ];
        } else {
            return [
                'success' => true,
                'saved' => $successCount,
                'message' => "{$successCount} MCQs saved"
            ];
        }
    }

    public function actionSaveFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $uploadedFile = UploadedFile::getInstanceByName('excelFile');
        if (!$uploadedFile) {
            return ['success' => false, 'message' => 'No file uploaded.'];
        }

        $importTimestamp = date('Ymd_His');
        Yii::$app->params['importTimestamp'] = $importTimestamp;

        $importLogFileName = 'mcq_import_errors_' . $importTimestamp . '.log';
        $importLogFilePath = Yii::getAlias('@runtime/logs/mcq_import/' . $importLogFileName);

        $importCategory = 'mcq-import-log-' . $importTimestamp;
        Yii::$app->log->targets['mcqImport'] = new \app\components\PlainFileTarget([
            'logFile' => $importLogFilePath,
            'logVars' => [],
            'categories' => [$importCategory],
        ]);

        $organSystems = OrganSystems::find()->select(['id', 'name'])->indexBy('name')->asArray()->all();
        $subjects = Subjects::find()->select(['id', 'name'])->indexBy('name')->asArray()->all();
        $chapters = Chapters::find()->select(['id', 'name'])->indexBy('name')->asArray()->all();
        $topics = Topics::find()->select(['id', 'name'])->indexBy('name')->asArray()->all();

        $hierarchyIndex = [];
        foreach (Hierarchy::find()->all() as $h) {
            $key = strtolower($h->organsys_id . '|' . $h->subject_id . '|' . $h->chapter_id . '|' . $h->topic_id);
            $hierarchyIndex[$key] = $h->id;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $spreadsheet = IOFactory::load($uploadedFile->tempName);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $header = array_map('trim', array_map('strtoupper', array_slice($rows[0], 0, 15)));
            $expectedHeader = [
                'QUESTION ID',
                'ORGAN SYSTEM',
                'SUBJECT',
                'CHAPTER',
                'TOPIC',
                'QUESTION',
                'A',
                'B',
                'C',
                'D',
                'E',
                'ANSWER',
                'EXPLANATION',
                'REFERENCE',
                'DIFFICULTYLEVEL'
            ];

            if ($header !== $expectedHeader) {
                $transaction->rollBack();
                $logMessage = 'Uploaded file header mismatch. Expected: ' . implode(', ', $expectedHeader) . '. Got: ' . implode(', ', $header);
                Yii::error($logMessage, $importCategory);
                Yii::info('[ERROR] ' . $logMessage, $importCategory);
                return ['success' => false, 'message' => 'Invalid file format. Please upload the correct MCQ template.'];
            }

            unset($rows[0]);

            $userId = Yii::$app->admin->identity->id;
            $batchSize = 100;
            $success = 0;
            $duplicates = 0;
            $failed = 0;
            $mcqBatchForDb = [];

            foreach ($rows as $rowIndex => $row) {

                if (empty(array_filter($row))) {
                    continue;
                }

                [$questionId, $osName, $subName, $chName, $topName] = array_map('trim', array_slice($row, 0, 5));
                $questionText = trim($row[5] ?? '');
                $optionA = $row[6] ?? '';
                $optionB = $row[7] ?? '';
                $optionC = $row[8] ?? '';
                $optionD = $row[9] ?? null;
                $optionE = $row[10] ?? null;
                $correctOption = strtoupper(trim($row[11] ?? ''));
                $explanation = $row[12] ?? null;
                $reference = $row[13] ?? null;
                $difficultyLevel = $row[14] ?? null;

                $questionHash = hash('sha256', strtolower(preg_replace('/\s+/', ' ', $questionText)));
                if (Mcqs::find()->where(['question_hash' => $questionHash])->exists()) {
                    $duplicates++;
                    $failed++;
                    Yii::info("Row " . ($rowIndex + 2) . " (QID: {$questionId}): SKIPPED - Duplicate question based on question statement.", $importCategory);
                    continue;
                }

                $osId = $organSystems[$osName]['id'] ?? null;
                $subId = $subjects[$subName]['id'] ?? null;
                $chId = $chapters[$chName]['id'] ?? null;
                $topId = $topics[$topName]['id'] ?? null;

                $key = strtolower($osId . '|' . $subId . '|' . $chId . '|' . $topId);
                $hierarchyId = $hierarchyIndex[$key] ?? null;

                if (!$hierarchyId) {
                    $heirarchy = new Hierarchy();
                    $heirarchy->organsys_id = $osId;
                    $heirarchy->subject_id = $subId;
                    $heirarchy->chapter_id = $chId;
                    $heirarchy->topic_id = $topId;
                    if (!$heirarchy->save()) {
                        $failed++;
                        $reason = "Failed to save new hierarchy. OS: {$osName} ({$osId}), Sub: {$subName} ({$subId}), Ch: {$chName} ({$chId}), Top: {$topName} ({$topId})";
                        Yii::info("Row " . ($rowIndex + 2) . " (QID: {$questionId}): FAILED - {$reason}", $importCategory);
                        continue;
                    }
                    $reason = "New hierarchy added. OS: {$osName} ({$osId}), Sub: {$subName} ({$subId}), Ch: {$chName} ({$chId}), Top: {$topName} ({$topId})";
                    Yii::info("Row " . ($rowIndex + 2) . " (QID: {$questionId}): FAILED - {$reason}", $importCategory);
                    $hierarchyId = $heirarchy->id;
                }

                if (empty($questionText) || empty($correctOption) || !in_array($correctOption, ['A', 'B', 'C', 'D', 'E'])) {
                    $failed++;
                    $reason = "Missing or invalid essential MCQ data (Question Text or Correct Option).";
                    Yii::info("Row " . ($rowIndex + 2) . " (QID: {$questionId}): FAILED - {$reason}", $importCategory);
                    continue;
                }

                $mcq = new Mcqs();
                $mcq->question_id = $questionId;
                $mcq->question_text = $questionText;
                $mcq->question_hash = $questionHash;
                $mcq->option_a = $optionA;
                $mcq->option_b = $optionB;
                $mcq->option_c = $optionC;
                $mcq->option_d = $optionD;
                $mcq->option_e = $optionE;
                $mcq->correct_option = $correctOption;
                $mcq->explanation = $explanation;
                $mcq->reference = $reference;
                $mcq->difficulty_level = $difficultyLevel;
                $mcq->hierarchy_id = $hierarchyId;
                $mcq->created_by = $userId;

                $mcqBatchForDb[] = $mcq;

                if (count($mcqBatchForDb) >= $batchSize) {
                    foreach ($mcqBatchForDb as $mcqModel) {
                        if ($mcqModel->save()) {
                            $success++;
                        } else {
                            $failed++;
                            $errors = $mcqModel->getErrors();
                            Yii::info("Row " . ($rowIndex + 2) . " (QID: {$mcqModel->question_id}): FAILED - DB Save Errors: " . Json::encode($errors), $importCategory);
                            Yii::error("DB Save failed for MCQ QID: {$mcqModel->question_id}. Errors: " . print_r($errors, true), 'mcq-import-error');
                        }
                    }
                    $mcqBatchForDb = [];
                }
            }

            if (!empty($mcqBatchForDb)) {
                foreach ($mcqBatchForDb as $mcqModel) {
                    if ($mcqModel->save()) {
                        $success++;
                    } else {
                        $failed++;
                        $errors = $mcqModel->getErrors();
                        Yii::info("Row " . ($rowIndex + 2) . " (QID: {$mcqModel->question_id}): FAILED - DB Save Errors: " . Json::encode($errors), $importCategory);
                        Yii::error("DB Save failed for final batch MCQ QID: {$mcqModel->question_id}. Errors: " . print_r($errors, true), 'mcq-import-error');
                    }
                }
            }

            $transaction->commit();
            Yii::getLogger()->flush(true);
            Yii::$app->log->targets['mcqImport']->export();
            $message = "Import finished. Imported: {$success}, Duplicates: {$duplicates}, Failed: {$failed}.";
            if ($failed > 0) {
                $downloadUrl = Yii::$app->urlManager->createAbsoluteUrl([
                    '/' . $this->module->id . '/' . $this->id . '/download-log',
                    'filename' => $importLogFileName,
                ]);

                $message .= " <a href='{$downloadUrl}' target='_blank'>Download detailed import log.</a>";
            }

            return [
                'success' => true,
                'message' => $message
            ];

        } catch (\Throwable $e) {
            $transaction->rollBack();
            $errorMessage = "MCQ import general error: " . $e->getMessage() . "\nStack: " . $e->getTraceAsString();
            Yii::error($errorMessage, 'mcq-import-exception');
            Yii::info('[FATAL ERROR] ' . $errorMessage, $importCategory);
            Yii::getLogger()->flush(true);
            Yii::$app->log->targets['mcqImport']->export();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionDeleteMcq()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post();
        $mcq = Mcqs::findOne($id);
        if (!$mcq) {
            return ['success' => false, 'message' => 'MCQ not found'];
        }
        return $mcq->delete()
            ? ['success' => true, 'message' => 'MCQ Deleted']
            : ['success' => false, 'message' => "MCQ could'nt be deleted"];
    }

    public function actionUpdate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();

        if (empty($data['mcq_id'])) {
            return ['success' => false, 'message' => 'MCQ ID is required'];
        }

        $mcq = Mcqs::findOne($data['mcq_id']);
        if (!$mcq) {
            return ['success' => false, 'message' => 'MCQ not found'];
        }

        $hierarchy = Hierarchy::findOne([
            'organsys_id' => $data['mcq_organ_system_id'],
            'subject_id' => $data['mcq_subject_id'],
            'chapter_id' => $data['mcq_chapter_id'],
            'topic_id' => $data['mcq_topic_id'],
        ]);

        if (!$hierarchy) {
            return ['success' => false, 'message' => 'Invalid hierarchy combination'];
        }

        $mcq->question_id = $data['mcq_question_id'] ?? $mcq->question_id;
        $mcq->question_text = $data['mcq_question_text'] ?? $mcq->question_text;
        $mcq->option_a = $data['mcq_option_a'] ?? $mcq->option_a;
        $mcq->option_b = $data['mcq_option_b'] ?? $mcq->option_b;
        $mcq->option_c = $data['mcq_option_c'] ?? $mcq->option_c;
        $mcq->option_d = $data['mcq_option_d'] ?? $mcq->option_d;
        $mcq->option_e = $data['mcq_option_e'] ?? $mcq->option_e;
        $mcq->hierarchy_id = $hierarchy->id;
        $mcq->correct_option = isset($data['mcq_correct_option']) ? strtoupper($data['mcq_correct_option']) : $mcq->correct_option;
        $mcq->explanation = $data['mcq_explanation'] ?? $mcq->explanation;
        $mcq->reference = $data['mcq_reference'] ?? $mcq->reference;
        $mcq->tags = $data['mcq_tags'] ?? $mcq->tags;


        if (isset($data['mcq_question_text'])) {
            $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $data['mcq_question_text'])));
            $mcq->question_hash = hash('sha256', $normalized);
        }

        if ($mcq->save()) {
            return ['success' => true, 'message' => 'MCQ updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Update failed', 'errors' => $mcq->getErrors()];
        }
    }
    public function actionDownloadLog($filename)
    {

        // if (!Yii::$app->user->can('viewMcqImportLogs')) {
        //     throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        // }

        if (!preg_match('/^mcq_import_errors_\d{8}_\d{6}\.log$/', $filename)) {
            throw new \yii\web\BadRequestHttpException('Invalid filename.');
        }

        $filePath = Yii::getAlias('@runtime/logs/mcq_import/' . $filename);

        if (!file_exists($filePath)) {
            throw new \yii\web\NotFoundHttpException('The requested log file does not exist.');
        }

        $normalizedFilePath = FileHelper::normalizePath($filePath);
        $expectedDir = FileHelper::normalizePath(Yii::getAlias('@runtime/logs/mcq_import'));
        if (strpos($normalizedFilePath, $expectedDir) !== 0) {
            throw new \yii\web\ForbiddenHttpException('Access to this file is forbidden.');
        }

        $response = Yii::$app->response->sendFile($filePath, $filename, ['inline' => false]);

        $response->on(Response::EVENT_AFTER_SEND, function ($event) use ($filePath) {

            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    Yii::info("Successfully deleted downloaded log file: {$filePath}", 'mcq-import-log-cleanup');
                } else {
                    Yii::error("Failed to delete downloaded log file: {$filePath}", 'mcq-import-log-cleanup-error');
                }
            }
        });

        return $response;
    }
}

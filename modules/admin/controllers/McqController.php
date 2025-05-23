<?php

namespace app\modules\admin\controllers;

use app\models\Chapters;
use app\models\Topics;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;
use yii\web\Response;
use app\models\Mcqs;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;

/**
 * Mcq controller for the `admin` module
 */
class McqController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionAdd()
    {
        $topics = Topics::find()->asArray()->all();
        return $this->render('add', [
            'topics' => $topics,
        ]);
    }

    public function actionImportMcq()
    {
        return $this->render('file');
    }

    public function actionManage()
    {
        $topics = Topics::find()->asArray()->all();

        $query = Mcqs::find()->with('topic');

        $pagination = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 20,
        ]);

        $mcqs = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
            ->all();

        return $this->render('manage', [
            'mcqs' => $mcqs,
            'topics' => $topics,
            'pagination' => $pagination,
        ]);
    }

    public function actionManageTopics()
    {
        $chapters = (new \yii\db\Query())
            ->select([
                'c.id',
                'c.name',
                'COUNT(t.id) AS topic_count'
            ])
            ->from(['c' => 'chapters'])
            ->leftJoin(['t' => 'topics'], 't.chapter_id = c.id')
            ->groupBy('c.id')
            ->all();
        $topics = Topics::find()
            ->select(['topics.*', 'chapters.name AS chapter_name'])
            ->leftJoin('chapters', 'chapters.id = topics.chapter_id')
            ->asArray()
            ->all();

        return $this->render('topics', [
            'topics' => $topics,
            'chapters' => $chapters,
        ]);
    }

    public function actionAddTopic()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $model = new Topics();
        $model->name = $data['name'];
        $model->chapter_id = $data['chapter_id'];
        return $model->save()
            ? ['success' => true, 'message' => 'Added Topic']
            : ['success' => false, 'message' => 'Topic couldnt be added'];
    }
    public function actionAddChapter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Chapters();
        $model->name = Yii::$app->request->post('name');
        return $model->save()
            ? ['success' => true, 'message' => 'Added Chapter']
            : ['success' => false, 'message' => 'Chapter couldnt be added'];
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
            $mcq->topic_id = $mcqData['topic_id'];
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

        try {
            $topics = Topics::find()
                ->select(['id', 'name'])
                ->indexBy('name')
                ->asArray()
                ->all();

            $spreadsheet = IOFactory::load($uploadedFile->tempName);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            unset($rows[0]);

            Yii::debug('Row count after header removal: ' . count($rows), 'mcq-import');

            $userId = Yii::$app->admin->identity->id;
            $batchSize = 100;
            $success = 0;
            $duplicates = 0;
            $failed = 0;
            $batch = [];

            foreach ($rows as $i => $row) {
                if (empty(array_filter($row))) {
                    continue;
                }
                $questionText = trim($row[2] ?? '');
                $questionHash = hash('sha256', strtolower(preg_replace('/\s+/', ' ', $questionText)));

                if (Mcqs::find()->where(['question_hash' => $questionHash])->exists()) {
                    Yii::debug('Duplicate found');
                    $duplicates++;
                    continue;
                }

                $topicName = trim($row[1] ?? '');
                $topicId = $topics[$topicName]['id'] ?? null;
                if (!$topicId) {
                    Yii::debug('Topic not found' . $row[0] . $row[1]);
                    $failed++;
                    continue;
                }

                $mcq = new Mcqs();
                $mcq->question_id = $row[0];
                $mcq->question_text = $questionText;
                $mcq->question_hash = $questionHash;
                $mcq->option_a = $row[3];
                $mcq->option_b = $row[4];
                $mcq->option_c = $row[5];
                $mcq->option_d = $row[6];
                $mcq->option_e = $row[7];
                $mcq->correct_option = strtoupper(trim($row[8]));
                $mcq->explanation = $row[9] ?? null;
                $mcq->reference = $row[10] ?? null;
                $mcq->topic_id = $topicId;
                $mcq->created_by = $userId;

                $batch[] = $mcq;

                if (count($batch) <= $batchSize || $i === array_key_last($rows)) {
                    foreach ($batch as $mcqModel) {
                        if ($mcqModel->save()) {
                            $success++;
                        } else {
                            var_dump($mcqModel->getErrors());
                            $failed++;
                            return [
                                'success' => false,
                                'message' => "Imported: {$success}, Duplicates: {$duplicates}, Failed: {$failed}.",
                                'errors' => $mcqModel->getErrors(),
                            ];
                        }
                    }
                    $batch = [];
                } else {
                    Yii::debug('Not running save model ');
                }
            }

            return [
                'success' => true,
                'message' => "Imported: {$success}, Duplicates: {$duplicates}, Failed: {$failed}."
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $params = Yii::$app->request->post();
        $query = Mcqs::find()->with('topic');

        if (!empty($params['question_id'])) {
            $query->andWhere(['question_id' => $params['question_id']]);
        }

        if (!empty($params['topic'])) {
            $query->andWhere(['topic_id' => $params['topic']]);
        }

        if (!empty($params['dates'])) {
            $parts = explode(' to ', $params['dates']);

            if (count($parts) === 2) {
                $dateFrom = trim($parts[0]);
                $dateTo = trim($parts[1]);

                $query->andWhere(['between', 'created_at', $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
            }
        }

        $mcqs = $query->orderBy(['created_at' => SORT_DESC])->asArray()->all();

        return ['data' => $mcqs];
    }

    public function actionDeleteMcq()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post();
        $mcq = Mcqs::findOne($id);
        if(!$mcq){
            return ['success' => false , 'message' => 'MCQ not found'];
        }
        return $mcq->delete()
        ?  ['success' => true , 'message' => 'MCQ Deleted']
        :   ['success' => false , 'message' => "MCQ could'nt be deleted"];
    }
}

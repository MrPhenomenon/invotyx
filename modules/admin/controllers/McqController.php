<?php

namespace app\modules\admin\controllers;

use app\models\Mcqs;
use Yii;
use yii\web\Controller;

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
        return $this->render('add');
    }

    public function actionImportMcq()
    {
        return $this->render('file');
    }

    public function actionSaveMultiple()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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
            $mcq->difficulty_level = $mcqData['difficulty_level'];
            $mcq->created_by = 0;

            if ($mcq->save()) {
                $successCount++;
            } else {
                return [
                    'success' => false,
                    'err' => $mcq->errors ,
                    'message' => "{$successCount} MCQs saved, {$duplicateCount} duplicates skipped."
                ];
            }
        }

        return [
            'success' => true,
            'saved' => $successCount,
            'duplicates' => $duplicateCount,
            'message' => "{$successCount} MCQs saved, {$duplicateCount} duplicates skipped."
        ];
    }


}

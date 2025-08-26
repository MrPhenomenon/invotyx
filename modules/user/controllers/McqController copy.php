<?php

namespace app\modules\user\controllers;

use app\models\ExamSessions;
use app\models\Mcqs;
use app\models\UserMcqInteractions;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `user` module
 */
class McqController extends Controller
{
    public $layout = '@app/modules/user/views/layouts/mcq';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionStart($session_id)
    {
        $userId = Yii::$app->user->id;
        $cacheKey = 'exam_state_' . $userId . '_' . $session_id;

        /** @var ExamSessions $session */
        $session = ExamSessions::findOne([
            'id' => $session_id,
            'user_id' => $userId,
            'status' => 'InProgress',
        ]);

        if (!$session) {
            Yii::$app->session->setFlash('danger', 'Exam session not found or already completed.');
            return $this->redirect(['exam/']);
        }

        $data = Yii::$app->cache->get($cacheKey);
        $mode = $data['mode'] ?? 'practice';

        if (!$data || empty($data['mcq_ids'])) {
            Yii::$app->session->setFlash('danger', 'Session data expired or invalid.');
            return $this->redirect(['exam/']);
        }

        $mcqIds = $data['mcq_ids'];
        $index = $data['current_index'] ?? 0;
        $responses = $data['responses'] ?? [];
        $currentIndex = $index;
        $attempted = count($responses);
        $total = count($mcqIds);
        $flagged = $data['flagged'] ?? [];
        $flaggedCount = count($flagged);

        $currentMcqId = $mcqIds[$index] ?? null;

        if (!$currentMcqId) {
            Yii::$app->session->setFlash('danger', 'No more questions available.');
            return $this->redirect(['exam/']);
        }

        /** @var Mcqs $mcq */
        $mcq = Mcqs::findOne($currentMcqId);

        if (!$mcq) {
            Yii::$app->session->setFlash('danger', 'Question not found.');
            return $this->redirect(['exam/']);
        }

        $timeLeft = null;
        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = $data['time_limit'] * 60;
            $timeLeft = max(0, $timeLimitSeconds - $elapsed);
        }

        return $this->render('take', [
            'mode' => $data['mode'] ?? 'practice', 
            'mcq' => $mcq,
            'index' => $index,
            'total' => $total,
            'sessionId' => $session_id,
            'progress' => [
                'attempted' => $attempted,
                'total' => $total,
                'flagged' => $flaggedCount,
                'percent' => round(($attempted / max($total, 1)) * 100),
            ],
            'timeLeft' => $timeLeft,
        ]);
    }
    public function actionSaveAnswer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $questionId = Yii::$app->request->post('question_id');
        $answer = Yii::$app->request->post('answer');
        $sessionId = Yii::$app->request->post('session_id');
        $userId = Yii::$app->user->id;
        $cacheKey = 'exam_state_' . $userId . '_' . $sessionId;

        if (!$questionId || !$answer || !$sessionId) {
            Yii::$app->session->setFlash('danger', 'Invalid data');
            return $this->redirect(['exam/']);
        }

        $data = Yii::$app->cache->get($cacheKey);
        if (!$data || empty($data['mcq_ids'])) {
            Yii::$app->session->setFlash('danger', 'Session expired.');
            return $this->redirect(['exam/']);
        }

        $mcqIds = $data['mcq_ids'];
        $responses = $data['responses'] ?? [];
        $index = $data['current_index'] ?? 0;

        $responses[$questionId] = $answer;

        $index++;
        $nextMcqId = $mcqIds[$index] ?? null;

        // Auto Submit on time end
        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = (int) $data['time_limit'] * 60;

            if ($elapsed >= $timeLimitSeconds) {
                $this->finalizeExamSession($sessionId, $userId, $data['responses'] ?? []);
                Yii::$app->session->setFlash('success', 'Exam time has ended and was auto-submitted.');
                return $this->redirect(['results/view?id='. $sessionId]);
            }
        }

        // If done
        if (!$nextMcqId) {
            $this->finalizeExamSession($sessionId, $userId, $responses);

            Yii::$app->session->setFlash('success', 'Exam Complete.');
            return $this->redirect(['results/view?id='. $sessionId]);
        }

        // Update cache
        $data['responses'] = $responses;
        $data['current_index'] = $index;
        Yii::$app->cache->set($cacheKey, $data, 3600);

        $mcq = Mcqs::findOne($nextMcqId);
        if (!$mcq) {
            Yii::$app->session->setFlash('danger', 'Next question not found.');
            return $this->redirect(['results/view?id='. $sessionId]);
        }

        $questionHtml = $this->renderPartial('partials/_question', [
            'index' => $index,
            'mcq' => $mcq,
            'sessionId' => $sessionId,
            'mode' => $data['mode'] ?? 'practice',
            'selectedOption' => $responses[$mcq->id] ?? null,
        ]);


        $progress = [
            'percent' => round((count($responses) / count($mcqIds)) * 100),
        ];

        return [
            'index' => $index,
            'success' => true,
            'questionHtml' => $questionHtml,
            'progress' => $progress,
        ];
    }

    private function finalizeExamSession($sessionId, $userId, $responses)
    {
        $session = ExamSessions::findOne([
            'id' => $sessionId,
            'user_id' => $userId,
        ]);

        if (!$session) {
            return;
        }

        $mcqIds = json_decode($session->mcq_ids, true);
        $mcqs = Mcqs::find()->where(['id' => $mcqIds])->all();

        $correctCount = 0;
        $totalQuestions = count($mcqIds);

        $now = date('Y-m-d H:i:s');
        $startTime = strtotime($session->start_time);
        $endTime = strtotime($now);
        $timeSpent = $endTime - $startTime;

        foreach ($mcqs as $mcq) {
            $selectedOption = $responses[$mcq->id] ?? null;
            $isCorrect = ($selectedOption !== null && $selectedOption == $mcq->correct_option) ? 1 : 0;
            if ($isCorrect) {
                $correctCount++;
            }

            // Insert into user_mcq_interactions
            $interaction = new UserMcqInteractions();
            $interaction->user_id = $userId;
            $interaction->mcq_id = $mcq->id;
            $interaction->exam_session_id = $sessionId;
            $interaction->selected_option = $selectedOption;
            $interaction->is_correct = $isCorrect;
            $interaction->flagged = 0;
            $interaction->attempted_at = $now;
            $interaction->time_spent_seconds = null;
            $interaction->save(false);
        }

        $session->status = 'Completed';
        $session->end_time = $now;
        $session->time_spent_seconds = $timeSpent;
        $session->correct_count = $correctCount;
        $session->accuracy = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        $session->updated_at = $now;

        $session->save(false);

        $cacheKey = 'exam_state_' . $userId . '_' . $sessionId;
        Yii::$app->cache->delete($cacheKey);
    }

    public function actionTimeUp()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sessionId = Yii::$app->request->post('session_id');
        $userId = Yii::$app->user->id;
        $cacheKey = 'exam_state_' . $userId . '_' . $sessionId;

        $data = Yii::$app->cache->get($cacheKey);
        if (!$data || empty($data['responses'])) {
            return ['success' => false, 'message' => 'Session expired or invalid.'];
        }

        $responses = $data['responses'];
        $this->finalizeExamSession($sessionId, $userId, $responses);

        return ['success' => true];
    }
}

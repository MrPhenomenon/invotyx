<?php

namespace app\modules\user\controllers;

use app\models\ExamSessions;
use app\models\Mcqs;
use app\models\StudyPlanDays;
use app\models\UserMcqInteractions;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MockExamController extends Controller
{

    public $layout = 'mcq';
    // const MANDATORY_BREAK_DURATION = 1800;
    const MANDATORY_BREAK_DURATION = 60; // 1 minute for testing

    public function actionTake($session)
    {
        $this->layout = 'mcq';
        $userId = Yii::$app->user->id;

        $cacheKey = 'exam_state_' . $userId . '_' . $session;
        $cache_session = Yii::$app->cache->get($cacheKey);

        if (!$cache_session) {
            Yii::$app->session->setFlash('danger', 'Your session has expired or could not be found.');
            return $this->redirect(['/user/dashboard']);
        }

        $part = $cache_session['part'];
        $partData = &$cache_session["part{$part}"];
        $currentTime = time();
        $oldLastActive = $cache_session['last_active_at'] ?? $currentTime;
        $idleThreshold = 60 * 5;

        if ($partData['start_time'] !== null && $currentTime - $oldLastActive > $idleThreshold) {
            $idleDuration = $currentTime - $oldLastActive - $idleThreshold;
            $idleDuration = max(0, $idleDuration);

            Yii::info("On-load timer adjusted for attempt $session (Part $part): Idle for $idleDuration seconds.", 'examTimer');

            $partData['start_time'] += $idleDuration;
            $cache_session['last_active_at'] = $currentTime;
            Yii::$app->cache->set($cacheKey, $cache_session, 86400);
        }

        $cache_session['last_active_at'] = $currentTime;
        Yii::$app->cache->set($cacheKey, $cache_session, 86400);

        if ($part === 2 && $partData['start_time'] === null) {
            $timeElapsedInBreak = 0;
            if ($cache_session['paused_at']) {
                $timeElapsedInBreak = time() - $cache_session['paused_at'];
            }
            $timeLeftInBreak = max(0, self::MANDATORY_BREAK_DURATION - $timeElapsedInBreak);

            if ($timeLeftInBreak > 0) {
                Yii::$app->session->setFlash('warning', 'Please wait until the mandatory break period is over before starting Part 2.');
                return $this->redirect(['break-screen', 'session' => $session]);
            } else {
                $partData['start_time'] = time();
                Yii::$app->cache->set($cacheKey, $cache_session, 86400);
            }
        }

        $examTimeLimit = 60 * 60 * 2;

        $timeSpent = 0;
        if ($partData['start_time'] !== null) {
            $timeSpent = time() - $partData['start_time'];
        }
        $timeLeft = max(0, $examTimeLimit - $timeSpent);


        if ($timeLeft <= 0) {
            Yii::$app->session->setFlash('info', 'Time for this part of the exam has elapsed. Proceeding to the next section.');
            if ($part === 1) {
                $cache_session['paused_at'] = time();
                $cache_session['part'] = 2;
                Yii::$app->cache->set($cacheKey, $cache_session, 86400);
                return $this->redirect(['break-screen', 'session' => $session]);
            } else {
                return $this->redirect(['finalize-exam-and-redirect', 'session' => $session]);
            }
        }

        $mcqId = null;

        if ($partData['revisiting_skipped']) {
            if (!empty($partData['skipped']) && isset($partData['skipped'][$partData['index']])) {
                $mcqId = $partData['skipped'][$partData['index']];
            } else {
                if ($part === 1) {
                    $cache_session['paused_at'] = time();
                    $cache_session['part'] = 2;
                    Yii::$app->cache->set($cacheKey, $cache_session, 86400);
                    return $this->redirect(['break-screen', 'session' => $session]);
                } else {
                    return $this->redirect(['finalize-exam-and-redirect', 'session' => $session]);
                }
            }
        } else {
            if (!empty($partData['mcqs']) && isset($partData['mcqs'][$partData['index']])) {
                $mcqId = $partData['mcqs'][$partData['index']];
            } else {
                if (!empty($partData['skipped'])) {
                    $partData['revisiting_skipped'] = true;
                    $partData['index'] = 0;
                    Yii::$app->cache->set($cacheKey, $cache_session, 86400);
                    return $this->redirect(['take', 'session' => $session]);
                } else {
                    if ($part === 1) {
                        $cache_session['paused_at'] = time();
                        $cache_session['part'] = 2;
                        Yii::$app->cache->set($cacheKey, $cache_session, 86400);
                        return $this->redirect(['break-screen', 'session' => $session]);
                    } else {
                        return $this->redirect(['finalize-exam-and-redirect', 'session' => $session]);
                    }
                }
            }
        }

        if (!$mcqId) {
            Yii::error("Fatal error in actionTakeExam: mcqId is null for attempt $session despite logical checks. Redirecting to finalize.", 'examError');
            Yii::$app->session->setFlash('danger', 'An unexpected error occurred. Please contact support. Attempting to finalize exam.');
            return $this->redirect(['finalize-exam-and-redirect', 'session' => $session]);
        }

        $mcq = Mcqs::findOne($mcqId);
        if (!$mcq) {
            Yii::error("MCQ ID $mcqId from session not found in DB for attempt $session. Redirecting to finalize.", 'examError');
            Yii::$app->session->setFlash('danger', 'A required question was not found. Please contact support. Attempting to finalize exam.');
            return $this->redirect(['finalize-exam-and-redirect', 'session' => $session]);
        }

        $actualQuestionNumber = array_search($mcqId, $cache_session["part{$part}"]['mcqs']);
        if ($actualQuestionNumber === false) {
            Yii::warning("MCQ ID $mcqId not found in original part{$part} MCQs array for attempt $session during display numbering. Falling back to current index.", 'examError');
            $actualQuestionNumber = $partData['index'];
        }
        $actualQuestionNumber++;

        if ($part === 2) {
            $actualQuestionNumber += count($cache_session['part1']['mcqs'] ?? []);
        }

        $totalQuestionsForProgress = count($cache_session["part{$part}"]['mcqs']);
        $attemptedCount = count($partData['responses']);

        $progress = [
            'attempted' => $attemptedCount,
            'total' => $totalQuestionsForProgress,
            'percent' => $totalQuestionsForProgress > 0 ? round(($attemptedCount / $totalQuestionsForProgress) * 100) : 0,
        ];

        $timeSpent = 0;
        if ($partData['start_time'] !== null) {
            $timeSpent = time() - $partData['start_time'];
        }

        $timeLeft = max(0, $examTimeLimit - $timeSpent);

        return $this->render('take-exam', [
            'mcq' => $mcq,
            'progress' => $progress,
            'timeLeft' => $timeLeft,
            'mode' => 'exam',
            'session' => $session,
            'skippedCount' => count($partData['skipped']),
            'actualQuestionNumber' => $actualQuestionNumber,
            'isRevisiting' => $partData['revisiting_skipped'],
        ]);
    }

    public function actionSaveAnswer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attemptId = Yii::$app->request->post('session_id');
        $mcq_id = Yii::$app->request->post('question_id');
        $answer = Yii::$app->request->post('answer');

        $session = Yii::$app->cache->get("exam_state_" . Yii::$app->user->getId() . "_" . $attemptId);

        $part = $session['part'];
        $partData = &$session["part{$part}"];

        $partData['responses'][$mcq_id] = $answer;

        return $this->processNextQuestion($session, $partData, $attemptId);
    }


    public function actionSkipQuestion()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attemptId = Yii::$app->request->post('session_id');
        $mcq_id = Yii::$app->request->post('question_id');
        $userId = Yii::$app->user->getId();

        $session = Yii::$app->cache->get("exam_state_{$userId}_{$attemptId}");

        $part = $session['part'];
        $partData = &$session["part{$part}"];

        if (!in_array($mcq_id, $partData['skipped'])) {
            $partData['skipped'][] = $mcq_id;
        }

        return $this->processNextQuestion($session, $partData, $attemptId);
    }


    private function processNextQuestion(&$session, &$partData, $attemptId)
    {
        $partData['index']++;

        $nextQuestionId = null;
        $shouldRedirect = false;
        $redirectUrl = null;
        $userId = Yii::$app->user->getId();

        if ($partData['revisiting_skipped']) {
            if ($partData['index'] < count($partData['skipped'])) {
                $nextQuestionId = $partData['skipped'][$partData['index']];
            } else {
                if ($session['part'] === 1) {
                    $session['paused_at'] = time();
                    $session['part'] = 2;
                    $shouldRedirect = true;
                    $redirectUrl = Url::to(['break-screen', 'session' => $attemptId]);
                } else {
                    $shouldRedirect = true;
                    $redirectUrl = Url::to(['finalize-exam-and-redirect', 'session' => $attemptId]);
                }
            }
        } else {
            if ($partData['index'] < count($partData['mcqs'])) {
                $nextQuestionId = $partData['mcqs'][$partData['index']];
            } else {

                if (!empty($partData['skipped'])) {
                    $partData['revisiting_skipped'] = true;
                    $partData['index'] = 0;
                    $shouldRedirect = true;
                    $redirectUrl = Url::to(['take', 'session' => $attemptId]);

                } else {

                    if ($session['part'] === 1) {
                        $session['paused_at'] = time();
                        $session['part'] = 2;
                        $shouldRedirect = true;
                        $redirectUrl = Url::to(['break-screen', 'attempt' => $attemptId]);
                    } else {
                        $shouldRedirect = true;
                        $redirectUrl = Url::to(['finalize-exam-and-redirect', 'session' => $attemptId]);
                    }
                }
            }
        }

        Yii::$app->cache->set("exam_state_{$userId}_{$attemptId}", $session, 86400);

        if ($shouldRedirect) {
            return ['success' => true, 'redirectUrl' => $redirectUrl];
        } else {
            $mcq = Mcqs::findOne($nextQuestionId);
            if (!$mcq) {
                Yii::error("MCQ ID $nextQuestionId not found during next question fetch for attempt $attemptId. Redirecting to finalize.", 'examError');
                return ['success' => false, 'message' => 'Next question not found. Attempting to finalize exam.', 'redirectUrl' => Url::to(['finalize-exam-and-redirect', 'attempt' => $attemptId])];
            }

            $currentPartNum = $session['part'];
            $actualQuestionNumber = array_search($nextQuestionId, $session["part{$currentPartNum}"]['mcqs']);
            if ($actualQuestionNumber === false) {
                $actualQuestionNumber = $partData['index'];
            }
            $actualQuestionNumber++;

            if ($currentPartNum === 2) {
                $actualQuestionNumber += count($session['part1']['mcqs'] ?? []);
            }

            $totalQuestionsForProgress = count($session["part{$currentPartNum}"]['mcqs']);
            $attemptedCount = count($partData['responses']);

            return [
                'success' => true,
                'questionHtml' => $this->renderPartial('partials/_question', [
                    'mcq' => $mcq,
                    'mode' => 'exam',
                    'actualQuestionNumber' => $actualQuestionNumber,
                    'isRevisiting' => $partData['revisiting_skipped'],
                ]),
                'progress' => [
                    'percent' => $totalQuestionsForProgress > 0 ? round(($attemptedCount / $totalQuestionsForProgress) * 100) : 0,
                    'attempted' => $attemptedCount,
                    'total' => $totalQuestionsForProgress,
                ],
                'skippedCount' => count($partData['skipped']),
                'isRevisiting' => $partData['revisiting_skipped'],
            ];
        }
    }


    public function actionBreakScreen($session)
    {
        $this->layout = 'main';

        $cacheKey = "exam_state_" . Yii::$app->user->getId() . '_' . $session;

        $cache = Yii::$app->cache->get($cacheKey);

        if ($cache['part'] !== 2 || ($cache['part'] === 2 && $cache['part2']['start_time'] !== null)) {
            Yii::$app->session->setFlash('warning', 'Invalid exam state for break screen. Redirecting to exam.');
            return $this->redirect(['take', 'session' => $session]);
        }

        $timeElapsedInBreak = 0;
        if ($cache['paused_at']) {
            $timeElapsedInBreak = time() - $cache['paused_at'];
        }

        $timeLeftInBreak = max(0, self::MANDATORY_BREAK_DURATION - $timeElapsedInBreak);
        if (Yii::$app->request->isPost && Yii::$app->request->post('action') === 'continue_part2') {
            if ($timeLeftInBreak > 0) {

                Yii::$app->session->setFlash('warning', 'Please wait until the mandatory break period is over.');
                return $this->redirect(['break-screen', 'attempt' => $session]);
            } else {

                if ($cache['part2']['start_time'] === null) {
                    $cache['part2']['start_time'] = time();
                    $cache['last_active_at'] = time();
                }
                Yii::$app->cache->set($cacheKey, $cache, 86400);
                return $this->redirect(['take', 'session' => $session]);
            }
        }

        return $this->render('break-screen', [
            'attempt' => $session,
            'timeLeftInBreak' => $timeLeftInBreak,
            'mandatoryBreakDuration' => self::MANDATORY_BREAK_DURATION,
        ]);
    }


    public function actionFinalizeExamInternal($session)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $examAttempt = ExamSessions::findOne($session);
        if (!$examAttempt) {
            return ['success' => false, 'message' => 'Exam attempt record not found.'];
        }

        $session_data = Yii::$app->cache->get('exam_state_' . Yii::$app->user->id . '_' . $session);

        if (!$session_data) {
            if ($examAttempt->status === 'completed') {
                return ['success' => true, 'message' => 'Exam already finalized.', 'attempt_id' => $session];
            }
        }

        $correctAnswersCount = 0;
        $totalQuestionsInExam = 0;
        $answeredQuestionsCount = 0;
        $examAnswersToSave = [];

        $allMcqIds = [];
        if (!empty($session_data['part1']['mcqs'])) {
            $allMcqIds = array_merge($allMcqIds, $session_data['part1']['mcqs']);
        }
        if (!empty($session_data['part2']['mcqs'])) {
            $allMcqIds = array_merge($allMcqIds, $session_data['part2']['mcqs']);
        }
        $totalQuestionsInExam = count($allMcqIds);

        $mcqsData = Mcqs::find()->where(['id' => $allMcqIds])->indexBy('id')->all();

        foreach (['part1', 'part2'] as $partKey) {
            $partData = $session_data[$partKey];

            foreach ($partData['responses'] as $mcqId => $userSelectedOption) {
                $mcq = $mcqsData[$mcqId] ?? null;

                if ($mcq) {
                    $isCorrect = (strtoupper($mcq->correct_option) == strtoupper($userSelectedOption)) ? 1 : 0; // Ensure case-insensitive comparison
                    if ($isCorrect) {
                        $correctAnswersCount++;
                    }
                    $answeredQuestionsCount++;

                    $examAnswersToSave[] = [
                        Yii::$app->user->id,
                        $mcq->id,
                        $examAttempt->id,
                        $userSelectedOption,
                        $isCorrect,
                    ];
                } else {
                    Yii::warning("MCQ ID $mcqId not found in mcqsData during finalization for attempt $session.", 'finalizeDebug');
                }
            }
        }

        if (!empty($examAnswersToSave)) {
            Yii::debug("Attempting batch insert for " . count($examAnswersToSave) . " answers.", 'finalizeDebug');
            try {
                $insertedRows = Yii::$app->db->createCommand()->batchInsert(
                    UserMcqInteractions::tableName(),
                    ['user_id', 'mcq_id', 'exam_session_id', 'selected_option', 'is_correct'],
                    $examAnswersToSave
                )->execute();
            } catch (\Exception $e) {
                return ['success' => false, 'message' => 'Failed to save individual answers.'];
            }
        }


        $examAttempt->end_time = date('Y-m-d H:i:s');
        $examAttempt->correct_count = $correctAnswersCount;
        $examAttempt->accuracy = ($totalQuestionsInExam > 0) ? round($correctAnswersCount / $totalQuestionsInExam * 100, 2) : 0;
        $examAttempt->time_spent_seconds = time() - strtotime($examAttempt->start_time);
        $examAttempt->status = 'Completed';

        if ($examAttempt->save(false)) {
            $plan = StudyPlanDays::findOne($session_data['plan_day_id']);
            $plan->status = StudyPlanDays::STATUS_COMPLETED;
            $plan->save(false);
            if ($session_data) {
                Yii::$app->cache->delete('exam_state_' . Yii::$app->user->id . '_' . $session);
            } else {
            }
            return ['success' => true, 'message' => 'Exam successfully finalized.', 'attempt_id' => $session];
        } else {
            return ['success' => false, 'message' => 'Failed to finalize exam record.'];
        }
    }


    public function actionFinalizeExamAndRedirect($session)
    {
        $result = $this->actionFinalizeExamInternal($session);

        if ($result['success']) {
            Yii::$app->session->setFlash('success', $result['message']);
            return $this->redirect(['/user/results/' . $result['attempt_id']]);
        } else {
            Yii::$app->session->setFlash('danger', $result['message'] . ' Please try again or contact support.');
            return $this->redirect(Yii::$app->homeUrl);
        }
    }

    public function actionHeartbeat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $attemptId = Yii::$app->request->post('attempt_id');
        $cache_key = 'exam_state_' . Yii::$app->user->id . '_' . $attemptId;
        try {
            $session = Yii::$app->cache->get($cache_key);

            if (!$session) {
                throw new NotFoundHttpException('Exam session invalid.');
            }

            $currentTime = time();
            $oldLastActive = $session['last_active_at'] ?? $currentTime;
            $idleThreshold = 60 * 1;

            $part = $session['part'];
            $partData = &$session["part{$part}"];

            if ($partData['start_time'] !== null && $currentTime - $oldLastActive > $idleThreshold) {
                $idleDuration = $currentTime - $oldLastActive - $idleThreshold;
                $idleDuration = max(0, $idleDuration);
                Yii::info("Timer adjusted for attempt $attemptId (Part $part): Idle for $idleDuration seconds.", 'examTimer');

                $partData['start_time'] += $idleDuration;
            }

            $session['last_active_at'] = $currentTime;
            Yii::$app->cache->set($cache_key, $session, 86400);

            return ['success' => true, 'message' => 'Heartbeat received and timer adjusted if necessary.'];

        } catch (NotFoundHttpException $e) {
            Yii::warning("Heartbeat failed for attempt $attemptId: " . $e->getMessage(), 'examTimer');
            return ['success' => false, 'message' => $e->getMessage(), 'redirectUrl' => Url::to(['site/index'])];
        } catch (\Exception $e) {
            Yii::error("Heartbeat error for attempt $attemptId: " . $e->getMessage(), 'examTimer');
            return ['success' => false, 'message' => 'An unexpected error occurred.'];
        }
    }

}

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

    private function _processExamProgression(&$data, $sessionId, $userId, $currentMcqId, $actionType, $answer = null)
    {
        $mcqIds = $data['mcq_ids'];
        $responses = &$data['responses'];
        $index = &$data['current_index'];
        $skippedMcqIds = &$data['skipped_mcq_ids']; // Now exists from actionStart initialization
        $isRevisitingSkipped = &$data['is_revisiting_skipped']; // Now exists from actionStart initialization
        $totalInitialQuestions = count($mcqIds);

        // 1. Handle the current question's action (save answer or skip)
        if ($actionType === 'save_answer' && $answer !== null) {
            $responses[$currentMcqId] = $answer;
            // If a question was answered, remove it from the skipped list if it was there
            $skippedMcqIds = array_filter($skippedMcqIds, fn($id) => $id !== $currentMcqId);
            $skippedMcqIds = array_values($skippedMcqIds); // Re-index array
        } elseif ($actionType === 'skip_question') {
            if (!in_array($currentMcqId, $skippedMcqIds)) {
                $skippedMcqIds[] = $currentMcqId;
            }
            // If a question is skipped, ensure it's removed from responses if previously answered
            if (isset($responses[$currentMcqId])) {
                unset($responses[$currentMcqId]);
            }
        }

        // 2. Determine the next index and mode
        $index++; // Always advance the index after an action

        $nextMcqId = null;
        $shouldFinalize = false;
        $shouldTransitionToSkippedPhase = false;
        $shouldRedirectToTakeAction = false; // Indicates a change in mode requiring `actionStart` reload

        if ($isRevisitingSkipped) {
            // We are revisiting skipped questions
            if ($index < count($skippedMcqIds)) {
                $nextMcqId = $skippedMcqIds[$index];
            } else {
                // All skipped questions have been revisited
                $shouldFinalize = true;
            }
        } else {
            // We are going through initial questions
            if ($index < $totalInitialQuestions) {
                $nextMcqId = $mcqIds[$index];
            } else {
                // All initial questions exhausted
                if (!empty($skippedMcqIds)) {
                    // Transition to revisiting skipped questions
                    $shouldTransitionToSkippedPhase = true;
                    $shouldRedirectToTakeAction = true; // Reload `actionStart` to show first skipped
                } else {
                    // No skipped questions, exam is fully complete
                    $shouldFinalize = true;
                }
            }
        }

        // Apply transition if needed
        if ($shouldTransitionToSkippedPhase) {
            $isRevisitingSkipped = true;
            $index = 0; // Reset index to start from the beginning of skipped questions
            $nextMcqId = null; // Next MCQ will be determined by `actionStart` after redirect
        }

        // Handle Time Limit Check (can be moved here for consistency with Controller 2, or kept separate)
        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = (int) $data['time_limit'] * 60;

            if ($elapsed >= $timeLimitSeconds) {
                $shouldFinalize = true; // Force finalization if time is up
                // Message will be set by the calling action
            }
        }

        return [
            'success' => true,
            'next_mcq_id' => $nextMcqId,
            'should_finalize' => $shouldFinalize,
            'should_redirect_to_take_action' => $shouldRedirectToTakeAction,
            'updated_responses_count' => count($responses),
            'updated_skipped_count' => count($skippedMcqIds),
            'is_revisiting_skipped' => $isRevisitingSkipped,
            'current_index_after_action' => $index,
            'total_initial_questions' => $totalInitialQuestions,
        ];
    }
    // --- END NEW HELPER FUNCTION ---


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

        if (!$data || empty($data['mcq_ids'])) {
            Yii::$app->session->setFlash('danger', 'Session data expired or invalid.');
            return $this->redirect(['exam/']);
        }

        // Initialize new fields if they don't exist (for existing sessions)
        $data['skipped_mcq_ids'] = $data['skipped_mcq_ids'] ?? [];
        $data['is_revisiting_skipped'] = $data['is_revisiting_skipped'] ?? false;

        $mcqIds = $data['mcq_ids'];
        $responses = $data['responses'] ?? [];
        $index = $data['current_index'] ?? 0;
        $skippedMcqIds = $data['skipped_mcq_ids'];
        $isRevisitingSkipped = $data['is_revisiting_skipped'];

        if ($isRevisitingSkipped) {
            $currentMcqId = $skippedMcqIds[$index] ?? null;

            if (!$currentMcqId && !empty($skippedMcqIds)) {
                $this->finalizeExamSession($session_id, $userId, $responses);
                Yii::$app->session->setFlash('success', 'You have revisited all skipped questions. Exam Complete.');
                Yii::$app->cache->delete($cacheKey);
                return $this->redirect(['results/view', 'id' => $session_id]);
            }
        } else {
            $currentMcqId = $mcqIds[$index] ?? null;

            if (!$currentMcqId && !empty($mcqIds)) {
                if (!empty($skippedMcqIds)) {

                    $data['is_revisiting_skipped'] = true;
                    $data['current_index'] = 0;
                    Yii::$app->cache->set($cacheKey, $data, 3600);
                    Yii::$app->session->setFlash('info', 'All initial questions answered. Now revisiting skipped questions.');
                    return $this->redirect(['mcq/start', 'session_id' => $session_id]);
                } else {
                    // No skipped, exam complete
                    $this->finalizeExamSession($session_id, $userId, $responses);
                    Yii::$app->session->setFlash('success', 'All questions answered. Exam Complete.');
                    Yii::$app->cache->delete($cacheKey);
                    return $this->redirect(['results/view', 'id' => $session_id]);
                }
            }
        }


        if (!$currentMcqId) {

            Yii::$app->session->setFlash('danger', 'No more questions available or an unexpected state.');
            $this->finalizeExamSession($session_id, $userId, $responses);
            Yii::$app->cache->delete($cacheKey); // Clean up cache
            return $this->redirect(['results/view', 'id' => $session_id]);
        }

        $mcq = Mcqs::findOne($currentMcqId);

        if (!$mcq) {
            Yii::$app->session->setFlash('danger', 'Question not found in database. Please contact support.');
            // Log this error
            Yii::error("MCQ ID {$currentMcqId} from cache was not found in database for session {$session_id}", __METHOD__);
            // Attempt to finalize gracefully
            $this->finalizeExamSession($session_id, $userId, $responses);
            Yii::$app->cache->delete($cacheKey); // Clean up cache
            return $this->redirect(['results/view', 'id' => $session_id]);
        }

        $timeLeft = null;
        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = $data['time_limit'] * 60;
            $timeLeft = max(0, $timeLimitSeconds - $elapsed);
        }

        $totalQuestionsToConsider = $isRevisitingSkipped ? count($skippedMcqIds) : count($mcqIds);
        $displayIndex = $index + 1; // 1-based index within the current phase

        // Calculate the *original overall number* of the current MCQ in the entire exam
        $overallQuestionNumber = array_search($mcq->id, $mcqIds); // Get 0-based index
        // Handle case where mcqId might not be found (e.g., malformed data, should log error)
        if ($overallQuestionNumber === false) {
            Yii::warning("MCQ ID {$mcq->id} not found in main mcq_ids array for session {$session_id}. Falling back to 0.", __METHOD__);
            $overallQuestionNumber = 0;
        }
        $overallQuestionNumber++; // Convert to 1-based

        return $this->render('take', [
            'mode' => $data['mode'] ?? 'practice',
            'mcq' => $mcq,
            'currentPhaseIndex' => $displayIndex,
            'totalQuestionsInPhase' => $totalQuestionsToConsider,
            'sessionId' => $session_id,
            'progress' => [
                'attempted' => count($responses),
                'total_questions_in_exam' => count($mcqIds),
                'skipped' => count($skippedMcqIds),
                'percent' => round(((count($responses) + count($skippedMcqIds)) / max(count($mcqIds), 1)) * 100),
            ],
            'timeLeft' => $timeLeft,
            'isRevisitingSkipped' => $isRevisitingSkipped,
            'overallQuestionNumber' => $overallQuestionNumber,
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

        if (!$questionId || !$sessionId) {
            return ['success' => false, 'message' => 'Invalid data provided.'];
        }

        $data = Yii::$app->cache->get($cacheKey);
        if (!$data || empty($data['mcq_ids'])) {
            return ['success' => false, 'message' => 'Session expired or invalid.', 'redirectUrl' => Url::to(['exam/'])];
        }


        $progressionResult = $this->_processExamProgression(
            $data,
            $sessionId,
            $userId,
            $questionId,
            'save_answer',
            $answer
        );

        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = (int) $data['time_limit'] * 60;
            if ($elapsed >= $timeLimitSeconds) {

                $this->finalizeExamSession($sessionId, $userId, $data['responses']);
                Yii::$app->cache->delete($cacheKey);
                Yii::$app->session->setFlash('success', 'Exam time has ended and was auto-submitted.');
                return ['success' => true, 'redirectUrl' => Url::to(['results/view', 'id' => $sessionId])];
            }
        }

        if (!$progressionResult['success']) {
            return ['success' => false, 'message' => $progressionResult['message'], 'redirectUrl' => Url::to(['exam/'])];
        }

        Yii::$app->cache->set($cacheKey, $data, 3600);

        if ($progressionResult['should_finalize']) {
            $this->finalizeExamSession($sessionId, $userId, $data['responses']);
            Yii::$app->cache->delete($cacheKey);
            Yii::$app->session->setFlash('success', 'Exam Complete.');
            return ['success' => true, 'redirectUrl' => Url::to(['results/view', 'id' => $sessionId])];
        } elseif ($progressionResult['should_redirect_to_take_action']) {
            Yii::$app->session->setFlash('info', 'All initial questions answered. Now revisiting skipped questions.');
            return ['success' => true, 'redirectUrl' => Url::to(['mcq/start', 'session_id' => $sessionId])];
        } else {
            $nextMcqId = $progressionResult['next_mcq_id'];
            $mcq = Mcqs::findOne($nextMcqId);

            if (!$mcq) {
                // ... error handling ...
            }

            // Calculate current question number for display within the current phase
            $totalQuestionsToConsider = $progressionResult['is_revisiting_skipped'] ? count($data['skipped_mcq_ids']) : count($data['mcq_ids']);
            $displayIndex = $data['current_index'] + 1; // current_index is already advanced by helper

            // Calculate the *original overall number* of the next MCQ in the entire exam
            $overallQuestionNumber = array_search($mcq->id, $data['mcq_ids']);
            if ($overallQuestionNumber === false) {
                Yii::warning("MCQ ID {$mcq->id} not found in main mcq_ids array for session {$sessionId}. Falling back to 0.", __METHOD__);
                $overallQuestionNumber = 0;
            }
            $overallQuestionNumber++; // Convert to 1-based

            $questionHtml = $this->renderPartial('partials/_question', [
                'mcq' => $mcq,
                'sessionId' => $sessionId,
                'mode' => $data['mode'] ?? 'practice',
                'selectedOption' => $data['responses'][$mcq->id] ?? null,
                'isRevisitingSkipped' => $progressionResult['is_revisiting_skipped'],
                'currentPhaseIndex' => $displayIndex, // 1-based index within current phase
                'totalQuestionsInPhase' => $totalQuestionsToConsider, // Total questions in current phase
                'overallQuestionNumber' => $overallQuestionNumber, // Pass the original 1-based overall question number
            ]);

            $progress = [
                'attempted' => $progressionResult['updated_responses_count'],
                'total_questions_in_exam' => count($data['mcq_ids']),
                'skipped' => $progressionResult['updated_skipped_count'],
                'percent' => round(((count($data['responses']) + count($data['skipped_mcq_ids'])) / max(count($data['mcq_ids']), 1)) * 100),
            ];

            return [
                'success' => true,
                'questionHtml' => $questionHtml,
                'progress' => $progress,
                'isRevisitingSkipped' => $progressionResult['is_revisiting_skipped'],
            ];
        }
    }


    public function actionSkipQuestion()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $questionId = Yii::$app->request->post('question_id');
        $sessionId = Yii::$app->request->post('session_id');
        $userId = Yii::$app->user->id;
        $cacheKey = 'exam_state_' . $userId . '_' . $sessionId;

        if (!$questionId || !$sessionId) {
            return ['success' => false, 'message' => 'Invalid data provided for skipping.'];
        }

        $data = Yii::$app->cache->get($cacheKey);
        if (!$data || empty($data['mcq_ids'])) {
            return ['success' => false, 'message' => 'Session expired or invalid.', 'redirectUrl' => \yii\helpers\Url::to(['exam/'])];
        }

        // --- Use the new helper function ---
        $progressionResult = $this->_processExamProgression(
            $data,
            $sessionId,
            $userId,
            $questionId,
            'skip_question'
        );

        // Check for time expiration *after* processing the skip for the current question
        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = (int) $data['time_limit'] * 60;
            if ($elapsed >= $timeLimitSeconds) {
                // If time's up, finalize and redirect, overriding normal progression
                $this->finalizeExamSession($sessionId, $userId, $data['responses']);
                Yii::$app->cache->delete($cacheKey);
                Yii::$app->session->setFlash('success', 'Exam time has ended and was auto-submitted.');
                return ['success' => true, 'redirectUrl' => \yii\helpers\Url::to(['results/view', 'id' => $sessionId])];
            }
        }
        // --- End time expiration check ---


        if (!$progressionResult['success']) {
            return ['success' => false, 'message' => $progressionResult['message'], 'redirectUrl' => Url::to(['exam/'])];
        }

        Yii::$app->cache->set($cacheKey, $data, 3600); // Save the updated state

        if ($progressionResult['should_finalize']) {
            $this->finalizeExamSession($sessionId, $userId, $data['responses']);
            Yii::$app->cache->delete($cacheKey);
            Yii::$app->session->setFlash('success', 'Exam Complete.');
            return ['success' => true, 'redirectUrl' => Url::to(['results/view', 'id' => $sessionId])];
        } elseif ($progressionResult['should_redirect_to_take_action']) {
            // Transition to revisiting skipped, reload actionStart to pick up new mode
            Yii::$app->session->setFlash('info', 'All initial questions answered. Now revisiting skipped questions.');
            return ['success' => true, 'redirectUrl' => Url::to(['mcq/start', 'session_id' => $sessionId])];
        } else {
            $nextMcqId = $progressionResult['next_mcq_id'];
            $mcq = Mcqs::findOne($nextMcqId);

            if (!$mcq) {

            }

            $totalQuestionsToConsider = $progressionResult['is_revisiting_skipped'] ? count($data['skipped_mcq_ids']) : count($data['mcq_ids']);
            $displayIndex = $data['current_index'] + 1;

            $overallQuestionNumber = array_search($mcq->id, $data['mcq_ids']);
            if ($overallQuestionNumber === false) {
                Yii::warning("MCQ ID {$mcq->id} not found in main mcq_ids array for session {$sessionId}. Falling back to 0.", __METHOD__);
                $overallQuestionNumber = 0;
            }
            $overallQuestionNumber++; // Convert to 1-based

            $questionHtml = $this->renderPartial('partials/_question', [
                'mcq' => $mcq,
                'sessionId' => $sessionId,
                'mode' => $data['mode'] ?? 'practice',
                'selectedOption' => $data['responses'][$mcq->id] ?? null,
                'isRevisitingSkipped' => $progressionResult['is_revisiting_skipped'],
                'currentPhaseIndex' => $displayIndex, // 1-based index within current phase
                'totalQuestionsInPhase' => $totalQuestionsToConsider, // Total questions in current phase
                'overallQuestionNumber' => $overallQuestionNumber, // Pass the original 1-based overall question number
            ]);

            $progress = [
                'attempted' => $progressionResult['updated_responses_count'],
                'total_questions_in_exam' => count($data['mcq_ids']),
                'skipped' => $progressionResult['updated_skipped_count'],
                'percent' => round(((count($data['responses']) + count($data['skipped_mcq_ids'])) / max(count($data['mcq_ids']), 1)) * 100),
            ];

            return [
                'success' => true,
                'questionHtml' => $questionHtml,
                'progress' => $progress,
                'isRevisitingSkipped' => $progressionResult['is_revisiting_skipped'],
            ];
        }
    }
    // --- END NEW ACTION FOR SKIPPING QUESTIONS ---


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
        // Count questions that were actually answered. Total questions for score is all questions in exam.
        $totalQuestionsInExam = count($mcqIds);

        $now = date('Y-m-d H:i:s');
        $startTime = strtotime($session->start_time);
        $endTime = strtotime($now);
        $timeSpent = $endTime - $startTime;

        foreach ($mcqs as $mcq) {
            $selectedOption = $responses[$mcq->id] ?? null;
            // A question is correct only if an option was selected AND it matches the correct option
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
            $interaction->flagged = 0; // The original 'flagged' in cache is not used here for interactions
            $interaction->attempted_at = $now;
            $interaction->time_spent_seconds = null; // This would require per-question time tracking, which is not implemented here
            $interaction->save(false);
        }

        $session->status = 'Completed';
        $session->end_time = $now;
        $session->time_spent_seconds = $timeSpent;
        $session->correct_count = $correctCount;
        $session->accuracy = $totalQuestionsInExam > 0 ? ($correctCount / $totalQuestionsInExam) * 100 : 0;
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
        if (!$data) { // Check for empty responses removed, as exam might have 0 responses but still need finalizing
            return ['success' => false, 'message' => 'Session expired or invalid.'];
        }

        $responses = $data['responses'] ?? []; // Ensure responses is an array
        $this->finalizeExamSession($sessionId, $userId, $responses);

        Yii::$app->cache->delete($cacheKey); // Ensure cache is cleared on time up finalization
        return ['success' => true];
    }
}

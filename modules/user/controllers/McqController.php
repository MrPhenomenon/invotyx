<?php

namespace app\modules\user\controllers;

use app\models\Chapters;
use app\models\ExamSessions;
use app\models\Mcqs;
use app\models\SpecialtyDistributions;
use app\models\UserBookmarkedMcqs;
use app\models\UserMcqInteractions;
use Yii;
use yii\db\Query;
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
        $skippedMcqIds = &$data['skipped_mcq_ids'];
        $isRevisitingSkipped = &$data['is_revisiting_skipped'];
        $totalInitialQuestions = count($mcqIds);

        if ($actionType === 'save_answer' && $answer !== null) {
            $responses[$currentMcqId] = $answer;
            $skippedMcqIds = array_filter($skippedMcqIds, fn($id) => $id !== $currentMcqId);
            $skippedMcqIds = array_values($skippedMcqIds);
        } elseif ($actionType === 'skip_question') {
            if (!in_array($currentMcqId, $skippedMcqIds)) {
                $skippedMcqIds[] = $currentMcqId;
            }
            if (isset($responses[$currentMcqId])) {
                unset($responses[$currentMcqId]);
            }
        }

        $index++;

        $nextMcqId = null;
        $shouldFinalize = false;
        $shouldTransitionToSkippedPhase = false;
        $shouldRedirectToTakeAction = false;

        if ($isRevisitingSkipped) {
            if ($index < count($skippedMcqIds)) {
                $nextMcqId = $skippedMcqIds[$index];
            } else {
                $shouldFinalize = true;
            }
        } else {
            if ($index < $totalInitialQuestions) {
                $nextMcqId = $mcqIds[$index];
            } else {
                if (!empty($skippedMcqIds)) {
                    $shouldTransitionToSkippedPhase = true;
                    $shouldRedirectToTakeAction = true;
                } else {
                    $shouldFinalize = true;
                }
            }
        }

        if ($shouldTransitionToSkippedPhase) {
            $isRevisitingSkipped = true;
            $index = 0;
            $nextMcqId = null;
        }

        if (!empty($data['time_limit']) && !empty($data['start_time'])) {
            $elapsed = time() - $data['start_time'];
            $timeLimitSeconds = (int) $data['time_limit'] * 60;

            if ($elapsed >= $timeLimitSeconds) {
                $shouldFinalize = true;

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

    public function actionStartEvaluationExam()
    {
        $userId = Yii::$app->user->id;
        $user = Yii::$app->user->identity;

        $totalExamQuestions = 100;
        $mcqsPerChapter = 2;
        $examType = 'test';

        $relevantSubjectIds = (new Query())
            ->select('subject_id')
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $user->specialty_id])
            ->column();

        if (empty($relevantSubjectIds)) {
            Yii::$app->session->setFlash('danger', 'No relevant subjects found for your specialty to create an evaluation exam.');
            return $this->redirect(['exam/']);
        }

        $allChapters = Chapters::find()
            ->innerJoin('hierarchy h', 'h.chapter_id = chapters.id')
            ->where(['h.subject_id' => $relevantSubjectIds])
            ->distinct()
            ->orderBy(['chapters.id' => SORT_ASC])
            ->all();

        if (empty($allChapters)) {
            Yii::$app->session->setFlash('danger', 'No chapters found to create an evaluation exam for your specialty.');
            return $this->redirect(['exam/']);
        }

        $selectedMcqIds = [];
        $chaptersProcessed = [];

        foreach ($allChapters as $chapter) {
            if (count($selectedMcqIds) >= $totalExamQuestions) {
                break;
            }

            $mcqQuery = Mcqs::find()->alias('m')
                ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id')
                ->where(['h.chapter_id' => $chapter->id])
                ->andWhere(['NOT IN', 'm.id', $selectedMcqIds])
                ->orderBy(new \yii\db\Expression('RAND()'))
                ->limit($mcqsPerChapter);

            $chapterMcqIds = $mcqQuery->column();
            $selectedMcqIds = array_merge($selectedMcqIds, $chapterMcqIds);
            $chaptersProcessed[] = $chapter->id;
        }

        $remainingQuestionsCount = $totalExamQuestions - count($selectedMcqIds);

        if ($remainingQuestionsCount > 0) {
            $randomMcqQuery = Mcqs::find()->alias('m')
                ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id')
                ->where(['h.subject_id' => $relevantSubjectIds])
                ->andWhere(['NOT IN', 'm.id', $selectedMcqIds]);
        
            $randomMcqQuery->orderBy(new \yii\db\Expression('RAND()'))
                ->limit($remainingQuestionsCount);

            $randomMcqIds = $randomMcqQuery->column();
            $selectedMcqIds = array_merge($selectedMcqIds, $randomMcqIds);
        }

        $selectedMcqIds = array_slice($selectedMcqIds, 0, $totalExamQuestions);

        if (empty($selectedMcqIds)) {
            Yii::$app->session->setFlash('danger', 'Could not find enough questions to create the evaluation exam.');
            return $this->redirect(['exam/']);
        }
        if (count($selectedMcqIds) < $totalExamQuestions) {
            Yii::$app->session->setFlash('warning', 'Only ' . count($selectedMcqIds) . ' questions were found for the evaluation exam due to content limitations.');
        }

        // 4. Create ExamSession
        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->name = 'Evaluation Exam';
        $session->exam_type = $user->exam_type;
        $session->specialty_id = $user->specialty_id;
        $session->mode = ExamSessions::MODE_TEST;
        $session->mcq_ids = json_encode($selectedMcqIds);
        $session->start_time = date('Y-m-d H:i:s');
        $session->status = 'InProgress';
        $session->total_questions = count($selectedMcqIds);
        $session->difficulty_level = 0;
        $session->time_limit_minutes = null;
        $session->randomize_questions = 1;
        $session->include_bookmarked = 0;
        $session->tags_used = json_encode([]);
        $session->topics_used = null;
        $session->organ_systems_used = null;
        $session->part_number = null;
        $session->mock_group_id = null;

        if (!$session->save()) {
            Yii::$app->session->setFlash('danger', 'Could not start evaluation exam session: ' . implode(', ', $session->getErrorSummary(true)));
            Yii::error('Failed to save evaluation exam session for user ' . $userId . ': ' . print_r($session->errors, true), 'exam-session-error');
            return $this->redirect(['/user/']); // Redirect to a generic user dashboard page
        }

        // 5. Cache Exam State
        $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
        $cacheDuration = ($session->time_limit_minutes !== null) ? ($session->time_limit_minutes * 60 + 3600) : (24 * 3600); // 24 hours for untimed exams

        Yii::$app->cache->set($cacheKey, [
            'mcq_ids' => $selectedMcqIds,
            'current_index' => 0,
            'responses' => [],
            'skipped_mcq_ids' => [],
            'is_revisiting_skipped' => false,
            'start_time' => time(),
            'time_limit' => $session->time_limit_minutes,
            'mode' => $examType,
            'session_id' => $session->id,
            'difficulty' => null,
            'randomize' => 1,
        ], $cacheDuration);

        // 6. Redirect to start taking the exam
        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
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

        if (!$data || empty($data['mcq_ids'])) {
            Yii::$app->session->setFlash('danger', 'Session data expired or invalid.');
            return $this->redirect(['exam/']);
        }

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
            Yii::error("MCQ ID {$currentMcqId} from cache was not found in database for session {$session_id}", __METHOD__);
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
        $displayIndex = $index + 1;

        $overallQuestionNumber = array_search($mcq->id, $mcqIds);
        if ($overallQuestionNumber === false) {
            Yii::warning("MCQ ID {$mcq->id} not found in main mcq_ids array for session {$session_id}. Falling back to 0.", __METHOD__);
            $overallQuestionNumber = 0;
        }
        $overallQuestionNumber++;
        $bookmarkedMcqIds = UserBookmarkedMcqs::find()
            ->select('mcq_id')
            ->where(['user_id' => $userId])
            ->column();

        $isBookmarkedForCurrentMcq = in_array($mcq->id, $bookmarkedMcqIds);
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
            'isBookmarked' => $isBookmarkedForCurrentMcq,
            'allBookmarkedMcqIds' => $bookmarkedMcqIds,
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

            $totalQuestionsToConsider = $progressionResult['is_revisiting_skipped'] ? count($data['skipped_mcq_ids']) : count($data['mcq_ids']);
            $displayIndex = $data['current_index'] + 1;

            $overallQuestionNumber = array_search($mcq->id, $data['mcq_ids']);
            if ($overallQuestionNumber === false) {
                Yii::warning("MCQ ID {$mcq->id} not found in main mcq_ids array for session {$sessionId}. Falling back to 0.", __METHOD__);
                $overallQuestionNumber = 0;
            }
            $overallQuestionNumber++;

            $questionHtml = $this->renderPartial('partials/_question', [
                'mcq' => $mcq,
                'sessionId' => $sessionId,
                'mode' => $data['mode'] ?? 'practice',
                'selectedOption' => $data['responses'][$mcq->id] ?? null,
                'isRevisitingSkipped' => $progressionResult['is_revisiting_skipped'],
                'currentPhaseIndex' => $displayIndex,
                'totalQuestionsInPhase' => $totalQuestionsToConsider,
                'overallQuestionNumber' => $overallQuestionNumber,
                'isBookmarked' => false,
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

        $progressionResult = $this->_processExamProgression(
            $data,
            $sessionId,
            $userId,
            $questionId,
            'skip_question'
        );

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
                'currentPhaseIndex' => $displayIndex,
                'totalQuestionsInPhase' => $totalQuestionsToConsider,
                'overallQuestionNumber' => $overallQuestionNumber,
                'isBookmarked' => false,
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
        $totalQuestionsInExam = count($mcqIds);

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
        if (!$data) {
            return ['success' => false, 'message' => 'Session expired or invalid.'];
        }

        $responses = $data['responses'] ?? []; // Ensure responses is an array
        $this->finalizeExamSession($sessionId, $userId, $responses);

        Yii::$app->cache->delete($cacheKey); // Ensure cache is cleared on time up finalization
        return ['success' => true];
    }

    public function actionSearch($q = null)
    {
        $this->layout = 'main';
        $query = Mcqs::find();

        if (!empty($q)) {
            $query->where(['like', 'question_text', $q]);
        }

        $mcqs = $query->limit(50)->all();

        return $this->render('search', [
            'mcqs' => $mcqs,
            'searchTerm' => $q,
        ]);
    }


}

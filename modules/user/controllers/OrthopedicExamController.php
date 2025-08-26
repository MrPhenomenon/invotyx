<?php

namespace app\modules\user\controllers;

use app\models\ExamSpecialties;
use app\models\PartnerExamAccess;
use app\models\PartnerExamAnswers;
use app\models\PartnerExamAttempts;
use app\models\PartnerExams;
use app\models\PartnerMcqs;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
/**
 * Default controller for the `user` module
 */
class OrthopedicExamController extends Controller
{

    public $layout = 'mcq';
    const MANDATORY_BREAK_DURATION = 1800;
    // const MANDATORY_BREAK_DURATION = 60; // 1 minute for testing
    /**
     * Helper to validate attempt and passkey for secure access.
     * @param int $attemptId
     * @param string $passkey
     * @return PartnerExamAttempts
     * @throws NotFoundHttpException 
     */
    protected function validateExamAccess($attemptId, $passkey)
    {
        $examAttempt = PartnerExamAttempts::findOne($attemptId);
        if (!$examAttempt) {
            Yii::$app->session->setFlash('danger', 'Exam attempt not found.');
            throw new NotFoundHttpException('The requested exam attempt does not exist.');
        }

        $session = Yii::$app->cache->get("partner_exam_{$attemptId}");

        if (!$session) {

            if ($examAttempt->status === 'completed') {
                return $examAttempt;
            }
            Yii::$app->session->setFlash('danger', 'Exam session expired due to inactivity.');
            throw new NotFoundHttpException('Exam session expired or invalid.');
        }

        if (!isset($session['passkey']) || $session['passkey'] !== $passkey) {
            Yii::$app->session->setFlash('danger', 'Invalid passkey for this exam attempt.');
            throw new NotFoundHttpException('Access denied: Invalid credentials.');
        }

        $accessRecord = PartnerExamAccess::findOne([
            'partner_exam_id' => $examAttempt->partner_exam_id,
            'email' => $examAttempt->user_email,
            'passkey' => $passkey,
        ]);

        if (!$accessRecord) {
            Yii::$app->session->setFlash('danger', 'Access record mismatch for this exam attempt.');
            throw new NotFoundHttpException('Access denied: Invalid credentials or record mismatch.');
        }


        return $examAttempt;
    }


    public function actionStartExam($id)
    {
        $this->layout = 'mcq';
        $form = new PartnerExamAttempts();

        $exam = PartnerExams::findOne(['id' => $id, 'is_active' => 1]);
        if (!$exam) {
            throw new NotFoundHttpException("Invalid or inactive exam.");
        }

        if ($form->load(Yii::$app->request->post())) {
            $form->partner_exam_id = $exam->id;


            // --- START NEW RESUME LOGIC BLOCK ---
            $existingOngoingAttempt = PartnerExamAttempts::find()
                ->where([
                    'partner_exam_id' => $exam->id,
                    'user_email' => $form->user_email,
                    'status' => 'started',
                ])
                ->andWhere(['is', 'completed_at', null])
                ->one();

            if ($existingOngoingAttempt) {
                $accessRecordForExisting = PartnerExamAccess::findOne([
                    'partner_exam_id' => $exam->id,
                    'email' => $form->user_email,
                    'passkey' => $form->passkey,
                ]);

                if ($accessRecordForExisting) { // Passkey matches for existing attempt access
                    $sessionCacheExists = Yii::$app->cache->exists("partner_exam_{$existingOngoingAttempt->id}");

                    if ($sessionCacheExists) {
                        Yii::$app->session->setFlash('info', 'You have an ongoing exam. Resuming your session.');
                        return $this->redirect([
                            'take-exam',
                            'attempt' => $existingOngoingAttempt->id,
                            'passkey' => $form->passkey,
                        ]);
                    } else {
                        Yii::$app->session->setFlash('warning', 'Your previous exam session has expired due to inactivity. Results are being finalized.');
                        return $this->redirect([
                            'finalize-exam-and-redirect',
                            'attempt' => $existingOngoingAttempt->id,
                            'passkey' => $form->passkey,
                        ]);
                    }
                } else { // Provided passkey does NOT match for the existing attempt access
                    Yii::$app->session->setFlash('danger', 'Invalid passkey for your ongoing exam. Please check your credentials.');
                    return $this->redirect(Yii::$app->request->url);
                }
            }
            // --- END NEW RESUME LOGIC BLOCK ---

            $accessRecord = PartnerExamAccess::findOne([
                'partner_exam_id' => $exam->id,
                'email' => $form->user_email,
                'passkey' => $form->passkey,
                'has_attempted' => 0,
            ]);

            if (!$accessRecord) {
                Yii::$app->session->setFlash('danger', 'Invalid access credentials or exam already attempted.');
                return $this->redirect(Yii::$app->request->url);
            } else {
                $form->started_at = date('Y-m-d H:i:s');
                $form->status = 'started';

                if ($form->save()) {
                    $accessRecord->has_attempted = 1;
                    $accessRecord->save(false);

                    $mcqs = PartnerMcqs::find()->where(['partner_exam_id' => $exam->id])->all();
                    shuffle($mcqs);

                    $totalMcqs = count($mcqs);
                    $part1Count = min(100, $totalMcqs);
                    $part2Count = $totalMcqs - $part1Count;

                    $mcqIds = array_map(fn($mcq) => $mcq->id, $mcqs);
                    $part1 = array_slice($mcqIds, 0, $part1Count);
                    $part2 = array_slice($mcqIds, $part1Count, $part2Count);

                    $sessionData = [
                        'part' => 1,
                        'part1' => [
                            'mcqs' => $part1,
                            'start_time' => time(),
                            'index' => 0,
                            'responses' => [],
                            'skipped' => [],
                            'revisiting_skipped' => false,
                        ],
                        'part2' => [
                            'mcqs' => $part2,
                            'start_time' => null,
                            'index' => 0,
                            'responses' => [],
                            'skipped' => [],
                            'revisiting_skipped' => false,
                        ],
                        'paused_at' => null,
                        'attempt_id' => $form->id,
                        'passkey' => $form->passkey,
                        'last_active_at' => time(),
                    ];

                    Yii::$app->cache->set("partner_exam_{$form->id}", $sessionData, 86400);
                    return $this->redirect(['take-exam', 'attempt' => $form->id, 'passkey' => $form->passkey]);
                } else {
                    Yii::$app->session->setFlash('danger', 'Failed to start exam: ' . implode(', ', $form->getFirstErrors()));
                }
            }
        } else {
            Yii::debug('Form NOT Loaded', 'examDebug');
            Yii::debug($form->getAttributes(), 'examDebug');
            Yii::debug($form->errors, 'examDebug');
        }

        return $this->render('start-exam', [
            'model' => $form,
            'exam' => $exam,
        ]);
    }

    public function actionTakeExam($attempt, $passkey)
    {
        $this->layout = 'mcq';
        $examAttempt = $this->validateExamAccess($attempt, $passkey);

        $session = Yii::$app->cache->get("partner_exam_{$attempt}");
        $part = $session['part'];
        $partData = &$session["part{$part}"];

        $currentTime = time();
        $oldLastActive = $session['last_active_at'] ?? $currentTime;
        $idleThreshold = 60 * 5;

        if ($partData['start_time'] !== null && $currentTime - $oldLastActive > $idleThreshold) {
            $idleDuration = $currentTime - $oldLastActive - $idleThreshold;
            $idleDuration = max(0, $idleDuration);

            Yii::info("On-load timer adjusted for attempt $attempt (Part $part): Idle for $idleDuration seconds.", 'examTimer');

            $partData['start_time'] += $idleDuration;
            $session['last_active_at'] = $currentTime;
            Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
        }
        $session['last_active_at'] = $currentTime;
        Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);

        if ($part === 2 && $partData['start_time'] === null) {
            $timeElapsedInBreak = 0;
            if ($session['paused_at']) {
                $timeElapsedInBreak = time() - $session['paused_at'];
            }
            $timeLeftInBreak = max(0, self::MANDATORY_BREAK_DURATION - $timeElapsedInBreak);

            if ($timeLeftInBreak > 0) {
                Yii::$app->session->setFlash('warning', 'Please wait until the mandatory break period is over before starting Part 2.');
                return $this->redirect(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]);
            } else {
                $partData['start_time'] = time();
                Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
            }
        }

        $examTimeLimit = 9000; // 2.5 hours
        // $examTimeLimit = 60 * 5; 5 mins for testing
        $timeSpent = 0;
        if ($partData['start_time'] !== null) {
            $timeSpent = time() - $partData['start_time'];
        }
        $timeLeft = max(0, $examTimeLimit - $timeSpent);

        if ($timeLeft <= 0) {
            Yii::$app->session->setFlash('info', 'Time for this part of the exam has elapsed. Proceeding to the next section.');
            if ($part === 1) {
                $session['paused_at'] = time();
                $session['part'] = 2;
                Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
                return $this->redirect(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]);
            } else {
                return $this->redirect(['finalize-exam-and-redirect', 'attempt' => $attempt, 'passkey' => $passkey]);
            }
        }
        // --- END TIME LIMIT CHECK ON RESUME ---

        $mcqId = null;

        if ($partData['revisiting_skipped']) {
            if (!empty($partData['skipped']) && isset($partData['skipped'][$partData['index']])) {
                $mcqId = $partData['skipped'][$partData['index']];
            } else {
                if ($part === 1) {
                    $session['paused_at'] = time();
                    $session['part'] = 2;
                    Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
                    return $this->redirect(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]);
                } else {
                    return $this->redirect(['finalize-exam-and-redirect', 'attempt' => $attempt, 'passkey' => $passkey]);
                }
            }
        } else {
            if (!empty($partData['mcqs']) && isset($partData['mcqs'][$partData['index']])) {
                $mcqId = $partData['mcqs'][$partData['index']];
            } else {
                if (!empty($partData['skipped'])) {
                    $partData['revisiting_skipped'] = true;
                    $partData['index'] = 0;
                    Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
                    return $this->redirect(['take-exam', 'attempt' => $attempt, 'passkey' => $passkey]);
                } else {
                    if ($part === 1) {
                        $session['paused_at'] = time();
                        $session['part'] = 2;
                        Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400);
                        return $this->redirect(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]);
                    } else {
                        return $this->redirect(['finalize-exam-and-redirect', 'attempt' => $attempt, 'passkey' => $passkey]);
                    }
                }
            }
        }

        if (!$mcqId) {
            Yii::error("Fatal error in actionTakeExam: mcqId is null for attempt $attempt despite logical checks. Redirecting to finalize.", 'examError');
            Yii::$app->session->setFlash('danger', 'An unexpected error occurred. Please contact support. Attempting to finalize exam.');
            return $this->redirect(['finalize-exam-and-redirect', 'attempt' => $attempt, 'passkey' => $passkey]);
        }

        $mcq = PartnerMcqs::findOne($mcqId);
        if (!$mcq) {
            Yii::error("MCQ ID $mcqId from session not found in DB for attempt $attempt. Redirecting to finalize.", 'examError');
            Yii::$app->session->setFlash('danger', 'A required question was not found. Please contact support. Attempting to finalize exam.');
            return $this->redirect(['finalize-exam-and-redirect', 'attempt' => $attempt, 'passkey' => $passkey]);
        }

        $actualQuestionNumber = array_search($mcqId, $session["part{$part}"]['mcqs']);
        if ($actualQuestionNumber === false) {
            Yii::warning("MCQ ID $mcqId not found in original part{$part} MCQs array for attempt $attempt during display numbering. Falling back to current index.", 'examError');
            $actualQuestionNumber = $partData['index'];
        }
        $actualQuestionNumber++;

        if ($part === 2) {
            $actualQuestionNumber += count($session['part1']['mcqs'] ?? []);
        }

        $totalQuestionsForProgress = count($session["part{$part}"]['mcqs']);
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
            'attempt' => $attempt,
            'passkey' => $passkey,
            'skippedCount' => count($partData['skipped']),
            'actualQuestionNumber' => $actualQuestionNumber,
            'isRevisiting' => $partData['revisiting_skipped'],
        ]);
    }


    public function actionSaveAnswer()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attemptId = Yii::$app->request->post('session_id');
        $passkey = Yii::$app->request->post('passkey');
        $mcq_id = Yii::$app->request->post('question_id');
        $answer = Yii::$app->request->post('answer');

        $examAttempt = $this->validateExamAccess($attemptId, $passkey);
        $session = Yii::$app->cache->get("partner_exam_{$attemptId}");

        $part = $session['part'];
        $partData = &$session["part{$part}"];

        $partData['responses'][$mcq_id] = $answer;

        return $this->processNextQuestion($session, $partData, $attemptId, $passkey);
    }


    public function actionSkipQuestion()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $attemptId = Yii::$app->request->post('session_id');
        $passkey = Yii::$app->request->post('passkey');
        $mcq_id = Yii::$app->request->post('question_id');

        $examAttempt = $this->validateExamAccess($attemptId, $passkey);
        $session = Yii::$app->cache->get("partner_exam_{$attemptId}");

        $part = $session['part'];
        $partData = &$session["part{$part}"];

        if (!in_array($mcq_id, $partData['skipped'])) {
            $partData['skipped'][] = $mcq_id;
        }

        return $this->processNextQuestion($session, $partData, $attemptId, $passkey);
    }


    private function processNextQuestion(&$session, &$partData, $attemptId, $passkey)
    {
        $partData['index']++;

        $nextQuestionId = null;
        $shouldRedirect = false;
        $redirectUrl = null;

        if ($partData['revisiting_skipped']) {
            if ($partData['index'] < count($partData['skipped'])) {
                $nextQuestionId = $partData['skipped'][$partData['index']];
            } else {
                // All skipped questions for this part have been revisited -> Part complete
                if ($session['part'] === 1) {
                    $session['paused_at'] = time();
                    $session['part'] = 2;
                    $shouldRedirect = true;
                    $redirectUrl = Url::to(['break-screen', 'attempt' => $attemptId, 'passkey' => $passkey]);
                } else {
                    $shouldRedirect = true;
                    $redirectUrl = Url::to(['finalize-exam-and-redirect', 'attempt' => $attemptId, 'passkey' => $passkey]);
                }
            }
        } else { // Currently on initial questions
            if ($partData['index'] < count($partData['mcqs'])) {
                $nextQuestionId = $partData['mcqs'][$partData['index']];
            } else {
                // All initial questions for this part exhausted
                if (!empty($partData['skipped'])) {
                    // --- CRITICAL FIX: REDIRECT WHEN TRANSITIONING TO SKIPPED QUESTIONS ---
                    $partData['revisiting_skipped'] = true;
                    $partData['index'] = 0; // Reset index for skipped questions
                    $shouldRedirect = true; // <-- SET REDIRECT FLAG
                    // Redirect to take-exam itself, it will load the first skipped question from $partData
                    $redirectUrl = Url::to(['take-exam', 'attempt' => $attemptId, 'passkey' => $passkey]);
                    // --- END CRITICAL FIX ---
                } else {
                    // No skipped questions, so part is fully complete
                    if ($session['part'] === 1) {
                        $session['paused_at'] = time();
                        $session['part'] = 2;
                        $shouldRedirect = true;
                        $redirectUrl = Url::to(['break-screen', 'attempt' => $attemptId, 'passkey' => $passkey]);
                    } else {
                        $shouldRedirect = true;
                        $redirectUrl = Url::to(['finalize-exam-and-redirect', 'attempt' => $attemptId, 'passkey' => $passkey]);
                    }
                }
            }
        }

        Yii::$app->cache->set("partner_exam_{$attemptId}", $session, 86400);

        if ($shouldRedirect) {
            return ['success' => true, 'redirectUrl' => $redirectUrl];
        } else {
            $mcq = PartnerMcqs::findOne($nextQuestionId);
            if (!$mcq) {
                Yii::error("MCQ ID $nextQuestionId not found during next question fetch for attempt $attemptId. Redirecting to finalize.", 'examError');
                return ['success' => false, 'message' => 'Next question not found. Attempting to finalize exam.', 'redirectUrl' => Url::to(['finalize-exam-and-redirect', 'attempt' => $attemptId, 'passkey' => $passkey])];
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


    public function actionBreakScreen($attempt, $passkey)
    {
        $this->layout = 'mcq';

        try {
            $examAttempt = $this->validateExamAccess($attempt, $passkey);

            if ($examAttempt->status === 'completed') {
                return $this->redirect(['result', 'attempt' => $attempt, 'passkey' => $passkey]);
            }
        } catch (NotFoundHttpException $e) {
            Yii::$app->session->setFlash('danger', $e->getMessage());
            return $this->goHome();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('danger', 'An error occurred during access validation.');
            return $this->goHome();
        }

        $session = Yii::$app->cache->get("partner_exam_{$attempt}");

        if ($session['part'] !== 2 || ($session['part'] === 2 && $session['part2']['start_time'] !== null)) {
            Yii::$app->session->setFlash('warning', 'Invalid exam state for break screen. Redirecting to exam.');
            return $this->redirect(['take-exam', 'attempt' => $attempt, 'passkey' => $passkey]);
        }

        $timeElapsedInBreak = 0;
        if ($session['paused_at']) {
            $timeElapsedInBreak = time() - $session['paused_at'];
        }

        $timeLeftInBreak = max(0, self::MANDATORY_BREAK_DURATION - $timeElapsedInBreak);
        if (Yii::$app->request->isPost && Yii::$app->request->post('action') === 'continue_part2') {
            if ($timeLeftInBreak > 0) {

                Yii::$app->session->setFlash('warning', 'Please wait until the mandatory break period is over.');
                return $this->redirect(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]); // Re-render break screen
            } else {
                // Mandatory break is over, proceed to Part 2
                if ($session['part2']['start_time'] === null) {
                    $session['part2']['start_time'] = time();
                    $session['last_active_at'] = time();
                }
                Yii::$app->cache->set("partner_exam_{$attempt}", $session, 86400); // Save updated session state
                return $this->redirect(['take-exam', 'attempt' => $attempt, 'passkey' => $passkey]);
            }
        }

        // Render the break screen view (for GET request or if break time is not over)
        return $this->render('break-screen', [
            'attempt' => $attempt,
            'passkey' => $passkey,
            'timeLeftInBreak' => $timeLeftInBreak,
            'mandatoryBreakDuration' => self::MANDATORY_BREAK_DURATION,
        ]);
    }


    public function actionFinalizeExamInternal($attempt, $passkey)
    {
        // Yii::debug("FinalizeExamInternal called for attempt: $attempt, passkey: $passkey", 'finalizeDebug');
        Yii::$app->response->format = Response::FORMAT_JSON;

        $examAttempt = PartnerExamAttempts::findOne($attempt);
        if (!$examAttempt) {
            // Yii::error("Exam attempt record not found for ID: $attempt", 'finalizeDebug');
            return ['success' => false, 'message' => 'Exam attempt record not found.'];
        }
        // Yii::debug("Found ExamAttempt: ID {$examAttempt->id}, Status: {$examAttempt->status}", 'finalizeDebug');


        $session = Yii::$app->cache->get("partner_exam_{$attempt}");

        if ($session && $session['passkey'] === $passkey) {
            // Yii::debug("Session found and passkey matched.", 'finalizeDebug');
        } elseif ($session && $session['passkey'] !== $passkey) {
            // Yii::warning("Invalid passkey for session. Attempt ID: $attempt, Provided passkey: $passkey, Session passkey: {$session['passkey']}", 'finalizeDebug');
            return ['success' => false, 'message' => 'Invalid passkey for session. Access denied.'];
        } elseif (!$session) {
            // Yii::warning("Session not found for attempt: $attempt. Checking DB status.", 'finalizeDebug');
            if ($examAttempt->status === 'completed') {
                // Yii::debug("Exam already completed in DB. Returning success without re-processing.", 'finalizeDebug');
                return ['success' => true, 'message' => 'Exam already finalized.', 'attempt_id' => $attempt, 'passkey' => $passkey];
            }
            // Yii::warning("Exam session expired for attempt $attempt, but status not completed. Attempting to finalize remaining data from DB (session responses not available).", 'finalizeDebug');
        }

        $correctAnswersCount = 0;
        $totalQuestionsInExam = 0;
        $answeredQuestionsCount = 0;
        $examAnswersToSave = [];


        // Yii::debug("Starting score calculation.", 'finalizeDebug');

        if ($session) {
            // Yii::debug("Session data available for calculation.", 'finalizeDebug');

            $allMcqIds = [];
            if (!empty($session['part1']['mcqs'])) {
                $allMcqIds = array_merge($allMcqIds, $session['part1']['mcqs']);
            }
            if (!empty($session['part2']['mcqs'])) {
                $allMcqIds = array_merge($allMcqIds, $session['part2']['mcqs']);
            }
            $totalQuestionsInExam = count($allMcqIds);
            // Yii::debug("Total MCQs in exam (from session): " . $totalQuestionsInExam, 'finalizeDebug');

            $mcqsData = PartnerMcqs::find()->where(['id' => $allMcqIds])->indexBy('id')->all();
            // Yii::debug("Fetched " . count($mcqsData) . " MCQs from DB.", 'finalizeDebug');

            foreach (['part1', 'part2'] as $partKey) {
                $partData = $session[$partKey];
                // Yii::debug("Processing $partKey. Responses count: " . count($partData['responses']), 'finalizeDebug');

                foreach ($partData['responses'] as $mcqId => $userSelectedOption) {
                    $mcq = $mcqsData[$mcqId] ?? null;

                    if ($mcq) {
                        $isCorrect = (strtoupper($mcq->correct_option) == strtoupper($userSelectedOption)) ? 1 : 0; // Ensure case-insensitive comparison
                        if ($isCorrect) {
                            $correctAnswersCount++;
                        }
                        $answeredQuestionsCount++;

                        // Yii::debug("MCQ ID: $mcqId, User Answer: $userSelectedOption, Correct Answer: {$mcq->correct_option}, Is Correct: $isCorrect", 'finalizeDebug');

                        $examAnswersToSave[] = [
                            $examAttempt->id,
                            $mcq->id,
                            $userSelectedOption,
                            $isCorrect,
                        ];
                    } else {
                        Yii::warning("MCQ ID $mcqId not found in mcqsData during finalization for attempt $attempt.", 'finalizeDebug');
                    }
                }
            }
            // Yii::debug("Finished processing session responses. Correct: $correctAnswersCount, Answered: $answeredQuestionsCount", 'finalizeDebug');


            if (!empty($examAnswersToSave)) {
                Yii::debug("Attempting batch insert for " . count($examAnswersToSave) . " answers.", 'finalizeDebug');
                try {
                    $insertedRows = Yii::$app->db->createCommand()->batchInsert(
                        PartnerExamAnswers::tableName(),
                        ['exam_attempt_id', 'partner_mcq_id', 'selected_option', 'is_correct'],
                        $examAnswersToSave
                    )->execute();
                    // Yii::debug("Batch insert successful. Inserted $insertedRows rows.", 'finalizeDebug');
                } catch (\Exception $e) {
                    // Yii::error("Batch insert failed for exam attempt $attempt: " . $e->getMessage() . " Stack: " . $e->getTraceAsString(), 'finalizeDebug');
                    return ['success' => false, 'message' => 'Failed to save individual answers.'];
                }
            } else {
                // Yii::debug("No exam answers to save (examAnswersToSave is empty).", 'finalizeDebug');
            }
        } else {

            // Yii::warning("Finalization called for attempt $attempt, but session is gone. Cannot calculate score from session. Using existing DB data (likely 0 scores).", 'finalizeDebug');
            $correctAnswersCount = $examAttempt->correct_answers ?? 0;
            $totalQuestionsInExam = $examAttempt->total_questions ?? 0;
            $answeredQuestionsCount = PartnerExamAnswers::find()
                ->where(['exam_attempt_id' => $attempt])
                ->count();
            // Yii::debug("Calculated counts when session is null: Correct: $correctAnswersCount, Answered: $answeredQuestionsCount, TotalExam: $totalQuestionsInExam", 'finalizeDebug');
        }

        // Update PartnerExamAttempts record
        $examAttempt->completed_at = date('Y-m-d H:i:s');
        $examAttempt->score = $correctAnswersCount;
        $examAttempt->correct_answers = $correctAnswersCount;
        $examAttempt->total_questions = $totalQuestionsInExam;
        $examAttempt->status = 'completed';

        // Yii::debug("Saving ExamAttempt record. Score: {$examAttempt->score}, Correct: {$examAttempt->correct_answers}, Total: {$examAttempt->total_questions}, Status: {$examAttempt->status}", 'finalizeDebug');

        if ($examAttempt->save(false)) { // Save without validation as data is trusted from calculation
            // Yii::debug("ExamAttempt saved successfully.", 'finalizeDebug');
            if ($session) {
                Yii::$app->cache->delete("partner_exam_{$attempt}");
                // Yii::debug("Exam session cache deleted for attempt: $attempt.", 'finalizeDebug');
            } else {
                // Yii::debug("No session to delete for attempt: $attempt.", 'finalizeDebug');
            }
            return ['success' => true, 'message' => 'Exam successfully finalized.', 'attempt_id' => $attempt, 'passkey' => $passkey];
        } else {
            // Yii::error('Failed to save exam attempt record: ' . implode(', ', $examAttempt->getFirstErrors()), 'finalizeDebug');
            return ['success' => false, 'message' => 'Failed to finalize exam record.'];
        }
    }


    public function actionFinalizeExamAndRedirect($attempt, $passkey)
    {
        $result = $this->actionFinalizeExamInternal($attempt, $passkey);

        if ($result['success']) {
            Yii::$app->session->setFlash('success', $result['message']);
            return $this->redirect(['result', 'attempt' => $result['attempt_id'], 'passkey' => $result['passkey']]);
        } else {
            Yii::$app->session->setFlash('danger', $result['message'] . ' Please try again or contact support.');
            return $this->redirect(Yii::$app->homeUrl);
        }
    }

    public function actionResult($attempt)
    {
        $this->layout = 'mcq';

        $result = PartnerExamAttempts::findOne($attempt);

        if (!$result || !$result->completed_at) {
            Yii::$app->session->setFlash('danger', 'The results for this exam are not yet available.');
            return $this->redirect(['site/index']);
        }

        $query = PartnerExamAnswers::find()
            ->where(['exam_attempt_id' => $result->id])
            ->with('partnerMcq')
            ->orderBy(['id' => SORT_ASC]);


        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('result', [
            'attempt' => $result,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHeartbeat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $attemptId = Yii::$app->request->post('attempt_id');
        $passkey = Yii::$app->request->post('passkey');

        try {
            $examAttempt = $this->validateExamAccess($attemptId, $passkey);
            $session = Yii::$app->cache->get("partner_exam_{$attemptId}");

            if (!$session || $session['passkey'] !== $passkey) {
                throw new NotFoundHttpException('Exam session invalid or unauthorized.');
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
            Yii::$app->cache->set("partner_exam_{$attemptId}", $session, 86400);

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

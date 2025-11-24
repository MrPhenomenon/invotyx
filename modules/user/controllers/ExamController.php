<?php

namespace app\modules\user\controllers;

use app\models\Chapters;
use app\models\ExamSessions;
use app\models\Hierarchy;
use app\models\Mcqs;
use app\models\OrganSystems;
use app\models\SpecialtyDistributions;
use app\models\StudyPlanDays;
use app\models\StudyPlans;
use app\models\Subjects;
use app\models\Topics;
use app\models\UserBookmarkedMcqs;
use app\models\UserMcqInteractions;
use DateTime;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `user` module
 */
class ExamController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $organSystems = OrganSystems::find()->all();
        $subjects = Subjects::find()->all();
        $subjectsWithCounts = Hierarchy::getMcqCountsWithAttempts('subject', $userId);
        $OrganSystemsWithCounts = Hierarchy::getMcqCountsWithAttempts('organsys', $userId);

        $countMapSubjects = \yii\helpers\ArrayHelper::map($subjectsWithCounts, 'id', function ($item) {
            return [
                'total' => (int) $item['total_mcq_count'],
                'attempted' => (int) $item['attempted_mcq_count']
            ];
        });
        $countMapOrganSystems = \yii\helpers\ArrayHelper::map($OrganSystemsWithCounts, 'id', function ($item) {
            return [
                'total' => (int) $item['total_mcq_count'],
                'attempted' => (int) $item['attempted_mcq_count']
            ];
        });

        $subjects = Subjects::find()->orderBy(['name' => SORT_ASC])->all();
        $organSystems = OrganSystems::find()->orderBy(['name' => SORT_ASC])->all();

        foreach ($subjects as $subject) {
            $subject->mcq_count = $countMapSubjects[$subject->id] ?? 0;
        }
        foreach ($organSystems as $organSystem) {
            $organSystem->mcq_count = $countMapOrganSystems[$organSystem->id] ?? 0;
        }
        return $this->render('index', [
            'organSystems' => $organSystems,
            'subjects' => $subjects,
            'countMapSubjects' => $countMapSubjects,
            'countMapOrganSystems' => $countMapOrganSystems,
        ]);
    }

    public function actionMcq()
    {
        return $this->render('mcq');
    }

    public function actionStartExam()
    {
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return $this->redirect(['exam/']);
        }

        $userId = Yii::$app->user->id;
        $post = $request->post();

        $examType = $post['examtype'] ?? 'practice';
        $examScope = $post['exam_scope'] ?? 'subject_chapter_topic';

        $questionCount = (int) ($post['question_count'] ?? 10);
        $difficulty = $post['difficulty'] ?? null;
        $timeLimit = isset($post['untimed']) ? null : ($post['time_limit'] ?? 60);
        $randomize = $post['randomize_questions'] ?? 0;
        $attemptedWrong = $post['attemptedWrong'] ?? 0;
        $attempted = $post['previously-asked'] ?? 0;
        $includeBookmarked = $post['include_bookmarked'] ?? 0;

        $subjectIds = $post['subject_ids'] ?? [];
        $chapterIds = $post['chapter_ids'] ?? [];
        $topicIds = $post['topic_ids'] ?? [];

        $organSystemIds = $post['organ_system_ids'] ?? [];

        if (empty($difficulty) || in_array('0', $difficulty, true)) {
            $difficulty = NULL;
        }

        if (empty($subjectIds) && empty($organSystemIds)) {
            Yii::$app->session->setFlash('danger', 'Please select at least one subject or organ system.');
            return $this->redirect(['exam/']);
        }

        if (!in_array($examType, ['practice', 'test'])) {
            Yii::$app->session->setFlash('danger', 'Invalid exam type selected.');
            return $this->redirect(['exam/']);
        }
        if (!in_array($examScope, ['subject_chapter_topic', 'organ_system'])) {
            Yii::$app->session->setFlash('danger', 'Invalid exam scope selected.');
            return $this->redirect(['exam/']);
        }
        if ($questionCount < 10 || $questionCount > 200) {
            Yii::$app->session->setFlash('danger', 'Number of questions must be between 10 and 200.');
            return $this->redirect(['exam/']);
        }

        $query = Mcqs::find()->alias('m')
            ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id');

        if ($examScope === 'subject_chapter_topic') {
            if (!empty($subjectIds && !in_array('0', $subjectIds))) {
                $query->andWhere(['h.subject_id' => $subjectIds]);
            }
            if (!empty($chapterIds) && !in_array('0', $chapterIds)) {
                $query->andWhere(['h.chapter_id' => $chapterIds]);
            }
            if (!empty($topicIds) && !in_array('0', $topicIds)) {
                $query->andWhere(['h.topic_id' => $topicIds]);
            }
        } elseif ($examScope === 'organ_system') {
            if (!empty($organSystemIds)) {
                $query->andWhere(['h.organsys_id' => $organSystemIds]);
            }
        }

        if ($difficulty != NULL) {
            $query->andWhere(['m.difficulty_level' => $difficulty]);
        }

        if ($randomize) {
            $query->orderBy(new \yii\db\Expression('RAND()'));
        } else {
            $query->orderBy(['m.id' => SORT_ASC]);
        }

        if ($attemptedWrong) {

            $wrongMcqIdsSubquery = UserMcqInteractions::find()
                ->select('mcq_id')
                ->where(['user_id' => $userId, 'is_correct' => 0]);
            $query->andWhere(['IN', 'm.id', $wrongMcqIdsSubquery]);

            if (!$includeBookmarked) {

                $bookmarkedMcqIdsSubquery = UserBookmarkedMcqs::find()
                    ->select('mcq_id')
                    ->where(['user_id' => $userId]);
                $query->andWhere(['NOT IN', 'm.id', $bookmarkedMcqIdsSubquery]);
            }
        } else {
            if (!$attempted) {

                $attemptedMcqIdsSubquery = UserMcqInteractions::find()
                    ->select('mcq_id')
                    ->where(['user_id' => $userId]);
                $query->andWhere(['NOT IN', 'm.id', $attemptedMcqIdsSubquery]);
            }

            if (!$includeBookmarked) {
                $bookmarkedMcqIdsSubquery = UserBookmarkedMcqs::find()
                    ->select('mcq_id')
                    ->where(['user_id' => $userId]);
                $query->andWhere(['NOT IN', 'm.id', $bookmarkedMcqIdsSubquery]);
            }
        }

        $mcqIds = $query->limit($questionCount)->column();

        if (empty($mcqIds)) {
            Yii::$app->session->setFlash('danger', 'No questions found with your selected criteria. Please adjust your filters.');
            return $this->redirect(['exam/']);
        }
        if (count($mcqIds) < $questionCount) {
            Yii::$app->session->setFlash('warning', 'Not enough questions found with your selected criteria. ' . count($mcqIds) . ' questions were found for the exam.');
        }


        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->name = ucfirst($examType) . ' Exam';
        $session->exam_type = Yii::$app->user->identity->exam_type;
        $session->specialty_id = Yii::$app->user->identity->specialty_id;

        $session->mode = $examType;
        $session->mcq_ids = json_encode($mcqIds);
        $session->start_time = date('Y-m-d H:i:s');
        $session->status = 'InProgress';
        $session->total_questions = count($mcqIds);

        $session->difficulty_level = $difficulty;
        $session->time_limit_minutes = $timeLimit;
        $session->randomize_questions = $randomize ? 1 : 0;
        $session->include_bookmarked = $includeBookmarked ? 1 : 0;
        $session->tags_used = json_encode([]);

        if ($examScope === 'subject_chapter_topic') {
            if (!empty($topicIds) && in_array('0', $topicIds)) {
                $session->topics_used = implode(',', Topics::find()
                    ->select('id')
                    ->where(['chapter_id' => $chapterIds])
                    ->column());
            } elseif (!empty($topicIds)) {
                $session->topics_used = implode(',', $topicIds);
            } else {
                $session->topics_used = null;
            }
            $session->organ_systems_used = null;

        } elseif ($examScope === 'organ_system') {
            $session->topics_used = null;
            $session->organ_systems_used = implode(',', $organSystemIds);
        }

        $session->part_number = null;
        $session->mock_group_id = null;

        if (!$session->save()) {
            Yii::$app->session->setFlash('danger', 'Could not start exam session: ' . implode(', ', $session->getErrorSummary(true)));
            Yii::error('Failed to save exam session for user ' . $userId . ': ' . print_r($session->errors, true), 'exam-session-error');
            return $this->redirect(['/user/']);
        }

        $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
        $cacheDuration = 12 * 3600;

        Yii::$app->cache->set($cacheKey, [
            'mcq_ids' => $mcqIds,
            'current_index' => 0,
            'responses' => [],
            'skipped_mcq_ids' => [],
            'is_revisiting_skipped' => false,
            'start_time' => time(),
            'time_limit' => $timeLimit,
            'mode' => $examType,
            'session_id' => $session->id,
            'difficulty' => $difficulty,
            'randomize' => $randomize,
        ], $cacheDuration);

        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
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

        if (!$session->save()) {
            Yii::$app->session->setFlash('danger', 'Could not start evaluation exam session: ' . implode(', ', $session->getErrorSummary(true)));
            Yii::error('Failed to save evaluation exam session for user ' . $userId . ': ' . print_r($session->errors, true), 'exam-session-error');
            return $this->redirect(['/user/']);
        }

        $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
        $cacheDuration = 5 * 3600;

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
            'is_evaluation' => true
        ], $cacheDuration);

        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
    }

    public function actionStartStudyPlanExam()
    {
        $userId = Yii::$app->user->id;
        $user = Yii::$app->user->identity;
        $today = date('Y-m-d');

        $mode = Yii::$app->request->get('mode', 'test');
        $studyPlan = StudyPlans::findOne(['user_id' => $userId]);
        $studyPlanDay = StudyPlanDays::find()
            ->where(['study_plan_id' => $studyPlan->id, 'plan_date' => $today])
            ->andWhere(['IN', 'status', ['pending', 'in_progress']])
            ->one();

        if (!$studyPlanDay) {
            Yii::$app->session->setFlash('danger', 'No active study plan found for today, or it has already been completed/skipped. Please check your study plan.');
            return $this->redirect(['/user/']);
        }

        $allStudyPlanMcqIds = [];
        $totalAllocatedMcqs = 0;
        $uniqueTopicIdsUsed = [];

        $daySubjects = $studyPlanDay->getStudyPlanDaySubjects()->all();

        foreach ($daySubjects as $daySubject) {
            $allocatedMcqIdsFromJson = json_decode($daySubject->mcq_ids, true);

            Yii::debug($allocatedMcqIdsFromJson);

            if (!empty($allocatedMcqIdsFromJson) && is_array($allocatedMcqIdsFromJson)) {
                $allStudyPlanMcqIds = array_merge($allStudyPlanMcqIds, $allocatedMcqIdsFromJson);
                $totalAllocatedMcqs += count($allocatedMcqIdsFromJson);

                if ($daySubject->topic_id) {
                    $uniqueTopicIdsUsed[$daySubject->topic_id] = true;
                }
            }
        }

        $allStudyPlanMcqIds = array_unique($allStudyPlanMcqIds);
        Yii::debug($allStudyPlanMcqIds);
        shuffle($allStudyPlanMcqIds);
        $mcqIds = $allStudyPlanMcqIds;


        if (empty($mcqIds)) {
            Yii::$app->session->setFlash('danger', 'No questions could be assembled for today\'s study plan. Please contact support if this persists.');
            return $this->redirect(['/user/']);
        }
        if (count($mcqIds) < $totalAllocatedMcqs) {
            Yii::$app->session->setFlash('warning', 'Only ' . count($mcqIds) . ' questions were found for today\'s study plan. Expected ' . $totalAllocatedMcqs . ' unique questions. This might indicate duplicate allocations or issues in plan generation.');
        }

        $now = new DateTime();
        $endOfDay = (new DateTime())->setTime(23, 59, 59);

        if ($now > $endOfDay) {
            Yii::$app->session->setFlash('danger', "No time left for today's study plan. Please check your study plan.");
            return $this->redirect(['/user/']);
        } else {
            $interval = $now->diff($endOfDay);
            $timeLimitSeconds = $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
            $timeLimitMinutes = ceil($timeLimitSeconds / 60);

            if ($timeLimitMinutes <= 0 || ($timeLimitMinutes <= 0 && $timeLimitSeconds > 0)) {
                Yii::$app->session->setFlash('danger', "No time left for today's study plan. Please check your study plan.");
                return $this->redirect(['/user/']);
            }
        }

        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->name = 'Study Plan Test - Day ' . $studyPlanDay->day_number;
        $session->exam_type = $user->exam_type;
        $session->specialty_id = $user->specialty_id;

        $session->mode = $mode;
        $session->mcq_ids = json_encode($mcqIds);
        $session->start_time = date('Y-m-d H:i:s');
        $session->status = 'InProgress';
        $session->total_questions = count($mcqIds);

        $session->difficulty_level = null;
        $session->time_limit_minutes = $timeLimitMinutes;
        $session->randomize_questions = 1;
        $session->include_bookmarked = 0;
        $session->tags_used = json_encode([]);

        $session->topics_used = implode(',', array_keys($uniqueTopicIdsUsed));

        $session->part_number = null;
        $session->mock_group_id = null;
        $session->study_plan_day_id = $studyPlanDay->id;

        if (!$session->save()) {
            Yii::$app->session->setFlash('danger', 'Could not start study plan exam session: ' . implode(', ', $session->getErrorSummary(true)));
            Yii::error('Failed to save study plan exam session for user ' . $userId . ': ' . print_r($session->errors, true), 'study-plan-session-error');
            return $this->redirect(['/user/']);
        }

        $studyPlanDay->status = 'in_progress';
        if (!$studyPlanDay->save()) {
            Yii::error('Failed to update study plan day status to in_progress for day ' . $studyPlanDay->id . ': ' . print_r($studyPlanDay->errors, true), 'study-plan-status-update-error');
        }

        $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
        $cacheDuration = 24 * 3600;

        Yii::$app->cache->set($cacheKey, [
            'mcq_ids' => $mcqIds,
            'current_index' => 0,
            'responses' => [],
            'skipped_mcq_ids' => [],
            'is_revisiting_skipped' => false,
            'start_time' => time(),
            'time_limit' => NULL,
            'mode' => $session->mode,
            'session_id' => $session->id,
            'difficulty' => null,
            'randomize' => 1,
            'study_plan_day_id' => $studyPlanDay->id,
        ], $cacheDuration);

        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
    }

    public function actionStartMock()
    {
        $this->layout = 'mcq';
        $userId = Yii::$app->user->id;
        $user = Yii::$app->user->identity;

        $todayPlan = StudyPlanDays::find()
            ->alias('spd')
            ->joinWith(['studyPlan sp'])
            ->where(['sp.user_id' => $userId])
            ->andWhere(['spd.plan_date' => date('Y-m-d')])
            ->andWhere(['spd.is_mock_exam' => 1])
            ->andWhere( ['spd.status' => StudyPlanDays::STATUS_PENDING])
            ->one();

            Yii::debug($todayPlan);
        if (!$todayPlan || $todayPlan->is_mock_exam != 1) {
            Yii::$app->session->setFlash('danger', "No mock exam available for today. Please check your study plan.");
            return $this->redirect(['/user/default/index']);
        }

        $totalTarget = 200;
        $specialtyId = $user->specialty_id;

        $distributions = SpecialtyDistributions::find()
            ->where(['specialty_id' => $specialtyId])
            ->with('specialtyDistributionChapters')
            ->all();

        $selectedMcqIds = [];
        $allocated = 0;
        $roundingBuffer = [];

        foreach ($distributions as $dist) {
            $subjectTarget = $totalTarget * ($dist->subject_percentage / 100);
            $subjectAllocated = 0;
            foreach ($dist->specialtyDistributionChapters as $ch) {
                $chapterTarget = $subjectTarget * ($ch->chapter_percentage / 100);
                $intTarget = floor($chapterTarget);
                $allocated += $intTarget;
                $subjectAllocated += $intTarget;
                $roundingBuffer[] = [
                    'chapter_id' => $ch->chapter_id,
                    'fraction' => $chapterTarget - $intTarget,
                ];

                if ($intTarget > 0) {
                    $ids = Mcqs::find()
                        ->joinWith('hierarchy h')
                        ->where(['h.chapter_id' => $ch->chapter_id])
                        ->select('mcqs.id')
                        ->orderBy(new Expression('RAND()'))
                        ->limit($intTarget)
                        ->column();
                    $selectedMcqIds = array_merge($selectedMcqIds, $ids);
                }
            }
        }

        $remaining = $totalTarget - $allocated;

        if ($remaining > 0 && !empty($roundingBuffer)) {

            usort($roundingBuffer, fn($a, $b) => $b['fraction'] <=> $a['fraction']);
            foreach (array_slice($roundingBuffer, 0, $remaining) as $extra) {
                $extraId = Mcqs::find()
                    ->joinWith('hierarchy h')
                    ->where(['h.chapter_id' => $extra['chapter_id']])
                    ->select('mcqs.id')
                    ->orderBy(new Expression('RAND()'))
                    ->limit(1)
                    ->scalar();
                if ($extraId) {
                    $selectedMcqIds[] = $extraId;
                }
            }
        }

        if (count($selectedMcqIds) < $totalTarget) {
            $extra = Mcqs::find()
                ->select('id')
                ->orderBy(new Expression('RAND()'))
                ->limit($totalTarget - count($selectedMcqIds))
                ->column();
            $selectedMcqIds = array_merge($selectedMcqIds, $extra);
        }

        $selectedMcqIds = array_unique($selectedMcqIds);
        $selectedMcqIds = array_slice($selectedMcqIds, 0, $totalTarget);


        shuffle($selectedMcqIds);

        $totalMcqs = count($selectedMcqIds);
        $part1Count = min(100, $totalMcqs);
        $part2Count = max(0, $totalMcqs - $part1Count);
        $part1 = array_slice($selectedMcqIds, 0, $part1Count);
        $part2 = array_slice($selectedMcqIds, $part1Count, $part2Count);


        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->name = 'Mock Exam';
        $session->exam_type = $user->exam_type ?? null;
        $session->specialty_id = $user->specialty_id ?? null;
        $session->mode = $session::MODE_MOCK;
        $session->mcq_ids = json_encode($selectedMcqIds);
        $session->start_time = date('Y-m-d H:i:s');
        $session->status = 'InProgress';
        $session->total_questions = $totalMcqs;

        $session->time_limit_minutes = $exam->time_limit_minutes ?? null;
        $session->randomize_questions = 1;
        $session->include_bookmarked = 0;
        $session->tags_used = json_encode([]);
        $session->part_number = null;
        $session->study_plan_day_id = $todayPlan->id;

        if (!$session->save(false)) {
            Yii::$app->session->setFlash('danger', 'Could not create mock exam session: ' . implode(', ', $session->getErrorSummary(true)));
            Yii::error('Failed to save mock exam session for user ' . $userId . ': ' . print_r($session->errors, true), 'mock-session-error');
            return $this->redirect(['/user/']);
        }

        $todayPlan->status = StudyPlanDays::STATUS_IN_PROGRESS;
        $todayPlan->save(false);

        $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
        $cacheDuration = 24 * 3600;

        $sessionCache = [
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
            'attempt_id' => $session->id,
            'last_active_at' => time(),
            'plan_day_id' => $todayPlan->id,
        ];

        Yii::$app->cache->set($cacheKey, $sessionCache, $cacheDuration);

        Yii::$app->session->setFlash('success', 'Mock exam session created. Good luck!');
        return $this->redirect(['/user/mock-exam/take', 'session' => $session->id]);
    }

    public function actionToggleBookmark()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;
        $mcqId = Yii::$app->request->post('mcq_id');

        if (!$mcqId) {
            return ['success' => false, 'message' => 'Invalid question ID provided.', 'code' => 400];
        }

        $mcq = Mcqs::findOne($mcqId);
        if (!$mcq) {
            return ['success' => false, 'message' => 'Question not found in database.', 'code' => 404];
        }

        $bookmark = UserBookmarkedMcqs::findOne(['user_id' => $userId, 'mcq_id' => $mcqId]);

        if ($bookmark) {
            if ($bookmark->delete()) {
                return ['success' => true, 'action' => 'removed', 'message' => 'Bookmark removed.'];
            } else {
                Yii::error("Failed to remove bookmark for user $userId, MCQ $mcqId: " . print_r($bookmark->getErrors(), true), __METHOD__);
                return ['success' => false, 'message' => 'Failed to remove bookmark due to a server error.', 'code' => 500];
            }
        } else {
            $newBookmark = new UserBookmarkedMcqs([
                'user_id' => $userId,
                'mcq_id' => $mcqId,
            ]);
            if ($newBookmark->save()) {
                return ['success' => true, 'action' => 'added', 'message' => 'Question bookmarked successfully!'];
            } else {
                Yii::error("Failed to add bookmark for user $userId, MCQ $mcqId: " . print_r($newBookmark->getErrors(), true), __METHOD__);
                return ['success' => false, 'message' => 'Failed to add bookmark due to a server error.', 'code' => 500];
            }
        }
    }
}
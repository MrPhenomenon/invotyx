<?php

namespace app\modules\user\controllers;

use app\models\Chapters;
use app\models\ExamSessions;
use app\models\Hierarchy;
use app\models\Mcqs;
use app\models\OrganSystems;
use app\models\SpecialtyDistributions;
use app\models\Subjects;
use app\models\Topics;
use app\models\UserBookmarkedMcqs;
use app\models\UserMcqInteractions;
use Yii;
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
        $organSystems = OrganSystems::find()->all();
        $subjects = Subjects::find()->all();
        $subjectsWithCounts = Hierarchy::getMcqCounts('subject');
        $OrganSystemsWithCounts = Hierarchy::getMcqCounts('organsys');
        $countMapSubjects = \yii\helpers\ArrayHelper::map($subjectsWithCounts, 'id', 'mcq_count');
        $countMapOrganSystems = \yii\helpers\ArrayHelper::map($OrganSystemsWithCounts, 'id', 'mcq_count');

        foreach ($subjects as $subject) {
            $subject->mcq_count = $countMapSubjects[$subject->id] ?? 0;
        }
        foreach ($organSystems as $organSystem) {
            $organSystem->mcq_count = $countMapOrganSystems[$organSystem->id] ?? 0;
        }
        return $this->render('index', [
            'organSystems' => $organSystems,
            'subjects' => $subjects,
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
        if ($difficulty !== null && !in_array($difficulty, ['Easy', 'Moderate', 'Hard'])) {
            Yii::$app->session->setFlash('danger', 'Invalid difficulty level selected.');
            return $this->redirect(['exam/']);
        }

        $query = Mcqs::find()->alias('m')
            ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id');

        if ($examScope === 'subject_chapter_topic') {
            if (!empty($topicIds) && !in_array('0', $topicIds)) {
                $query->andWhere(['h.topic_id' => $topicIds]);
            } elseif (!empty($chapterIds) && !in_array('0', $chapterIds)) {
                $query->andWhere(['h.chapter_id' => $chapterIds]);
            } elseif (!empty($subjectIds)) {
                $query->andWhere(['h.subject_id' => $subjectIds]);
            }
        } elseif ($examScope === 'organ_system') {
            if (!empty($organSystemIds)) {
                $query->andWhere(['h.organsys_id' => $organSystemIds]);
            }
        }

        if ($difficulty !== null) {
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
        $session->name =  ucfirst($examScope) . ' Exam';
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
        $cacheDuration = ($timeLimit !== null) ? ($timeLimit * 60 + 3600) : (24 * 3600);

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
        $cacheDuration = ($session->time_limit_minutes !== null) ? ($session->time_limit_minutes * 60 + 3600) : (24 * 3600);

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

        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
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
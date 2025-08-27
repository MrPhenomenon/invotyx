<?php

namespace app\modules\user\controllers;

use app\models\Chapters;
use app\models\ExamSessions;
use app\models\Mcqs;
use app\models\OrganSystems;
use app\models\Subjects;
use app\models\Topics;
use app\models\UserBookmarkedMcqs;
use app\models\UserMcqInteractions;
use Yii;
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
        $chapters = Chapters::find()->all();
        $organSystems = OrganSystems::find()->all(); // Fetch all organ systems
        $subjects = Subjects::find()->all(); // Fetch all subjects

        return $this->render('index', [ // Your view file name
            'chapters' => $chapters,
            'organSystems' => $organSystems,
            'subjects' => $subjects,
        ]);
    }


    public function actionGetTopics()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $chapterIds = Yii::$app->request->get('chapter_ids', []);

        // Return topics matching selected chapters
        $topics = Topics::find()
            ->where(['chapter_id' => $chapterIds])
            ->select(['id', 'name'])
            ->asArray()
            ->all();

        return $topics;
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

        $query = Mcqs::find()->select('mcqs.id');

        if ($examScope === 'subject_chapter_topic') {
            if (!empty($topicIds) && !in_array('0', $topicIds)) {
                $query->andWhere(['mcqs.topic_id' => $topicIds]);
            } elseif (!empty($chapterIds)) {
                $allTopicsForChapters = Topics::find()
                    ->select('id')
                    ->where(['chapter_id' => $chapterIds])
                    ->column();
                if (empty($allTopicsForChapters)) {
                    Yii::$app->session->setFlash('warning', 'No topics found for selected chapters. Please adjust your criteria.');
                    return $this->redirect(['exam/']);
                }
                $query->andWhere(['mcqs.topic_id' => $allTopicsForChapters]);
            } elseif (!empty($subjectIds)) {
                $query->andWhere(['mcqs.subject_id' => $subjectIds]);
            }
        } elseif ($examScope === 'organ_system') {
            if (!empty($organSystemIds)) {
                $query->andWhere(['mcqs.organ_system_id' => $organSystemIds]);
            }
        }

        if ($difficulty !== null) {
            $query->andWhere(['mcqs.difficulty_level' => $difficulty]);
        }

        if ($randomize) {
            $query->orderBy(new \yii\db\Expression('RAND()'));
        } else {
            $query->orderBy(['mcqs.id' => SORT_ASC]);
        }

        $mcqIds = $query->limit($questionCount)->column();

        if (empty($mcqIds)) {
            Yii::$app->session->setFlash('danger', 'No questions found with your selected criteria. Please adjust your filters.');
            return $this->redirect(['exam/']);
        }
        if (count($mcqIds) < $questionCount) {
            Yii::$app->session->setFlash('warning', 'Not enough questions found with your selected criteria. ' . count($mcqIds) . ' questions were found for the exam.');
        }

        // $loggedMcqDetails = [];
        // $mcqModelsForLog = Mcqs::find()
        //     ->where(['id' => $mcqIds])
        //     ->with(['organSystem', 'subject', 'topic.chapter'])
        //     ->all();

        // foreach ($mcqModelsForLog as $mcq) {
        //     $detail = [
        //         'id' => $mcq->id,
        //         'question_id' => $mcq->question_id,
        //         'difficulty_level' => $mcq->difficulty_level,
        //         'organ_system' => $mcq->organSystem->name ?? 'N/A',
        //         'subject' => $mcq->subject->name ?? 'N/A',
        //         'chapter' => $mcq->topic->chapter->name ?? 'N/A',
        //         'topic' => $mcq->topic->name ?? 'N/A',
        //     ];
        //     $loggedMcqDetails[] = $detail;
        // }
        // Yii::debug(['Selected MCQs and their hierarchy' => $loggedMcqDetails], 'exam-mcq-selection');


        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->exam_type = Yii::$app->user->identity->exam_type;
        $session->specialty_id = Yii::$app->user->identity->speciality_id;

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

        if (!$this->userHasAccessToMcq($userId, $mcq)) {
            return ['success' => false, 'message' => 'Unauthorized: You do not have access to bookmark this question.', 'code' => 403];
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

    private function userHasAccessToMcq($userId, Mcqs $mcq)
    {
        $hasAttemptedMcq = UserMcqInteractions::find()
            ->where(['user_id' => $userId, 'mcq_id' => $mcq->id])
            ->exists();

        if ($hasAttemptedMcq) {
            return true;
        }

        return false;
    }
}
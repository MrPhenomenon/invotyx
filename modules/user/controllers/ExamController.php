<?php

namespace app\modules\user\controllers;

use app\models\Chapters;
use app\models\ExamSessions;
use app\models\Mcqs;
use app\models\Topics;
use app\models\UserMcqInteractions;
use Yii;
use yii\web\Controller;

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
        return $this->render('index', [
            'chapters' => $chapters,
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

        // Input collection
        $examMode = $post['examtype'] ?? 'practice';
        $questionCount = (int) ($post['question_count'] ?? 10);
        $difficulty = $post['difficulty'] ?? null;
        $chapterIds = $post['chapter_ids'] ?? [];
        $topicIds = $post['topic_ids'] ?? [];
        $timeLimit = isset($post['untimed']) ? null : ($post['time_limit'] ?? 60);
        $randomize = !empty($post['randomize_questions']);
        $includeBookmarked = !empty($post['include_bookmarked']);
        $tags = $post['tags'] ?? [];

        // Query MCQs
        $query = Mcqs::find()->select('mcqs.id');

        if (!empty($topicIds) && !in_array('0', $topicIds)) {
            $query->andWhere(['topic_id' => $topicIds]);
        } elseif (!empty($chapterIds)) {
            $query->joinWith('topic')->andWhere(['topics.chapter_id' => $chapterIds]);
        }

        if ($difficulty) {
            $query->andWhere(['difficulty_level' => $difficulty]);
        }

        // (Optional) Tag logic for unseen, attemptedWrong, etc.

        $mcqIds = $query->orderBy(new \yii\db\Expression('RAND()'))
            ->limit($questionCount)
            ->column();

        if (empty($mcqIds)) {
            Yii::$app->session->setFlash('danger', 'No questions found matching the selected criteria.');
            return $this->redirect(['exam/']);
        }

        if ($randomize) {
            shuffle($mcqIds);
        }

        // Create and Save Session
        $session = new ExamSessions();
        $session->user_id = $userId;
        $session->exam_type = Yii::$app->user->identity->exam_type;
        ;
        $session->specialty_id = Yii::$app->user->identity->speciality_id;
        $session->mode = $examMode;
        $session->topics_used = implode(',', $topicIds);
        $session->mcq_ids = json_encode($mcqIds);
        $session->start_time = date('Y-m-d H:i:s');
        $session->status = 'InProgress';
        $session->total_questions = $questionCount;

        if (!$session->save()) {
            Yii::$app->session->setFlash('danger', 'Could not start exam session.');
            Yii::debug($session->errors);
            return $this->redirect(['/user/']);
        }

        // Cache Session State
        $cacheKey = 'exam_' . $userId . '_' . $session->id;
        Yii::$app->cache->set($cacheKey, [
            'mcq_ids' => $mcqIds,
            'current_index' => 0,
            'responses' => [],
            'start_time' => time(),
            'time_limit' => $timeLimit,
        ], $timeLimit * 60 + 10);

        return $this->redirect(['/user/mcq/start', 'session_id' => $session->id]);
    }
}
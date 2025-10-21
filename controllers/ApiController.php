<?php

namespace app\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use app\models\Hierarchy;
use app\models\ManagementTeam;
use app\models\Subscriptions;
use app\models\Users;
use app\models\UserSubscriptions;
use yii\authclient\ClientInterface;
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\User;

class ApiController extends Controller
{

    public function actionGetChapters()
    {
        $subjectIds = Yii::$app->request->post('subject_ids', []);
        $chapters = Hierarchy::getChaptersForSubjects($subjectIds);

        $counts = Hierarchy::getMcqCounts('chapter');
        $countMap = \yii\helpers\ArrayHelper::map($counts, 'id', 'mcq_count');

        return $this->asJson(array_map(function ($c) use ($countMap) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'mcq_count' => $countMap[$c->id] ?? 0,
            ];
        }, $chapters));
    }

    public function actionGetTopics()
    {
        $chapterIds = Yii::$app->request->post('chapter_ids', []);
        $subjectIds = Yii::$app->request->post('subject_ids', []);

        $topics = Hierarchy::getTopicsForChapters($chapterIds, $subjectIds);

        // Get topic-level counts
        $counts = Hierarchy::getMcqCounts('topic');
        $countMap = \yii\helpers\ArrayHelper::map($counts, 'id', 'mcq_count');

        return $this->asJson(array_map(function ($t) use ($countMap) {
            return [
                'id' => $t->id,
                'name' => $t->name,
                'mcq_count' => $countMap[$t->id] ?? 0,
            ];
        }, $topics));
    }

    public function actionGetDifficultyCounts()
    {
        $subjectIds = Yii::$app->request->post('subject_ids', []);
        $chapterIds = Yii::$app->request->post('chapter_ids', []);
        $topicIds = Yii::$app->request->post('topic_ids', []);

        $query = (new \yii\db\Query())
            ->select(['difficulty_level', 'COUNT(*) as cnt'])
            ->from('mcqs m')
            ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id');

        if (!empty($subjectIds)) {
            $query->andWhere(['h.subject_id' => $subjectIds]);
        }
        if (!empty($chapterIds)) {
            $query->andWhere(['h.chapter_id' => $chapterIds]);
        }
        if (!empty($topicIds) && !(count($topicIds) === 1 && $topicIds[0] === '0')) {
            $query->andWhere(['h.topic_id' => $topicIds]);
        }

        $rows = $query->groupBy('difficulty_level')->all();
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['difficulty_level']] = (int) $row['cnt'];
        }

        $counts['all'] = array_sum($counts);

        return $this->asJson($counts);
    }

}
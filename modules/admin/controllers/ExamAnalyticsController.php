<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\ExamSessions;
use app\models\Users;
use app\models\UserMcqInteractions;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class ExamAnalyticsController extends Controller
{
    public function actionIndex($from = null, $to = null, $user_id = null, $exam_type = null, $mode = null)
    {
        $query = ExamSessions::find()->with(['user', 'specialty', 'examType']);

        // Apply filters
        if ($from) {
            $query->andWhere(['>=', 'start_time', $from]);
        }

        if ($to) {
            $query->andWhere(['<=', 'start_time', $to]);
        }

        if ($user_id) {
            $query->andWhere(['user_id' => $user_id]);
        }

        if ($exam_type) {
            $query->andWhere(['exam_type' => $exam_type]);
        }

        if ($mode) {
            $query->andWhere(['mode' => $mode]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['start_time' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filters' => [
                'from' => $from,
                'to' => $to,
                'user_id' => $user_id,
                'exam_type' => $exam_type,
                'mode' => $mode,
            ],
            'users' => ArrayHelper::map(Users::find()->all(), 'id', 'name'),
        ]);
    }

    public function actionSession($id)
    {
        $session = ExamSessions::find()->with(['user', 'userMcqInteractions.mcq'])->where(['id' => $id])->one();
        if (!$session) {
            throw new \yii\web\NotFoundHttpException('Session not found.');
        }

        return $this->render('session', [
            'session' => $session,
            'interactions' => $session->userMcqInteractions,
        ]);
    }

    public function actionAggregate()
    {
        $metrics = ExamSessions::find()
            ->select([
                'total_sessions' => new Expression('COUNT(*)'),
                'avg_accuracy' => new Expression('AVG(accuracy)'),
                'avg_time_per_question' => new Expression('AVG(time_spent_seconds / NULLIF(total_questions, 0))'),
                'total_breaches' => new Expression('SUM(breaches)'),
            ])
            ->asArray()
            ->one();

        $topTopics = (new \yii\db\Query())
            ->select([
                'topics.name AS topic_name',
                'mcqs.topic_id',
                'avg_accuracy' => new Expression('AVG(user_mcq_interactions.is_correct)'),
                'count' => new Expression('COUNT(*)'),
            ])
            ->from('user_mcq_interactions')
            ->innerJoin('mcqs', 'mcqs.id = user_mcq_interactions.mcq_id')
            ->innerJoin('topics', 'topics.id = mcqs.topic_id')
            ->groupBy('mcqs.topic_id')
            ->orderBy(['avg_accuracy' => SORT_ASC])
            ->limit(5)
            ->all();


        return $this->render('aggregate', [
            'metrics' => $metrics,
            'topTopics' => $topTopics,
        ]);
    }
}

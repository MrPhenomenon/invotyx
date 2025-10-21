<?php
namespace app\modules\user\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\ExamSessions;
use app\models\UserMcqInteractions;
use app\models\Mcqs;

class ResultsController extends Controller
{
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        $sessions = ExamSessions::find()
            ->where(['user_id' => $userId])
            ->orderBy(['end_time' => SORT_DESC])
            ->all();

        foreach ($sessions as $session) {
            if ($session->status === 'InProgress') {
                $cacheKey = 'exam_' . $userId . '_' . $session->id;
                $cacheData = Yii::$app->cache->get($cacheKey);

                $isExpired = !$cacheData || (
                    isset($cacheData['start_time'], $cacheData['time_limit']) &&
                    (time() - $cacheData['start_time']) > $cacheData['time_limit'] * 60
                );

                if ($isExpired) {
                    $session->status = 'Terminated';
                    $session->end_time = date('Y-m-d H:i:s');
                    $session->updated_at = date('Y-m-d H:i:s');
                    $session->save(false);
                    Yii::$app->cache->delete($cacheKey);
                }
            }
        }

        return $this->render('index', [
            'sessions' => $sessions,
        ]);
    }


    public function actionView($id)
    {
        $session = ExamSessions::find()
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->one();

        if (!$session) {
            throw new NotFoundHttpException('The requested exam session does not exist.');
        }

        $interactions = $session->getUserMcqInteractions()
            ->with('mcq')
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('view', [
            'session' => $session,
            'interactions' => $interactions,
        ]);
    }

}

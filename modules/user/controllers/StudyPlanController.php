<?php
namespace app\modules\user\controllers;

use app\models\StudyPlans;
use app\services\StudyPlanGenerator;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Users;
use app\models\StudyPlanDays;

class StudyPlanController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $today = date('Y-m-d');

        $query = StudyPlanDays::find()
            ->joinWith(['studyPlan'])
            ->where(['study_plans.user_id' => $user->id])
            ->orderBy(['plan_date' => SORT_ASC]);

        $studyPlan = StudyPlans::findOne(['user_id' => $user->id]);
        $planStartDate = $studyPlan ? $studyPlan->start_date : $today;

        $pageSize = 7;

        $currentDayNumber = (int) ((strtotime($today) - strtotime($planStartDate)) / 86400) + 1;

        $calculatedTodayPage = max(0, ceil($currentDayNumber / $pageSize) - 1);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'today' => $today,
            'planStartDate' => $planStartDate,
            'currentDayNumber' => $currentDayNumber,
            'calculatedTodayPage' => $calculatedTodayPage,
        ]);
    }

}

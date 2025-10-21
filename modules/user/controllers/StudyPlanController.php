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

        // try {
        //     StudyPlanGenerator::generateFullStudyPlan($user); // Or ensurePlan()
        // } catch (\Exception $e) {
        //     Yii::$app->session->setFlash('error', 'Error generating study plan: ' . $e->getMessage());
        // }

        $query = StudyPlanDays::find()
            ->joinWith(['studyPlan'])
            ->where(['study_plans.user_id' => $user->id])
            ->orderBy(['plan_date' => SORT_ASC]);

        $studyPlan = StudyPlans::findOne(['user_id' => $user->id]);
        $planStartDate = $studyPlan ? $studyPlan->start_date : $today; // Fallback if no plan yet

        $pageSize = 7; // Number of items per page

        // Calculate today's day number in the plan
        $currentDayNumber = (int) ((strtotime($today) - strtotime($planStartDate)) / 86400) + 1;

        // Calculate which page 'today' falls on (0-indexed)
        $calculatedTodayPage = max(0, ceil($currentDayNumber / $pageSize) - 1);

        // ActiveDataProvider will default to page 0 if no 'page' parameter in URL.
        // If a 'page' parameter exists, it will honor it.
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
                // Removed: 'page' => $requestedPage,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'today' => $today,
            'planStartDate' => $planStartDate,
            'currentDayNumber' => $currentDayNumber,
            'calculatedTodayPage' => $calculatedTodayPage, // For the "Go to Today" button
        ]);
    }

}

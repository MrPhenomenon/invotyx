<?php
namespace app\commands;

use yii\console\Controller;
use app\models\Users;

class StudyPlanController extends Controller
{
    public function actionRefresh()
    {
        foreach (Users::find()->where(['status' => 'active'])->each(50) as $user) {
            try {
                \app\services\StudyPlanGenerator::ensureWeeklyPlan($user);
                echo "Processed user {$user->id}\n"; // optional
            } catch (\Throwable $e) {
                \Yii::error("Plan refresh failed for user {$user->id}: " . $e->getMessage());
            }
        }

        echo "Study plan refresh completed.\n";
    }
}

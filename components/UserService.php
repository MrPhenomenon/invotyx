<?php
namespace app\components;

use app\models\StudyPlanDays;
use Yii;
use yii\helpers\Url;
use DateTime;
use app\models\Users;
use app\models\UserSubscriptions;

class UserService
{
    public static function loginUser(Users $user)
    {
        Yii::$app->user->login($user, 3600 * 12);

        $token = Yii::$app->security->generateRandomString(64);
        $user->session_token = $token;
        $user->save(false);
        Yii::$app->session->set('auth.session_token', $token);

        $today = date('Y-m-d');
        $subscription = UserSubscriptions::find()
            ->where(['user_id' => $user->id, 'is_active' => 1])
            ->andWhere(['>', 'end_date', (new DateTime())->format('Y-m-d')])
            ->orderBy(['start_date' => SORT_DESC])
            ->one();

        $plan = StudyPlanDays::find()
            ->leftJoin('study_plans', 'study_plans.id = study_plan_days.study_plan_id')
            ->where(['study_plans.user_id' => $user->id])
            ->andWhere(['study_plan_days.plan_date' => $today])
            ->one();

        if ($plan && $plan->status == 'upcoming') {
            $plan->status = 'pending';
            $plan->save(false);
        }

        if ($subscription) {
            Yii::$app->session->set('user.subscription_name', $subscription->subscription->name);
            Yii::$app->session->set('user.subscription_end_date', $subscription->end_date);
            Yii::$app->session->set('user.has_active_subscription', true);
        } else {
            Yii::$app->session->set('user.has_active_subscription', false);
        }

        return Url::to(['/user/default/index']);
    }
}

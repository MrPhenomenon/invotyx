<?php

namespace app\modules\admin\controllers;

use app\models\ExamSessions;
use app\models\ManagementTeam;
use app\models\Users;
use Yii;
use yii\web\Response;
use yii\db\Query;
/**
 * Default controller for the `admin` module
 */
class DefaultController extends AdminBaseController
{
    protected function allowedRoles(): array
    {
        return ['Super Admin'];
    }

    public function actionDashboard()
    {
        $userCount = Users::find()->count();
        $proUserCount = Users::find()
            ->joinWith('userSubscriptions')
            ->where(['user_subscriptions.is_active' => 1])
            ->distinct()
            ->count();
        $examsTaken = ExamSessions::find()
            ->where(['status' => 'Completed'])
            ->count();
        $today = date('Y-m-d');
        $examsToday = ExamSessions::find()
            ->where(['status' => 'Completed'])
            ->andWhere(['>=', 'end_time', $today . ' 00:00:00'])
            ->andWhere(['<=', 'end_time', $today . ' 23:59:59'])
            ->count();

        $recentUsers = Users::find()
            ->alias('u')
            ->select([
                'u.*',
                'subscription_name' => 's.name'
            ])
            ->leftJoin('user_subscriptions us', 'us.user_id = u.id AND us.is_active = 1')
            ->leftJoin('subscriptions s', 's.id = us.subscription_id')
            ->with([
                'examType',
                'speciality',
            ])
            ->orderBy(['u.created_at' => SORT_DESC])
            ->limit(15)
            ->asArray()
            ->all();

        $startDate = date('Y-m-d', strtotime('-6 days'));
        $totalUsers = [];
        $basicUsers = [];
        $proUsers = [];
        $mockOnlyUsers = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));

            $total = (new Query())
                ->from('users')
                ->where(['DATE(created_at)' => $date])
                ->count();
            $totalUsers[] = (int) $total;

            // For each subscription type, count users who registered on this day and have an active subscription of that type
            // 1 = Basic, 2 = Pro, 3 = Mock Only
            $basic = (new Query())
                ->from('user_subscriptions us')
                ->innerJoin('users u', 'u.id = us.user_id')
                ->where(['DATE(u.created_at)' => $date])
                ->andWhere(['us.subscription_id' => 1])
                ->andWhere(['<=', 'us.start_date', $date])
                ->andWhere(['>=', 'us.end_date', $date])
                ->count();
            $basicUsers[] = (int) $basic;

            $pro = (new Query())
                ->from('user_subscriptions us')
                ->innerJoin('users u', 'u.id = us.user_id')
                ->where(['DATE(u.created_at)' => $date])
                ->andWhere(['us.subscription_id' => 2])
                ->andWhere(['<=', 'us.start_date', $date])
                ->andWhere(['>=', 'us.end_date', $date])
                ->count();
            $proUsers[] = (int) $pro;

            $mock = (new Query())
                ->from('user_subscriptions us')
                ->innerJoin('users u', 'u.id = us.user_id')
                ->where(['DATE(u.created_at)' => $date])
                ->andWhere(['us.subscription_id' => 3])
                ->andWhere(['<=', 'us.start_date', $date])
                ->andWhere(['>=', 'us.end_date', $date])
                ->count();
            $mockOnlyUsers[] = (int) $mock;
        }

        return $this->render(
            'index',
            [
                'userCount' => $userCount,
                'proUsers' => $proUserCount,
                'examTaken' => $examsTaken,
                'examToday' => $examsToday,
                'recentUsers' => $recentUsers,
                'totalUsers' => $totalUsers,
                'basicUsers' => $basicUsers,
                'proUser' => $proUsers,
                'mockOnlyUsers' => $mockOnlyUsers,
            ]
        );
    }

    public function actionTeamManagement()
    {
        $data = ManagementTeam::find()
            ->asArray()
            ->all();

        return $this->render('management', [
            'members' => $data,
        ]);
    }

    public function actionAddManagement()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $model = new ManagementTeam();

        if ($model->load($data, '') && $model->validate()) {
            $model->created_at = date('Y-m-d H:i:s');

            unset($model->password);
            $model->password = Yii::$app->security->generatePasswordHash($data['password']);

            $model->auth_key = Yii::$app->security->generateRandomString();

            if ($model->save()) {
                return ['response' => 'success', 'message' => 'Team member added successfully.'];
            }

            return ['response' => 'error', 'message' => 'Failed to save.'];
        }

        return ['response' => 'error', 'message' => 'Invalid input.', 'errors' => $model->getErrors()];
    }

    public function actionDeleteUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $model = ManagementTeam::findOne($id);

        if (!$model) {
            return ['success' => false, 'message' => 'Entry Not Found'];
        }

        return $model->delete()
            ? ['success' => true]
            : ['success' => false, 'message' => 'There was an error deleting this entry'];

    }
}

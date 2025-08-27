<?php

namespace app\modules\user\controllers;

use app\models\ExamSessions;
use app\models\ExamSpecialties;
use app\models\UserMcqInteractions;
use app\models\Users;
use app\models\UserSubscriptions;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;
/**
 * Default controller for the `user` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $user = Users::findOne($userId);

        if (!$user) {
            Yii::$app->session->setFlash('error', 'User not found.');
            return $this->goHome(); // Or redirect to login
        }

        // 1. Subscription Status
        $subscription = UserSubscriptions::find()
            ->where(['user_id' => $userId])
            // ->andWhere(['>', 'end_date', date('Y-m-d H:i:s')])
            ->orderBy(['end_date' => SORT_DESC]) // Get the latest active
            ->joinWith('subscription') // Load subscription details
            ->one();
            

        // 2. Ongoing Exams
        $ongoingExams = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'InProgress'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // 3. Recent Completed Exams
        $recentExams = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'Completed'])
            ->orderBy(['end_time' => SORT_DESC])
            ->limit(5)
            ->all();

        // 4. Overall Performance Metrics
        $overallStats = [];
        $totalInteractions = UserMcqInteractions::find()
            ->where(['user_id' => $userId])
            ->count();

        $correctInteractions = UserMcqInteractions::find()
            ->where(['user_id' => $userId, 'is_correct' => 1])
            ->count();

        $answeredInteractions = UserMcqInteractions::find()
            ->where(['user_id' => $userId])
            ->andWhere(['is not', 'selected_option', null])
            ->count();

        $completedExamsCount = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'Completed'])
            ->count();

        $totalAccuracySum = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'Completed'])
            ->average('accuracy');

        $overallStats['totalAttempted'] = $totalInteractions;
        $overallStats['correctlyAnswered'] = $correctInteractions;
        $overallStats['incorrectlyAnswered'] = $answeredInteractions - $correctInteractions;
        $overallStats['skippedUnanswered'] = $totalInteractions - $answeredInteractions;
        $overallStats['examsCompleted'] = $completedExamsCount;
        $overallStats['overallAccuracy'] = $totalAccuracySum !== null ? round($totalAccuracySum, 2) : 0;

        return $this->render('index', [
            'user' => $user,
            'subscription' => $subscription,
            'ongoingExams' => $ongoingExams,
            'recentExams' => $recentExams,
            'overallStats' => $overallStats,
        ]);
    }


    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        $changePasswordForm = new \app\models\ChangePasswordForm();

        $activeSubscription = $user->userSubscriptions
            ? \yii\helpers\ArrayHelper::getValue(array_filter($user->userSubscriptions, fn($sub) => $sub->is_active), 0)
            : null;

        return $this->render('profile', [
            'user' => $user,
            'changePasswordForm' => $changePasswordForm,
            'activeSubscription' => $activeSubscription,
        ]);
    }

    public function actionUpdateProfile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            return ['success' => true, 'message' => 'Profile updated successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to update profile.', 'errors' => $user->errors];
    }

    public function actionChangePassword()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $form = new \app\models\ChangePasswordForm();
        $user = Yii::$app->user->identity;

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            if (Yii::$app->security->validatePassword($form->current_password, $user->password)) {
                $user->password = Yii::$app->security->generatePasswordHash($form->new_password);
                if ($user->save()) {
                    return ['success' => true, 'message' => 'Password changed successfully.'];
                }
                return ['success' => false, 'message' => 'Failed to save new password.'];
            }

            return ['success' => false, 'message' => 'Incorrect current password.'];
        }

        return ['success' => false, 'message' => 'Validation failed.', 'errors' => $form->errors];
    }
    public function actionSpecialties($exam_type)
    {
        $specialties = ExamSpecialties::find()
            ->where(['exam_type' => $exam_type])
            ->orderBy('name')
            ->all();

        $options = "<option value=''>Select Specialty</option>";

        foreach ($specialties as $specialty) {
            $options .= "<option value='{$specialty->id}'>" . Html::encode($specialty->name) . "</option>";
        }

        return $options;
    }

}

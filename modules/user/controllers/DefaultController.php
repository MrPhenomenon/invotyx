<?php

namespace app\modules\user\controllers;

use app\models\ExamSessions;
use app\models\ExamSpecialties;
use app\models\Hierarchy;
use app\models\Mcqs;
use app\models\OrganSystems;
use app\models\Reports;
use app\models\StudyPlanDays;
use app\models\StudyPlans;
use app\models\Topics;
use app\models\UserMcqInteractions;
use app\models\Users;
use app\models\UserSubscriptions;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
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
        $today = date('Y-m-d');
        $studyPlanDayToday = null;
        $currentExamSession = null;

        if (!$user) {
            Yii::$app->session->setFlash('error', 'User not found.');
            return $this->goHome();
        }

        if ($user->seen_dashboard_tutorial == 0 && Yii::$app->session->get('user.subscription_name') === 'Basic (Free Early Access)') {
            Yii::$app->session->setFlash('show_notice', true);
        }

        if ($user->seen_dashboard_tutorial == 0) {
            Yii::$app->session->setFlash('ask_tutorial', true);
            $user->seen_dashboard_tutorial = 1;
            $user->save(false);
        }

        $accuracyTrend = ExamSessions::find()
            ->select(['end_time', 'accuracy'])
            ->where(['user_id' => $userId, 'status' => 'Completed'])
            ->andWhere(['not', ['accuracy' => null]])
            ->orderBy(['end_time' => SORT_ASC])
            ->asArray()
            ->all();

        $studyPlan = StudyPlans::find()->select(['id'])->where(['user_id' => $user->id]);
        $studyPlanDayToday = StudyPlanDays::find()
            ->where(['study_plan_id' => $studyPlan, 'plan_date' => $today])
            ->with(['studyPlanDaySubjects.subject', 'studyPlanDaySubjects.chapter', 'studyPlanDaySubjects.topic'])
            ->one();

        if ($studyPlanDayToday) {
            $currentExamSession = ExamSessions::find()
                ->where([
                    'study_plan_day_id' => $studyPlanDayToday->id,
                    'status' => ExamSessions::STATUS_INPROGRESS
                ])
                ->one();
        }

        $ongoingExams = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'InProgress'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(5)
            ->all();

        $recentExams = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => 'Completed'])
            ->orderBy(['end_time' => SORT_DESC])
            ->limit(5)
            ->all();

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

        $overallStats['totalAttempted'] = $totalInteractions;
        $overallStats['correctlyAnswered'] = $correctInteractions;
        $overallStats['incorrectlyAnswered'] = $answeredInteractions - $correctInteractions;
        $overallStats['skippedUnanswered'] = $totalInteractions - $answeredInteractions;


        return $this->render('index', [
            'user' => $user,
            'ongoingExams' => $ongoingExams,
            'recentExams' => $recentExams,
            'overallStats' => $overallStats,
            'accuracyTrend' => $accuracyTrend,
            'studyPlanDayToday' => $studyPlanDayToday,
            'currentExamSession' => $currentExamSession,
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

    public function actionAnalytics()
    {
        $userId = Yii::$app->user->id;

        $totalExams = (int) ExamSessions::find()->where(['user_id' => $userId])->count();
        $completedExams = (int) ExamSessions::find()->where(['user_id' => $userId, 'status' => 'complete'])->count();
        $totalQuestions = (int) UserMcqInteractions::find()->where(['user_id' => $userId])->count();
        $totalTimeSpent = (int) ExamSessions::find()->where(['user_id' => $userId])->sum('time_spent_seconds');

        if ($totalQuestions > 0) {
            $correctCount = (int) UserMcqInteractions::find()
                ->where(['user_id' => $userId, 'is_correct' => 1])
                ->count();
            $accuracy = round(($correctCount / $totalQuestions) * 100, 2);
            $avgTimePerMcq = $totalTimeSpent > 0 ? $totalTimeSpent / $totalQuestions : 0;

            $seconds = (int) round($avgTimePerMcq);
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            $formattedTime = "{$minutes}m {$remainingSeconds}s";
        } else {
            $accuracy = null;
            $formattedTime = "-";
        }

        $chapterStats = [];
        if ($totalQuestions > 0) {
            $chapterStats = (new \yii\db\Query())
                ->select([
                    'c.id as chapter_id',
                    'c.name as chapter_name',
                    'COUNT(umi.id) as total_attempts',
                    'SUM(umi.is_correct) as correct_count',
                    'ROUND(SUM(umi.is_correct)/COUNT(umi.id)*100, 2) as accuracy',
                    'AVG(umi.time_spent_seconds) as avg_time',
                ])
                ->from('user_mcq_interactions umi')
                ->innerJoin('mcqs m', 'm.id = umi.mcq_id')
                ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id')
                ->innerJoin('chapters c', 'c.id = h.chapter_id')
                ->where(['umi.user_id' => $userId])
                ->groupBy(['c.id'])
                ->all();

            foreach ($chapterStats as &$stat) {
                $acc = (float) $stat['accuracy'];
                if ($acc >= 75) {
                    $stat['strength'] = 'Good';
                } elseif ($acc >= 60) {
                    $stat['strength'] = 'Need Preparation';
                } else {
                    $stat['strength'] = 'Weak';
                }
            }
        }

        return $this->render('analytics', [
            'totalExams' => $totalExams,
            'completedExams' => $completedExams,
            'totalQuestions' => $totalQuestions,
            'accuracy' => $accuracy,
            'avgTimePerMcq' => $formattedTime,
            'chapterStats' => $chapterStats,
        ]);
    }


    public function actionTopicsByChapter($chapterId)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $userId = Yii::$app->user->id;

        $topics = (new \yii\db\Query())
            ->select([
                't.id as topic_id',
                't.name as topic_name',
                'ROUND(SUM(umi.is_correct)/COUNT(umi.id)*100, 2) as accuracy'
            ])
            ->from('user_mcq_interactions umi')
            ->innerJoin('mcqs m', 'm.id = umi.mcq_id')
            ->innerJoin('hierarchy h', 'h.id = m.hierarchy_id')
            ->innerJoin('topics t', 't.id = h.topic_id')
            ->where(['umi.user_id' => $userId, 'h.chapter_id' => $chapterId])
            ->groupBy(['t.id'])
            ->all();

        return [
            'labels' => array_column($topics, 'topic_name'),
            'accuracy' => array_column($topics, 'accuracy'),
            'colors' => array_map(function ($t) {
                if ($t['accuracy'] >= 75)
                    return '#28a745';
                if ($t['accuracy'] >= 60)
                    return '#fd7e14';
                return '#dc3545';
            }, $topics),
        ];
    }


    public function actionReportMcq()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'error' => 'Invalid request'];
        }

        $mcqId = $request->post('mcq_id');
        $message = trim($request->post('message'));

        if (empty($mcqId) || empty($message)) {
            return ['success' => false, 'error' => 'MCQ ID and message are required'];
        }

        $exists = Reports::find()
            ->where(['mcq_id' => $mcqId, 'status' => 'pending'])
            ->exists();

        if ($exists) {
            return ['success' => false, 'error' => 'This MCQ is already reported and pending review.'];
        }

        $report = new Reports();
        $report->mcq_id = $mcqId;
        $report->reported_by = Yii::$app->user->id;
        $report->message = $message;

        if ($report->save()) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => $report->getErrors(),
        ];
    }

}

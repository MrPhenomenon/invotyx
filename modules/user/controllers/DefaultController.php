<?php

namespace app\modules\user\controllers;

use app\models\ExamSpecialties;
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
        return $this->render('index');
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

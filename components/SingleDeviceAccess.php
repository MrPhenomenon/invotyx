<?php
namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;

class SingleDeviceAccess extends Behavior
{
    public function events()
    {
        return [
            \yii\base\Controller::EVENT_BEFORE_ACTION => 'validateSessionToken',
        ];
    }

    public function validateSessionToken()
    {
        if (Yii::$app->user->isGuest)
            return true;

        $user = Yii::$app->user->identity;
        $stored = $user->session_token;
        $current = Yii::$app->session->get('auth.session_token');

        if (!$stored || !$current || $stored !== $current) {
            Yii::$app->user->logout(false);
            Yii::$app->session->destroy();
            Yii::$app->response->redirect(['/login'])->send();
            return false;
        }
        return true;
    }
}

<?php

namespace app\modules\user;
use Yii;

/**
 * user module definition class
 */
class Module extends \yii\base\Module
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (Yii::$app->user->isGuest && Yii::$app->controller->id !== 'site') {
                Yii::$app->response->redirect(['site/login'])->send();
                return false;
            }
            return true;
        }
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\user\controllers';
    public $layout = '@app/modules/user/views/layouts/main';
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }
}

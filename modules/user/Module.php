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
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Allow public access to certain controllers
        $publicControllers = ['orthopedic-exam'];

        $controller = Yii::$app->controller->id;

        if (in_array($controller, $publicControllers)) {
            return true;
        }

        if (Yii::$app->user->isGuest && $controller !== 'site') {
            Yii::$app->response->redirect(['site/login'])->send();
            return false;
        }

        return true;
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

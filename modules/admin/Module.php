<?php

namespace app\modules\admin;
use Yii;
/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (Yii::$app->admin->isGuest && Yii::$app->controller->id !== 'site') {
                throw new \yii\web\NotFoundHttpException('The page you are looking for does not exist.');
            }
            return true;
        }
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $layout = '@app/modules/admin/views/layouts/main';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}

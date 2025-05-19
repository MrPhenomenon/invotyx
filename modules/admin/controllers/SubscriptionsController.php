<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;

/**
 * Subscription controller for the `admin` module
 */
class SubscriptionController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    
}

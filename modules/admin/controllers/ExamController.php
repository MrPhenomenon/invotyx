<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;

/**
 * Exam controller for the `admin` module
 */
class ExamController extends Controller
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

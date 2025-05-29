<?php

namespace app\modules\admin\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use Yii;
use yii\db\Expression;
use yii\web\Response;
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
        $exams = ExamType::find()
        ->select([
            'exam_type.*',
            new Expression ('(SELECT COUNT(*) FROM exam_specialties WHERE exam_specialties.exam_type = exam_type.id) AS specialties_count')
        ])
        ->asArray()
        ->all();
        $specializations = ExamSpecialties::find()->with('examType')->asArray()->all();
        Yii::debug($specializations);
        return $this->render('index',[
            'exams' => $exams,
            'specializations' => $specializations
        ]);
    }

    public function actionSpecialization()
    {
        return $this->render('specialization');
    }

    public function actionDistribution()
    {
        return $this->render('distribution');
    }
    
    public function actionAddExam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');
        $model = new ExamType();
        $model->name = $name;
        return $model->save()
        ? ['success' => true]
        : ['success' => false];
    }

    public function actionAddSpecialization()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $model = new ExamSpecialties();
        $model->name = $data['name'];
        $model->exam_type = $data['examId'];
        return $model->save()
        ? ['success' => true]
        : ['success' => false];
    }
}

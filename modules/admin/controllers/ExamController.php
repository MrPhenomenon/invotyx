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
class ExamController extends AdminBaseController
{
    protected function allowedRoles(): array
    {
        return ['Super Admin', 'Content Manager'];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $exams = ExamType::find()
            ->select([
                'exam_type.*',
                new Expression('(SELECT COUNT(*) FROM exam_specialties WHERE exam_specialties.exam_type = exam_type.id) AS specialties_count')
            ])
            ->asArray()
            ->all();
        $specializations = ExamSpecialties::find()->with('examType')->asArray()->all();
        Yii::debug($specializations);
        return $this->render('index', [
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
        $model->exam_type = $data['exam_id'];
        return $model->save()
            ? ['success' => true]
            : ['success' => false];
    }

    public function actionUpdateExam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');

        if (!$id || !$name) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        $model = ExamType::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Exam not found'];
        }

        $model->name = $name;
        if ($model->save()) {
            return ['success' => true, 'message' => 'Exam updated'];
        } else {
            return ['success' => false, 'message' => 'Update failed', 'errors' => $model->getErrors()];
        }
    }

    public function actionUpdateSpecialization()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $name = Yii::$app->request->post('name');
        $examId = Yii::$app->request->post('exam_id');

        if (!$id || !$name || !$examId) {
            return ['success' => false, 'message' => 'Missing required fields'];
        }

        $model = ExamSpecialties::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Specialization not found'];
        }

        $model->name = $name;
        $model->exam_type = $examId;
        if ($model->save()) {
            return ['success' => true, 'message' => 'Specialization updated'];
        } else {
            return ['success' => false, 'message' => 'Update failed', 'errors' => $model->getErrors()];
        }
    }

    public function actionDeleteExam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing exam ID'];
        }
        $model = ExamType::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Exam not found'];
        }
        if ($model->delete()) {
            return ['success' => true, 'message' => 'Exam deleted'];
        } else {
            return ['success' => false, 'message' => 'Delete failed'];
        }
    }

    public function actionDeleteSpecialization()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing specialization ID'];
        }
        $model = ExamSpecialties::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Specialization not found'];
        }
        if ($model->delete()) {
            return ['success' => true, 'message' => 'Specialization deleted'];
        } else {
            return ['success' => false, 'message' => 'Delete failed'];
        }
    }
}

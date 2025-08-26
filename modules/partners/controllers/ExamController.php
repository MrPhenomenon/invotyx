<?php
namespace app\modules\partners\controllers;
use app\models\PartnerExamAccess;
use app\models\PartnerExams;
use app\models\PartnerMcqs;
use app\modules\partners\components\PartnerBaseController;
use app\models\ExternalPartners;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ExamController extends PartnerBaseController
{
    public $partner;

    public function actionIndex()
    {
        $exams = PartnerExams::find()
            ->where(['external_partner_id' => $this->partner->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'exams' => $exams,
        ]);
    }

    public function actionCreate()
    {
        $model = new PartnerExams();    
        $model->external_partner_id = $this->partner->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $accessList = Yii::$app->request->post('PartnerExamAccess', []);
            foreach ($accessList as $row) {
                if (!empty($row['email']) && !empty($row['passkey'])) {
                    $access = new PartnerExamAccess();
                    $access->partner_exam_id = $model->id;
                    $access->email = $row['email'];
                    $access->passkey = $row['passkey'];
                    $access->has_attempted = 0;
                    $access->save(false);
                }
            }

            Yii::$app->session->setFlash('success', 'Exam created successfully.');
            return $this->redirect([
                '/partners/exam/manage-mcqs',
                'access' => $this->partner->access_token,
                'exam_id' => $model->id
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'partner' => $this->partner,
        ]);
    }

    public function actionDeleteExam()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');

        $model = PartnerExams::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Exam not found.'];
        }

        // Optional: delete associated MCQs and their images
        foreach ($model->partnerMcqs as $mcq) {
            if ($mcq->image_url && file_exists(Yii::getAlias('@webroot/' . $mcq->image_url))) {
                @unlink(Yii::getAlias('@webroot/' . $mcq->image_url));
            }
            $mcq->delete();
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'Exam deleted successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to delete exam.'];
    }

    public function actionUpdateExam($id)
    {
        $exam = PartnerExams::findOne($id);
        if (!$exam) {
            throw new NotFoundHttpException("Exam not found");
        }
        $existingAccess = PartnerExamAccess::find()
            ->where(['partner_exam_id' => $exam->id])
            ->indexBy('email')
            ->all();

        if ($exam->load(Yii::$app->request->post()) && $exam->save()) {

            $submittedAccess = Yii::$app->request->post('PartnerExamAccess', []);
            $processedEmails = [];

            foreach ($submittedAccess as $row) {
                $email = trim($row['email'] ?? '');
                $passkey = trim($row['passkey'] ?? '');

                if ($email === '' || $passkey === '')
                    continue;

                $processedEmails[] = $email;

                if (isset($existingAccess[$email])) {
                    // Update existing
                    $existing = $existingAccess[$email];
                    $existing->passkey = $passkey;
                    $existing->save(false);
                } else {
                    $access = new PartnerExamAccess();
                    $access->partner_exam_id = $exam->id;
                    $access->email = $email;
                    $access->passkey = $passkey;
                    $access->has_attempted = 0;
                    $access->save(false);
                }
            }

            foreach ($existingAccess as $email => $access) {
                if (!in_array($email, $processedEmails)) {
                    $access->delete();
                }
            }

            Yii::$app->session->setFlash('examUpdated', 'Exam updated successfully!');
            return $this->redirect(['exam/index', 'access' => $this->partner->access_token]);
        }

        return $this->render('update-exam', [
            'model' => $exam,
            'accessList' => $existingAccess,
        ]);
    }


    public function actionManageMcqs($exam_id)
    {
        $exam = PartnerExams::findOne([
            'id' => $exam_id,
            'external_partner_id' => $this->partner->id
        ]);

        if (!$exam) {
            throw new \yii\web\NotFoundHttpException('Exam not found.');
        }

        $mcqs = PartnerMcqs::find()
            ->where(['partner_exam_id' => $exam_id])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render('manage-mcqs', [
            'exam' => $exam,
            'mcqs' => $mcqs
        ]);
    }

    public function actionAddMcqs($exam_id)
    {
        $exam = PartnerExams::findOne([
            'id' => $exam_id,
            'external_partner_id' => $this->partner->id
        ]);

        if (!$exam) {
            throw new NotFoundHttpException('Exam not found');
        }

        if (Yii::$app->request->isPost) {
            $mcqsData = Yii::$app->request->post('PartnerMcqs', []);

            foreach ($mcqsData as $index => $mcqData) {
                $mcq = new PartnerMcqs();
                $mcq->load(['PartnerMcqs' => $mcqData]);
                $mcq->partner_exam_id = $exam_id;
                $mcq->external_partner_id = $this->partner->id;

                $imageFile = UploadedFile::getInstanceByName("PartnerMcqs[$index][image_url]");
                if ($imageFile) {
                    $filename = 'mcq_' . time() . "_$index." . $imageFile->extension;
                    $path = Yii::getAlias('@webroot/uploads/partner_mcqs/') . $filename;
                    $imageFile->saveAs($path);
                    $mcq->image_url = '/uploads/partner_mcqs/' . $filename;
                }

                $mcq->save();
            }

            Yii::$app->session->setFlash('mcqAdded', 'MCQs added successfully.');
            return $this->redirect(['exam/manage-mcqs', 'exam_id' => $exam_id, 'access' => $this->partner->access_token]);
        }

        return $this->render('add-mcqs', [
            'exam' => $exam
        ]);
    }

    public function actionDeleteMcq()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $mcq = PartnerMcqs::findOne($id);

        if (!$mcq) {
            return ['success' => false, 'message' => 'MCQ not found.'];
        }

        if (!empty($mcq->image_url)) {
            $imagePath = Yii::getAlias('@webroot') . $mcq->image_url;
            if (file_exists($imagePath) && is_file($imagePath)) {
                @unlink($imagePath);
            }
        }

        if ($mcq->delete()) {
            return ['success' => true, 'message' => 'MCQ and image deleted successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to delete MCQ.'];
    }

    public function actionUpdateMcq($id)
    {
        $model = PartnerMcqs::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('The requested MCQ does not exist.');
        }

        $oldImageUrl = $model->image_url;
        if ($model->load(Yii::$app->request->post())) {
            $uploadedFile = UploadedFile::getInstance($model, 'image_url');

            if ($model->remove_image) {
                if ($oldImageUrl && file_exists(Yii::getAlias('@webroot') . $oldImageUrl)) {
                    unlink(Yii::getAlias('@webroot') . $oldImageUrl);
                }
                $model->image_url = null;
            } elseif ($uploadedFile) {
                $filename = uniqid('mcq_') . '.' . $uploadedFile->extension;
                $path = Yii::getAlias('@webroot/uploads/partner_mcqs/') . $filename;

                if ($uploadedFile->saveAs($path)) {
                    if ($oldImageUrl && file_exists(Yii::getAlias('@webroot') . $oldImageUrl)) {
                        unlink(Yii::getAlias('@webroot') . $oldImageUrl);
                    }
                    $model->image_url = '/uploads/partner_mcqs/' . $filename;
                }
            } else {
                $model->image_url = $oldImageUrl;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'MCQ has been updated successfully.');
                return $this->redirect(['exam/manage-mcqs', 'exam_id' => $model->partner_exam_id, 'access' => Yii::$app->request->get('access')]);
            }
        }

        return $this->render('update-mcq', [
            'model' => $model,
        ]);
    }

    public function actionExportAccessList($id)
{
    $exam = PartnerExams::findOne($id);
    if (!$exam) {
        throw new NotFoundHttpException("Exam not found");
    }

    $accessList = PartnerExamAccess::find()
        ->where(['partner_exam_id' => $exam->id])
        ->all();

    $filename = 'access_list_exam_' . $exam->id . '.csv';

    Yii::$app->response->headers->set('Content-Type', 'text/csv');
    Yii::$app->response->headers->set('Content-Disposition', "attachment; filename={$filename}");

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Email', 'Passkey']); // headers

    foreach ($accessList as $access) {
        fputcsv($output, [$access->email, $access->passkey]);
    }

    fclose($output);
    return Yii::$app->response->send();
}


}

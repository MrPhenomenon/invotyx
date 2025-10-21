<?php
namespace app\modules\admin\controllers;

use Yii;
use app\models\ExternalPartners;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ExternalPartnersController extends AdminBaseController
{
      protected function allowedRoles(): array
    {
        return ['Super Admin'];
    }
    public function actionCreate()
    {
        $model = new ExternalPartners();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('partner', [
                    'type' => 'success',
                    'message' => 'Partner added successfully!',
                ]);
            } else {
                Yii::$app->session->setFlash('partner', [
                    'type' => 'error',
                    'message' => 'Failed to save partner. Please check your input.',
                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionIndex()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => ExternalPartners::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => false, // show all partners
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = ExternalPartners::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Partner not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('toast', [
                'type' => 'success',
                'message' => 'Partner updated successfully!',
            ]);
            return $this->redirect(['external-partners/index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $model = ExternalPartners::findOne($id);

        if (!$model) {
            return ['success' => false, 'message' => 'Partner not found.'];
        }

        if ($model->getPartnerExams()->exists()) {
            return ['success' => false, 'message' => 'Cannot delete partner with existing exams.'];
        }

        $model->delete();

        return ['success' => true, 'message' => 'Partner deleted successfully.'];
    }


}

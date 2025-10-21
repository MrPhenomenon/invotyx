<?php

namespace app\modules\admin\controllers;

use app\models\ExamSessions;
use app\models\ManagementTeam;
use app\models\Reports;
use app\models\Users;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\db\Query;
/**
 * Default controller for the `admin` module
 */
class SupportController extends Controller
{
    protected function allowedRoles(): array
    {
        return ['Super Admin'];
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionMcqReports()
    {
        $reports = Reports::find()->with('user')->orderBy(['reported_at' => SORT_DESC])->all();

        return $this->render('mcq-reports', [
            'reports' => $reports,
        ]);
    }

    public function actionMarkAsSolved($id)
    {
        $report = Reports::findOne($id);

        if (!$report) {
            throw new NotFoundHttpException('The requested report does not exist.');
        }

        if ($report->status === Reports::STATUS_SOLVED) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Report is already solved.'];
            } else {
                Yii::$app->session->setFlash('info', 'Report is already marked as solved.');
                return $this->redirect(['mcq-reports']);
            }
        }

        $report->setStatusToSolved();

        if ($report->save()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => true, 'message' => 'Report marked as solved.'];
            } else {
                Yii::$app->session->setFlash('success', 'Report marked as solved.');
                return $this->redirect(['mcq-reports']);
            }
        } else {
            Yii::error('Failed to mark report ' . $report->id . ' as solved: ' . json_encode($report->getErrors()));
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Failed to mark report as solved: ' . current($report->getFirstErrors())];
            } else {
                Yii::$app->session->setFlash('error', 'Failed to mark report as solved: ' . current($report->getFirstErrors()));
                return $this->redirect(['mcq-reports']);
            }
        }
    }
}

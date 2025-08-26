<?php
namespace app\modules\partners\controllers;
use app\models\PartnerExamAnswers;
use app\models\PartnerExamAttempts;
use app\models\PartnerExams;
use app\models\PartnerMcqs;
use app\modules\partners\components\PartnerBaseController;
use app\models\ExternalPartners;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ResultController extends PartnerBaseController
{
    public $partner;

    public function actionIndex($exam_id)
    {

        $exam = PartnerExams::findOne($exam_id);

        if (!$exam) {
            throw new NotFoundHttpException('The requested exam does not exist.');
        }

        // Create a query for all COMPLETED attempts for this exam.
        $query = PartnerExamAttempts::find()
            ->where(['partner_exam_id' => $exam->id])
            ->andWhere(['is not', 'completed_at', null]);

        // --- Summary Statistics ---
        $totalAttempts = (clone $query)->count();
        $averageScore = ($totalAttempts > 0) ? (clone $query)->average('score') : 0;
        $highestScore = ($totalAttempts > 0) ? (clone $query)->max('score') : 0;



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['completed_at' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'exam' => $exam,
            'dataProvider' => $dataProvider,
            'totalAttempts' => $totalAttempts,
            'averageScore' => $averageScore,
            'highestScore' => $highestScore,
        ]);
    }
    /**
     * @param int $attempt_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPrintResult($attempt_id)
    {
        $this->layout = 'mcq';

        $examAttempt = PartnerExamAttempts::find()
            ->where(['id' => $attempt_id, 'partner_exam_id' => $this->partner->getPartnerExams()->select('id')->column()])
            ->one();

        if (!$examAttempt) {
            throw new NotFoundHttpException('The requested exam attempt does not exist or does not belong to your partner account.');
        }

        if (!$examAttempt->completed_at) {
            Yii::$app->session->setFlash('danger', 'The results for this exam are not yet available.');
            throw new NotFoundHttpException('Exam results are not yet available for print.');
        }

        $query = PartnerExamAnswers::find()
            ->where(['exam_attempt_id' => $examAttempt->id])
            ->with('partnerMcq')
            ->orderBy(['id' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $correctAnswersCount = PartnerExamAnswers::find()->where(['exam_attempt_id' => $examAttempt->id, 'is_correct' => 1])->count();
        $totalQuestionsAnswered = PartnerExamAnswers::find()->where(['exam_attempt_id' => $examAttempt->id])->count();
        $totalExamQuestions = $examAttempt->total_questions;

        return $this->render('print-result', [
            'attempt' => $examAttempt,
            'dataProvider' => $dataProvider,
            'correctAnswers' => $correctAnswersCount,
            'totalQuestionsAnswered' => $totalQuestionsAnswered,
            'totalExamQuestions' => $totalExamQuestions,
            'partner' => $this->partner,
        ]);
    }
}
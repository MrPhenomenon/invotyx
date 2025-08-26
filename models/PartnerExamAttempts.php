<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner_exam_attempts".
 *
 * @property int $id
 * @property int $partner_exam_id
 * @property string|null $user_name
 * @property string $user_email
 * @property string|null $user_hospital
 * @property string|null $started_at
 * @property string|null $completed_at
 * @property string $status
 * @property float|null $score
 * @property int|null $total_questions
 * @property int|null $correct_answers
 *
 * @property PartnerExams $partnerExam
 * @property PartnerExamAnswers[] $partnerExamAnswers
 */
class PartnerExamAttempts extends \yii\db\ActiveRecord
{
    public $passkey;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_exam_attempts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'user_hospital', 'started_at', 'completed_at', 'score', 'total_questions', 'correct_answers'], 'default', 'value' => null],
            [['partner_exam_id'], 'required'],
            [['partner_exam_id', 'total_questions', 'correct_answers'], 'integer'],
            [['started_at', 'completed_at'], 'safe'],
            [['user_email'], 'email'],
            [['user_email'], 'required'],
            [['user_name', 'user_hospital'], 'safe'],
            [['passkey'], 'required'],
            [['score'], 'number'],
            [['user_name', 'user_email', 'user_hospital', 'passkey'], 'string', 'max' => 255],
            [['partner_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerExams::class, 'targetAttribute' => ['partner_exam_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_exam_id' => 'Partner Exam ID',
            'user_name' => 'Name',
            'user_email' => 'Email',
            'user_hospital' => 'Hospital Name',
            'started_at' => 'Started At',
            'completed_at' => 'Completed At',
            'score' => 'Score',
            'total_questions' => 'Total Questions',
            'correct_answers' => 'Correct Answers',
        ];
    }

    /**
     * Gets query for [[PartnerExam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerExam()
    {
        return $this->hasOne(PartnerExams::class, ['id' => 'partner_exam_id']);
    }

    /**
     * Gets query for [[PartnerExamAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerExamAnswers()
    {
        return $this->hasMany(PartnerExamAnswers::class, ['exam_attempt_id' => 'id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner_exam_answers".
 *
 * @property int $id
 * @property int $exam_attempt_id
 * @property int $partner_mcq_id
 * @property string|null $selected_option
 * @property int|null $is_correct
 *
 * @property PartnerExamAttempts $examAttempt
 * @property PartnerMcqs $partnerMcq
 */
class PartnerExamAnswers extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const SELECTED_OPTION_A = 'a';
    const SELECTED_OPTION_B = 'b';
    const SELECTED_OPTION_C = 'c';
    const SELECTED_OPTION_D = 'd';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_exam_answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['selected_option', 'is_correct'], 'default', 'value' => null],
            [['exam_attempt_id', 'partner_mcq_id'], 'required'],
            [['exam_attempt_id', 'partner_mcq_id', 'is_correct'], 'integer'],
            [['selected_option'], 'string'],
            ['selected_option', 'in', 'range' => array_keys(self::optsSelectedOption())],
            [['exam_attempt_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerExamAttempts::class, 'targetAttribute' => ['exam_attempt_id' => 'id']],
            [['partner_mcq_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerMcqs::class, 'targetAttribute' => ['partner_mcq_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exam_attempt_id' => 'Exam Attempt ID',
            'partner_mcq_id' => 'Partner Mcq ID',
            'selected_option' => 'Selected Option',
            'is_correct' => 'Is Correct',
        ];
    }

    /**
     * Gets query for [[ExamAttempt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamAttempt()
    {
        return $this->hasOne(PartnerExamAttempts::class, ['id' => 'exam_attempt_id']);
    }

    /**
     * Gets query for [[PartnerMcq]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerMcq()
    {
        return $this->hasOne(PartnerMcqs::class, ['id' => 'partner_mcq_id']);
    }


    /**
     * column selected_option ENUM value labels
     * @return string[]
     */
    public static function optsSelectedOption()
    {
        return [
            self::SELECTED_OPTION_A => 'a',
            self::SELECTED_OPTION_B => 'b',
            self::SELECTED_OPTION_C => 'c',
            self::SELECTED_OPTION_D => 'd',
        ];
    }

    /**
     * @return string
     */
    public function displaySelectedOption()
    {
        return self::optsSelectedOption()[$this->selected_option];
    }

    /**
     * @return bool
     */
    public function isSelectedOptionA()
    {
        return $this->selected_option === self::SELECTED_OPTION_A;
    }

    public function setSelectedOptionToA()
    {
        $this->selected_option = self::SELECTED_OPTION_A;
    }

    /**
     * @return bool
     */
    public function isSelectedOptionB()
    {
        return $this->selected_option === self::SELECTED_OPTION_B;
    }

    public function setSelectedOptionToB()
    {
        $this->selected_option = self::SELECTED_OPTION_B;
    }

    /**
     * @return bool
     */
    public function isSelectedOptionC()
    {
        return $this->selected_option === self::SELECTED_OPTION_C;
    }

    public function setSelectedOptionToC()
    {
        $this->selected_option = self::SELECTED_OPTION_C;
    }

    /**
     * @return bool
     */
    public function isSelectedOptionD()
    {
        return $this->selected_option === self::SELECTED_OPTION_D;
    }

    public function setSelectedOptionToD()
    {
        $this->selected_option = self::SELECTED_OPTION_D;
    }
}

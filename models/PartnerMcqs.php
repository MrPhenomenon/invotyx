<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner_mcqs".
 *
 * @property int $id
 * @property int $external_partner_id
 * @property int $partner_exam_id
 * @property string $question
 * @property string|null $option_a
 * @property string|null $option_b
 * @property string|null $option_c
 * @property string|null $option_d
 * @property string|null $option_e
 * @property string $correct_option
 * @property string|null $explanation
 * @property string|null $reference
 * @property string|null $image_url
 * @property string|null $created_at
 *
 * @property ExternalPartners $externalPartner
 * @property PartnerExams $partnerExam
 * @property PartnerExamAnswers[] $partnerExamAnswers
 */
class PartnerMcqs extends \yii\db\ActiveRecord
{
    public $remove_image;
    /**
     * ENUM field values
     */
    const CORRECT_OPTION_A = 'a';
    const CORRECT_OPTION_B = 'b';
    const CORRECT_OPTION_C = 'c';
    const CORRECT_OPTION_D = 'd';
    const CORRECT_OPTION_E = 'e';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_mcqs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'explanation', 'reference', 'image_url'], 'default', 'value' => null],
            [['external_partner_id', 'partner_exam_id', 'question', 'correct_option'], 'required'],
            [['external_partner_id', 'partner_exam_id'], 'integer'],
            [['question', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_option', 'explanation', 'image_url'], 'string'],
            [['created_at'], 'safe'],
            ['correct_option', 'in', 'range' => array_keys(self::optsCorrectOption())],
            [['external_partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExternalPartners::class, 'targetAttribute' => ['external_partner_id' => 'id']],
            [['partner_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerExams::class, 'targetAttribute' => ['partner_exam_id' => 'id']],
            [['remove_image'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_partner_id' => 'External Partner ID',
            'partner_exam_id' => 'Partner Exam ID',
            'question' => 'Question',
            'option_a' => 'Option A',
            'option_b' => 'Option B',
            'option_c' => 'Option C',
            'option_d' => 'Option D',
            'option_e' => 'Option E',
            'correct_option' => 'Correct Option',
            'explanation' => 'Explanation',
            'reference' => 'Reference',
            'image_url' => 'Image Url',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ExternalPartner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalPartner()
    {
        return $this->hasOne(ExternalPartners::class, ['id' => 'external_partner_id']);
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
        return $this->hasMany(PartnerExamAnswers::class, ['partner_mcq_id' => 'id']);
    }


    /**
     * column correct_option ENUM value labels
     * @return string[]
     */
    public static function optsCorrectOption()
    {
        return [
            self::CORRECT_OPTION_A => 'a',
            self::CORRECT_OPTION_B => 'b',
            self::CORRECT_OPTION_C => 'c',
            self::CORRECT_OPTION_D => 'd',
            self::CORRECT_OPTION_E => 'e',
        ];
    }

    /**
     * @return string
     */
    public function displayCorrectOption()
    {
        return self::optsCorrectOption()[$this->correct_option];
    }

    /**
     * @return bool
     */
    public function isCorrectOptionA()
    {
        return $this->correct_option === self::CORRECT_OPTION_A;
    }

    public function setCorrectOptionToA()
    {
        $this->correct_option = self::CORRECT_OPTION_A;
    }

    /**
     * @return bool
     */
    public function isCorrectOptionB()
    {
        return $this->correct_option === self::CORRECT_OPTION_B;
    }

    public function setCorrectOptionToB()
    {
        $this->correct_option = self::CORRECT_OPTION_B;
    }

    /**
     * @return bool
     */
    public function isCorrectOptionC()
    {
        return $this->correct_option === self::CORRECT_OPTION_C;
    }

    public function setCorrectOptionToC()
    {
        $this->correct_option = self::CORRECT_OPTION_C;
    }

    /**
     * @return bool
     */
    public function isCorrectOptionD()
    {
        return $this->correct_option === self::CORRECT_OPTION_D;
    }

    public function setCorrectOptionToD()
    {
        $this->correct_option = self::CORRECT_OPTION_D;
    }
}

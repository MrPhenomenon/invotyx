<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_mcq_interactions".
 *
 * @property int $id
 * @property int $user_id
 * @property int $mcq_id
 * @property int $exam_session_id
 * @property string|null $selected_option
 * @property int|null $is_correct
 * @property int|null $flagged
 * @property string $attempted_at
 * @property int|null $time_spent_seconds
 *
 * @property ExamSessions $examSession
 * @property Mcqs $mcq
 * @property Users $user
 */
class UserMcqInteractions extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const SELECTED_OPTION_A = 'A';
    const SELECTED_OPTION_B = 'B';
    const SELECTED_OPTION_C = 'C';
    const SELECTED_OPTION_D = 'D';
    const SELECTED_OPTION_E = 'E';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_mcq_interactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['selected_option', 'is_correct', 'time_spent_seconds'], 'default', 'value' => null],
            [['flagged'], 'default', 'value' => 0],
            [['user_id', 'mcq_id', 'exam_session_id'], 'required'],
            [['user_id', 'mcq_id', 'exam_session_id', 'is_correct', 'flagged', 'time_spent_seconds'], 'integer'],
            [['selected_option'], 'string'],
            [['attempted_at'], 'safe'],
            ['selected_option', 'in', 'range' => array_keys(self::optsSelectedOption())],
            [['user_id', 'mcq_id', 'exam_session_id'], 'unique', 'targetAttribute' => ['user_id', 'mcq_id', 'exam_session_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['mcq_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mcqs::class, 'targetAttribute' => ['mcq_id' => 'id']],
            [['exam_session_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSessions::class, 'targetAttribute' => ['exam_session_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mcq_id' => 'Mcq ID',
            'exam_session_id' => 'Exam Session ID',
            'selected_option' => 'Selected Option',
            'is_correct' => 'Is Correct',
            'flagged' => 'Flagged',
            'attempted_at' => 'Attempted At',
            'time_spent_seconds' => 'Time Spent Seconds',
        ];
    }

    /**
     * Gets query for [[ExamSession]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamSession()
    {
        return $this->hasOne(ExamSessions::class, ['id' => 'exam_session_id']);
    }

    /**
     * Gets query for [[Mcq]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMcq()
    {
        return $this->hasOne(Mcqs::class, ['id' => 'mcq_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }


    /**
     * column selected_option ENUM value labels
     * @return string[]
     */
    public static function optsSelectedOption()
    {
        return [
            self::SELECTED_OPTION_A => 'A',
            self::SELECTED_OPTION_B => 'B',
            self::SELECTED_OPTION_C => 'C',
            self::SELECTED_OPTION_D => 'D',
            self::SELECTED_OPTION_E => 'E',
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

    /**
     * @return bool
     */
    public function isSelectedOptionE()
    {
        return $this->selected_option === self::SELECTED_OPTION_E;
    }

    public function setSelectedOptionToE()
    {
        $this->selected_option = self::SELECTED_OPTION_E;
    }
}

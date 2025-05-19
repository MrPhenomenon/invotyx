<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mcqs".
 *
 * @property int $id
 * @property string $question_text
 * @property string $option_a
 * @property string $option_b
 * @property string $option_c
 * @property string|null $option_d
 * @property string|null $option_e
 * @property string $correct_option
 * @property string|null $explanation
 * @property int $topic_id
 * @property string|null $reference
 * @property int $difficulty_level
 * @property string|null $image_path
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $createdBy
 * @property Topics $topic
 * @property UserMcqInteractions[] $userMcqInteractions
 */
class Mcqs extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const CORRECT_OPTION_A = 'A';
    const CORRECT_OPTION_B = 'B';
    const CORRECT_OPTION_C = 'C';
    const CORRECT_OPTION_D = 'D';
    const CORRECT_OPTION_E = 'E';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mcqs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['option_d', 'option_e', 'explanation', 'reference', 'image_path'], 'default', 'value' => null],
            [['difficulty_level'], 'default', 'value' => 1],
            [['id', 'question_text', 'option_a', 'option_b', 'option_c', 'correct_option', 'topic_id', 'created_by'], 'required'],
            [['id', 'topic_id', 'difficulty_level', 'created_by'], 'integer'],
            [['question_text', 'correct_option', 'explanation', 'reference'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'image_path'], 'string', 'max' => 255],
            ['correct_option', 'in', 'range' => array_keys(self::optsCorrectOption())],
            [['id'], 'unique'],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topics::class, 'targetAttribute' => ['topic_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_text' => 'Question Text',
            'option_a' => 'Option A',
            'option_b' => 'Option B',
            'option_c' => 'Option C',
            'option_d' => 'Option D',
            'option_e' => 'Option E',
            'correct_option' => 'Correct Option',
            'explanation' => 'Explanation',
            'topic_id' => 'Topic ID',
            'reference' => 'Reference',
            'difficulty_level' => 'Difficulty Level',
            'image_path' => 'Image Path',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Users::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[Topic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(Topics::class, ['id' => 'topic_id']);
    }

    /**
     * Gets query for [[UserMcqInteractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserMcqInteractions()
    {
        return $this->hasMany(UserMcqInteractions::class, ['mcq_id' => 'id']);
    }


    /**
     * column correct_option ENUM value labels
     * @return string[]
     */
    public static function optsCorrectOption()
    {
        return [
            self::CORRECT_OPTION_A => 'A',
            self::CORRECT_OPTION_B => 'B',
            self::CORRECT_OPTION_C => 'C',
            self::CORRECT_OPTION_D => 'D',
            self::CORRECT_OPTION_E => 'E',
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

    /**
     * @return bool
     */
    public function isCorrectOptionE()
    {
        return $this->correct_option === self::CORRECT_OPTION_E;
    }

    public function setCorrectOptionToE()
    {
        $this->correct_option = self::CORRECT_OPTION_E;
    }
}

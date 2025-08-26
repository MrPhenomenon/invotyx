<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mcqs".
 *
 * @property int $id
 * @property string|null $question_id
 * @property string $question_text
 * @property string $question_hash
 * @property string $option_a
 * @property string $option_b
 * @property string $option_c
 * @property string|null $option_d
 * @property string|null $option_e
 * @property string $correct_option
 * @property string|null $explanation
 * @property int $topic_id
 * @property int|null $organ_system_id Foreign Key to organ_systems table
 * @property int|null $subject_id Foreign Key to subjects table
 * @property string|null $reference
 * @property string|null $difficulty_level
 * @property string|null $image_path
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ManagementTeam $createdBy
 * @property OrganSystems $organSystem
 * @property Subjects $subject
 * @property Topics $topic
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
    const DIFFICULTY_LEVEL_EASY = 'Easy';
    const DIFFICULTY_LEVEL_MODERATE = 'Moderate';
    const DIFFICULTY_LEVEL_HARD = 'Hard';

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
            [['question_id', 'option_d', 'option_e', 'explanation', 'organ_system_id', 'subject_id', 'reference', 'difficulty_level', 'image_path'], 'default', 'value' => null],
            [['question_text', 'question_hash', 'option_a', 'option_b', 'option_c', 'correct_option', 'topic_id', 'created_by'], 'required'],
            [['question_text', 'correct_option', 'explanation', 'reference', 'difficulty_level'], 'string'],
            [['topic_id', 'organ_system_id', 'subject_id', 'created_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['question_id'], 'string', 'max' => 20],
            [['question_hash'], 'string', 'max' => 64],
            [['option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'image_path'], 'string', 'max' => 255],
            ['correct_option', 'in', 'range' => array_keys(self::optsCorrectOption())],
            ['difficulty_level', 'in', 'range' => array_keys(self::optsDifficultyLevel())],
            [['question_hash'], 'unique'],
            [['organ_system_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrganSystems::class, 'targetAttribute' => ['organ_system_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topics::class, 'targetAttribute' => ['topic_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => ManagementTeam::class, 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'question_text' => 'Question Text',
            'question_hash' => 'Question Hash',
            'option_a' => 'Option A',
            'option_b' => 'Option B',
            'option_c' => 'Option C',
            'option_d' => 'Option D',
            'option_e' => 'Option E',
            'correct_option' => 'Correct Option',
            'explanation' => 'Explanation',
            'topic_id' => 'Topic ID',
            'organ_system_id' => 'Organ System ID',
            'subject_id' => 'Subject ID',
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
        return $this->hasOne(ManagementTeam::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[OrganSystem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganSystem()
    {
        return $this->hasOne(OrganSystems::class, ['id' => 'organ_system_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subjects::class, ['id' => 'subject_id']);
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
     * column difficulty_level ENUM value labels
     * @return string[]
     */
    public static function optsDifficultyLevel()
    {
        return [
            self::DIFFICULTY_LEVEL_EASY => 'Easy',
            self::DIFFICULTY_LEVEL_MODERATE => 'Moderate',
            self::DIFFICULTY_LEVEL_HARD => 'Hard',
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

    /**
     * @return string
     */
    public function displayDifficultyLevel()
    {
        return self::optsDifficultyLevel()[$this->difficulty_level];
    }

    /**
     * @return bool
     */
    public function isDifficultyLevelEasy()
    {
        return $this->difficulty_level === self::DIFFICULTY_LEVEL_EASY;
    }

    public function setDifficultyLevelToEasy()
    {
        $this->difficulty_level = self::DIFFICULTY_LEVEL_EASY;
    }

    /**
     * @return bool
     */
    public function isDifficultyLevelModerate()
    {
        return $this->difficulty_level === self::DIFFICULTY_LEVEL_MODERATE;
    }

    public function setDifficultyLevelToModerate()
    {
        $this->difficulty_level = self::DIFFICULTY_LEVEL_MODERATE;
    }

    /**
     * @return bool
     */
    public function isDifficultyLevelHard()
    {
        return $this->difficulty_level === self::DIFFICULTY_LEVEL_HARD;
    }

    public function setDifficultyLevelToHard()
    {
        $this->difficulty_level = self::DIFFICULTY_LEVEL_HARD;
    }
}

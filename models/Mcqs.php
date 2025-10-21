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
 * @property string|null $reference
 * @property string|null $difficulty_level
 * @property string|null $tags
 * @property int $hierarchy_id
 * @property string|null $image_path
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ManagementTeam $createdBy
 * @property Hierarchy $hierarchy
 * @property UserBookmarkedMcqs[] $userBookmarkedMcqs
 * @property Users[] $users
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
            [['question_id', 'option_d', 'option_e', 'explanation', 'reference', 'difficulty_level', 'image_path'], 'default', 'value' => null],
            [['question_text', 'question_hash', 'option_a', 'option_b', 'option_c', 'correct_option', 'hierarchy_id', 'created_by'], 'required'],
            [['question_text', 'correct_option', 'explanation', 'reference', 'difficulty_level'], 'string'],
            [['hierarchy_id', 'created_by'], 'integer'],
            [['created_at', 'updated_at', 'tags'], 'safe'],
            [['question_id'], 'string', 'max' => 20],
            [['question_hash'], 'string', 'max' => 64],
            [['option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'image_path'], 'string', 'max' => 255],
            ['correct_option', 'in', 'range' => array_keys(self::optsCorrectOption())],
            ['difficulty_level', 'in', 'range' => array_keys(self::optsDifficultyLevel())],
            [['question_hash'], 'unique'],
            [['hierarchy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hierarchy::class, 'targetAttribute' => ['hierarchy_id' => 'id']],
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
            'reference' => 'Reference',
            'difficulty_level' => 'Difficulty Level',
            'tags' => 'Tags',
            'hierarchy_id' => 'Hierarchy ID',
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
     * Gets query for [[Hierarchy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHierarchy()
    {
        return $this->hasOne(Hierarchy::class, ['id' => 'hierarchy_id']);
    }

    /**
     * Gets query for [[UserBookmarkedMcqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserBookmarkedMcqs()
    {
        return $this->hasMany(UserBookmarkedMcqs::class, ['mcq_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['id' => 'user_id'])->viaTable('user_bookmarked_mcqs', ['mcq_id' => 'id']);
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


    public function getOrganSystemName()
    {
        return $this->hierarchy ? $this->hierarchy->organsys->name : null;
    }

    public function getSubjectName()
    {
        return $this->hierarchy ? $this->hierarchy->subject->name : null;
    }

    public function getChapterName()
    {
        return $this->hierarchy ? $this->hierarchy->chapter->name : null;
    }

    public function getTopicName()
    {
        return $this->hierarchy ? $this->hierarchy->topic->name : null;
    }
    public function getUserBookmarks()
    {
        return $this->hasMany(UserBookmarkedMcqs::class, ['mcq_id' => 'id']);
    }
}

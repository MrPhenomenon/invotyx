<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exam_specialties".
 *
 * @property int $id
 * @property string $exam_type
 * @property string $name
 * @property string $created_at
 *
 * @property ExamSessions[] $examSessions
 * @property MockExamDistribution[] $mockExamDistributions
 * @property Topics[] $topics
 * @property Users[] $users
 */
class ExamSpecialties extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const EXAM_TYPE_FCPS = 'FCPS';
    const EXAM_TYPE_USMLE = 'USMLE';
    const EXAM_TYPE_PLAB = 'PLAB';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_specialties';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_type', 'name'], 'required'],
            [['exam_type'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            ['exam_type', 'in', 'range' => array_keys(self::optsExamType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exam_type' => 'Exam Type',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ExamSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamSessions()
    {
        return $this->hasMany(ExamSessions::class, ['specialty_id' => 'id']);
    }

    /**
     * Gets query for [[MockExamDistributions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMockExamDistributions()
    {
        return $this->hasMany(MockExamDistribution::class, ['specialty_id' => 'id']);
    }

    /**
     * Gets query for [[Topics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopics()
    {
        return $this->hasMany(Topics::class, ['id' => 'topic_id'])->viaTable('mock_exam_distribution', ['specialty_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['specialty_id' => 'id']);
    }


    /**
     * column exam_type ENUM value labels
     * @return string[]
     */
    public static function optsExamType()
    {
        return [
            self::EXAM_TYPE_FCPS => 'FCPS',
            self::EXAM_TYPE_USMLE => 'USMLE',
            self::EXAM_TYPE_PLAB => 'PLAB',
        ];
    }

    /**
     * @return string
     */
    public function displayExamType()
    {
        return self::optsExamType()[$this->exam_type];
    }

    /**
     * @return bool
     */
    public function isExamTypeFcps()
    {
        return $this->exam_type === self::EXAM_TYPE_FCPS;
    }

    public function setExamTypeToFcps()
    {
        $this->exam_type = self::EXAM_TYPE_FCPS;
    }

    /**
     * @return bool
     */
    public function isExamTypeUsmle()
    {
        return $this->exam_type === self::EXAM_TYPE_USMLE;
    }

    public function setExamTypeToUsmle()
    {
        $this->exam_type = self::EXAM_TYPE_USMLE;
    }

    /**
     * @return bool
     */
    public function isExamTypePlab()
    {
        return $this->exam_type === self::EXAM_TYPE_PLAB;
    }

    public function setExamTypeToPlab()
    {
        $this->exam_type = self::EXAM_TYPE_PLAB;
    }
}

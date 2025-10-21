<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exam_specialties".
 *
 * @property int $id
 * @property int $exam_type
 * @property string $name
 * @property string $created_at
 *
 * @property ExamSessions[] $examSessions
 * @property ExamType $examType
 * @property MockExamDistribution[] $mockExamDistributions
 * @property Topics[] $topics
 * @property Users[] $users
 */
class ExamSpecialties extends \yii\db\ActiveRecord
{


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
            [['exam_type'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['exam_type'], 'exist', 'skipOnError' => true, 'targetClass' => ExamType::class, 'targetAttribute' => ['exam_type' => 'id']],
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
     * Gets query for [[ExamType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamType()
    {
        return $this->hasOne(ExamType::class, ['id' => 'exam_type']);
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

}

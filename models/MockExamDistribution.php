<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mock_exam_distribution".
 *
 * @property int $id
 * @property int $topic_id
 * @property int $specialty_id
 * @property float $percentage
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ExamSpecialties $specialty
 * @property Topics $topic
 */
class MockExamDistribution extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mock_exam_distribution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['topic_id', 'specialty_id', 'percentage'], 'required'],
            [['topic_id', 'specialty_id'], 'integer'],
            [['percentage'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['topic_id', 'specialty_id'], 'unique', 'targetAttribute' => ['topic_id', 'specialty_id']],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topics::class, 'targetAttribute' => ['topic_id' => 'id']],
            [['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSpecialties::class, 'targetAttribute' => ['specialty_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'topic_id' => 'Topic ID',
            'specialty_id' => 'Specialty ID',
            'percentage' => 'Percentage',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Specialty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty()
    {
        return $this->hasOne(ExamSpecialties::class, ['id' => 'specialty_id']);
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

}

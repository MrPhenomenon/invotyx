<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "topics".
 *
 * @property int $id
 * @property string $name
 * @property int $chapter_id
 *
 * @property Chapters $chapter
 * @property Mcqs[] $mcqs
 * @property MockExamDistribution[] $mockExamDistributions
 * @property ExamSpecialties[] $specialties
 */
class Topics extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'topics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'chapter_id'], 'required'],
            [['chapter_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['chapter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chapters::class, 'targetAttribute' => ['chapter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'chapter_id' => 'Chapter ID',
        ];
    }

    /**
     * Gets query for [[Chapter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChapter()
    {
        return $this->hasOne(Chapters::class, ['id' => 'chapter_id']);
    }

    /**
     * Gets query for [[Mcqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMcqs()
    {
        return $this->hasMany(Mcqs::class, ['topic_id' => 'id']);
    }

    /**
     * Gets query for [[MockExamDistributions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMockExamDistributions()
    {
        return $this->hasMany(MockExamDistribution::class, ['topic_id' => 'id']);
    }

    /**
     * Gets query for [[Specialties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialties()
    {
        return $this->hasMany(ExamSpecialties::class, ['id' => 'specialty_id'])->viaTable('mock_exam_distribution', ['topic_id' => 'id']);
    }

}

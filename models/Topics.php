<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "topics".
 *
 * @property int $id
 * @property string $name

 * @property Mcqs[] $mcqs
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
            [['name',], 'required'],
            [['name'], 'string', 'max' => 100],
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
        ];
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
     * Gets query for [[Specialties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialties()
    {
        return $this->hasMany(ExamSpecialties::class, ['id' => 'specialty_id'])->viaTable('mock_exam_distribution', ['topic_id' => 'id']);
    }

}

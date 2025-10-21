<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "specialty_distributions".
 *
 * @property int $id
 * @property int $specialty_id
 * @property int $subject_id
 * @property int $subject_count
 * @property float $subject_percentage
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ExamSpecialties $specialty
 * @property SpecialtyDistributionChapters[] $specialtyDistributionChapters
 * @property Subjects $subject
 */
class SpecialtyDistributions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specialty_distributions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['specialty_id', 'subject_id', 'subject_count', 'subject_percentage'], 'required'],
            [['specialty_id', 'subject_id', 'subject_count'], 'integer'],
            [['subject_percentage'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSpecialties::class, 'targetAttribute' => ['specialty_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'specialty_id' => 'Specialty ID',
            'subject_id' => 'Subject ID',
            'subject_count' => 'Subject Count',
            'subject_percentage' => 'Subject Percentage',
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
     * Gets query for [[SpecialtyDistributionChapters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialtyDistributionChapters()
    {
        return $this->hasMany(SpecialtyDistributionChapters::class, ['specialty_distribution_id' => 'id']);
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

}

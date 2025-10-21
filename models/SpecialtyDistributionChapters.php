<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "specialty_distribution_chapters".
 *
 * @property int $id
 * @property int $specialty_distribution_id
 * @property int $chapter_id
 * @property int $chapter_count
 * @property float $chapter_percentage
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Chapters $chapter
 * @property SpecialtyDistributions $specialtyDistribution
 */
class SpecialtyDistributionChapters extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'specialty_distribution_chapters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['specialty_distribution_id', 'chapter_id', 'chapter_count', 'chapter_percentage'], 'required'],
            [['specialty_distribution_id', 'chapter_id', 'chapter_count'], 'integer'],
            [['chapter_percentage'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['specialty_distribution_id'], 'exist', 'skipOnError' => true, 'targetClass' => SpecialtyDistributions::class, 'targetAttribute' => ['specialty_distribution_id' => 'id']],
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
            'specialty_distribution_id' => 'Specialty Distribution ID',
            'chapter_id' => 'Chapter ID',
            'chapter_count' => 'Chapter Count',
            'chapter_percentage' => 'Chapter Percentage',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[SpecialtyDistribution]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialtyDistribution()
    {
        return $this->hasOne(SpecialtyDistributions::class, ['id' => 'specialty_distribution_id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner_exams".
 *
 * @property int $id
 * @property int $external_partner_id
 * @property string $title
 * @property string |null $course_conductor
 * @property int|null $is_active
 * @property string|null $access_code
 * @property string|null $created_at
 *
 * @property ExternalPartners $externalPartner
 * @property PartnerExamAttempts[] $partnerExamAttempts
 * @property PartnerMcqs[] $partnerMcqs
 */
class PartnerExams extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_exams';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_code'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],
            [['external_partner_id', 'title'], 'required'],
            [['external_partner_id', 'is_active'], 'integer'],
            [['created_at', 'course_conductor'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['access_code'], 'string', 'max' => 32],
            [['external_partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExternalPartners::class, 'targetAttribute' => ['external_partner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_partner_id' => 'External Partner ID',
            'title' => 'Title',
            'course_conductor' => 'Course Conductor',
            'is_active' => 'Is Active',
            'access_code' => 'Access Code',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ExternalPartner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalPartner()
    {
        return $this->hasOne(ExternalPartners::class, ['id' => 'external_partner_id']);
    }

    /**
     * Gets query for [[PartnerExamAttempts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerExamAttempts()
    {
        return $this->hasMany(PartnerExamAttempts::class, ['partner_exam_id' => 'id']);
    }

    /**
     * Gets query for [[PartnerMcqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerMcqs()
    {
        return $this->hasMany(PartnerMcqs::class, ['partner_exam_id' => 'id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner_exam_access".
 *
 * @property int $id
 * @property int $partner_exam_id
 * @property string $email
 * @property string $passkey
 * @property int|null $has_attempted
 * @property string|null $created_at
 *
 * @property PartnerExams $partnerExam
 */
class PartnerExamAccess extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_exam_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['has_attempted'], 'default', 'value' => 0],
            [['partner_exam_id', 'email', 'passkey'], 'required'],
            [['partner_exam_id', 'has_attempted'], 'integer'],
            [['created_at'], 'safe'],
            [['email'], 'string', 'max' => 255],
            [['passkey'], 'string', 'max' => 64],
            [['partner_exam_id', 'email'], 'unique', 'targetAttribute' => ['partner_exam_id', 'email']],
            [['partner_exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerExams::class, 'targetAttribute' => ['partner_exam_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_exam_id' => 'Partner Exam ID',
            'email' => 'Email',
            'passkey' => 'Passkey',
            'has_attempted' => 'Has Attempted',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PartnerExam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerExam()
    {
        return $this->hasOne(PartnerExams::class, ['id' => 'partner_exam_id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "external_partners".
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string|null $contact_person
 * @property string|null $access_token
 * @property string|null $status
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property PartnerExams[] $partnerExams
 * @property PartnerMcqs[] $partnerMcqs
 */
class ExternalPartners extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_partners';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'contact_person', 'access_token', 'created_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'active'],
            [['name'], 'required'],
            [['status'], 'string'],
            [['created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['name', 'email', 'contact_person'], 'string', 'max' => 255],
            [['access_token'], 'string', 'max' => 64],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['access_token'], 'unique'],
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
            'email' => 'Email',
            'contact_person' => 'Contact Person',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PartnerExams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerExams()
    {
        return $this->hasMany(PartnerExams::class, ['external_partner_id' => 'id']);
    }
    public function beforeSave($insert)
    {
        if ($insert && empty($this->access_token)) {
            $this->access_token = Yii::$app->security->generateRandomString(6);
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[PartnerMcqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerMcqs()
    {
        return $this->hasMany(PartnerMcqs::class, ['external_partner_id' => 'id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_INACTIVE => 'inactive',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusInactive()
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function setStatusToInactive()
    {
        $this->status = self::STATUS_INACTIVE;
    }
}

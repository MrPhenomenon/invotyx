<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int $id
 * @property string $type
 * @property float $price
 * @property int $duration_days
 * @property string|null $features_json
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Payments[] $payments
 * @property Users[] $users
 */
class Subscriptions extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TYPE_BASIC = 'basic';
    const TYPE_PRO = 'pro';
    const TYPE_MOCK_ONLY = 'mock-only';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['features_json'], 'default', 'value' => null],
            [['type', 'price', 'duration_days'], 'required'],
            [['type'], 'string'],
            [['price'], 'number'],
            [['duration_days'], 'integer'],
            [['features_json', 'created_at', 'updated_at'], 'safe'],
            ['type', 'in', 'range' => array_keys(self::optsType())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'price' => 'Price',
            'duration_days' => 'Duration Days',
            'features_json' => 'Features Json',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['subscription_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['subscription_id' => 'id']);
    }


    /**
     * column type ENUM value labels
     * @return string[]
     */
    public static function optsType()
    {
        return [
            self::TYPE_BASIC => 'basic',
            self::TYPE_PRO => 'pro',
            self::TYPE_MOCK_ONLY => 'mock-only',
        ];
    }

    /**
     * @return string
     */
    public function displayType()
    {
        return self::optsType()[$this->type];
    }

    /**
     * @return bool
     */
    public function isTypeBasic()
    {
        return $this->type === self::TYPE_BASIC;
    }

    public function setTypeToBasic()
    {
        $this->type = self::TYPE_BASIC;
    }

    /**
     * @return bool
     */
    public function isTypePro()
    {
        return $this->type === self::TYPE_PRO;
    }

    public function setTypeToPro()
    {
        $this->type = self::TYPE_PRO;
    }

    /**
     * @return bool
     */
    public function isTypeMockOnly()
    {
        return $this->type === self::TYPE_MOCK_ONLY;
    }

    public function setTypeToMockOnly()
    {
        $this->type = self::TYPE_MOCK_ONLY;
    }
}

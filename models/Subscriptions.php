<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int $id
 * @property string $name
 * @property float $price
 * @property int $duration_days
 * @property string|null $features_json
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Payments[] $payments
 * @property UserSubscriptions[] $userSubscriptions
 */
class Subscriptions extends \yii\db\ActiveRecord
{


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
            [['name', 'price', 'duration_days'], 'required'],
            [['price'], 'number'],
            [['duration_days'], 'integer'],
            [['features_json', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 25],
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
     * Gets query for [[UserSubscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubscriptions()
    {
        return $this->hasMany(UserSubscriptions::class, ['subscription_id' => 'id']);
    }

}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_subscriptions".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subscription_id
 * @property string $start_date
 * @property string $end_date
 * @property int $is_active 0 - Non Active 1 - Active
 *
 * @property Subscriptions $subscription
 * @property Users $user
 */
class UserSubscriptions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'subscription_id', 'end_date', 'is_active'], 'required'],
            [['user_id', 'subscription_id', 'is_active'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriptions::class, 'targetAttribute' => ['subscription_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'subscription_id' => 'Subscription ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Gets query for [[Subscription]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscription()
    {
        return $this->hasOne(Subscriptions::class, ['id' => 'subscription_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

}

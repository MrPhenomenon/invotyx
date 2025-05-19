<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subscription_id
 * @property float $amount
 * @property string $method
 * @property string $status
 * @property string|null $transaction_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Subscriptions $subscription
 * @property Users $user
 */
class Payments extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['user_id', 'subscription_id', 'amount', 'method'], 'required'],
            [['user_id', 'subscription_id'], 'integer'],
            [['amount'], 'number'],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['method'], 'string', 'max' => 50],
            [['transaction_id'], 'string', 'max' => 100],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['transaction_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriptions::class, 'targetAttribute' => ['subscription_id' => 'id']],
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
            'amount' => 'Amount',
            'method' => 'Method',
            'status' => 'Status',
            'transaction_id' => 'Transaction ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_FAILED => 'failed',
            self::STATUS_REFUNDED => 'refunded',
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
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function setStatusToCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isStatusFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setStatusToFailed()
    {
        $this->status = self::STATUS_FAILED;
    }

    /**
     * @return bool
     */
    public function isStatusRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function setStatusToRefunded()
    {
        $this->status = self::STATUS_REFUNDED;
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reports".
 *
 * @property int $id
 * @property string $mcq_id
 * @property int $reported_by
 * @property string $status
 * @property string $message
 * @property string $reported_at
 */
class Reports extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SOLVED = 'solved';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reports';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 'pending'],
            [['mcq_id', 'reported_by', 'message'], 'required'],
            [['reported_by'], 'integer'],
            [['status', 'mcq_id'], 'string'],
            [['reported_at'], 'safe'],
            [['message'], 'string', 'max' => 250],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mcq_id' => 'Mcq ID',
            'reported_by' => 'Reported By',
            'status' => 'Status',
            'message' => 'Message',
            'reported_at' => 'Reported At',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_SOLVED => 'solved',
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
    public function isStatusSolved()
    {
        return $this->status === self::STATUS_SOLVED;
    }

    public function setStatusToSolved()
    {
        $this->status = self::STATUS_SOLVED;
    }

    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'reported_by']);
    }
}

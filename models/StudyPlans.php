<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "study_plans".
 *
 * @property int $id
 * @property int $user_id
 * @property string $start_date
 * @property string $exam_date
 * @property int $mcqs_per_day
 * @property int $total_capacity
 * @property string|null $status
 * @property string $last_generated_week
 * @property string $created_at
 * @property string $updated_at

 *
 * @property StudyPlanDays[] $studyPlanDays
 * @property Users $user
 */
class StudyPlans extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ARCHIVED = 'archived';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_plans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 'draft'],
            [['user_id', 'start_date', 'exam_date', 'mcqs_per_day', 'total_capacity', 'created_at', 'required']],
            [['user_id', 'mcqs_per_day', 'total_capacity'], 'integer'],
            [['start_date', 'exam_date', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
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
            'start_date' => 'Start Date',
            'exam_date' => 'Exam Date',
            'mcqs_per_day' => 'Mcqs Per Day',
            'total_capacity' => 'Total Capacity',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[StudyPlanDays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPlanDays()
    {
        return $this->hasMany(StudyPlanDays::class, ['study_plan_id' => 'id']);
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
            self::STATUS_DRAFT => 'draft',
            self::STATUS_ACTIVE => 'active',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_ARCHIVED => 'archived',
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
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
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
    public function isStatusArchived()
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function setStatusToArchived()
    {
        $this->status = self::STATUS_ARCHIVED;
    }
}

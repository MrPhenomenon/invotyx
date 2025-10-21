<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "study_plan_days".
 *
 * @property int $id
 * @property int $study_plan_id
 * @property int $day_number
 * @property string $plan_date
 * @property int|null $review_mcqs
 * @property int|null $new_mcqs
 * @property int|null $is_mock_exam
 * @property string|null $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property StudyPlans $studyPlan
 * @property StudyPlanDaySubjects[] $studyPlanDaySubjects
 */
class StudyPlanDays extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SKIPPED = 'skipped';
    const STATUS_UPCOMING = 'upcoming';
    const STATUS_IN_PROGRESS = 'in_progress';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_plan_days';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => null],
            [['is_mock_exam'], 'default', 'value' => 0],
            [['study_plan_id', 'day_number', 'plan_date', 'created_at', 'updated_at'], 'required'],
            [['study_plan_id', 'day_number', 'review_mcqs', 'new_mcqs', 'is_mock_exam'], 'integer'],
            [['plan_date', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['study_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyPlans::class, 'targetAttribute' => ['study_plan_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'study_plan_id' => 'Study Plan ID',
            'day_number' => 'Day Number',
            'plan_date' => 'Plan Date',
            'review_mcqs' => 'Review Mcqs',
            'new_mcqs' => 'New Mcqs',
            'is_mock_exam' => 'Is Mock Exam',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[StudyPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPlan()
    {
        return $this->hasOne(StudyPlans::class, ['id' => 'study_plan_id']);
    }

    /**
     * Gets query for [[StudyPlanDaySubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPlanDaySubjects()
    {
        return $this->hasMany(StudyPlanDaySubjects::class, ['study_plan_day_id' => 'id']);
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
            self::STATUS_SKIPPED => 'skipped',
            self::STATUS_UPCOMING => 'upcoming',
            self::STATUS_IN_PROGRESS => 'in_progress',
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
    public function isStatusSkipped()
    {
        return $this->status === self::STATUS_SKIPPED;
    }

    public function setStatusToSkipped()
    {
        $this->status = self::STATUS_SKIPPED;
    }

    /**
     * @return bool
     */
    public function isStatusUpcoming()
    {
        return $this->status === self::STATUS_UPCOMING;
    }

    public function setStatusToUpcoming()
    {
        $this->status = self::STATUS_UPCOMING;
    }
}

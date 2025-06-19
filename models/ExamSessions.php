<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exam_sessions".
 *
 * @property int $id
 * @property int $user_id
 * @property string $mode
 * @property int $exam_type
 * @property int $specialty_id
 * @property string|null $topics_used
 * @property string $mcq_ids
 * @property string $start_time
 * @property string|null $end_time
 * @property int|null $total_questions
 * @property int|null $time_spent_seconds
 * @property int|null $correct_count
 * @property string $status
 * @property int|null $breaches
 * @property float|null $accuracy
 * @property string $updated_at
 *
 * @property ExamType $examType
 * @property ExamSpecialties $specialty
 * @property Users $user
 * @property UserMcqInteractions[] $userMcqInteractions
 */
class ExamSessions extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const MODE_PRACTICE = 'practice';
    const MODE_EXAM = 'exam';
    const MODE_MOCK = 'mock';
    const MODE_TRAINING = 'training';
    const STATUS_INPROGRESS = 'InProgress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_TERMINATED = 'Terminated';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_sessions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['topics_used', 'end_time', 'total_questions', 'time_spent_seconds', 'correct_count', 'accuracy'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'InProgress'],
            [['breaches'], 'default', 'value' => 0],
            [['user_id', 'mode', 'exam_type', 'specialty_id', 'mcq_ids'], 'required'],
            [['user_id', 'exam_type', 'specialty_id', 'total_questions', 'time_spent_seconds', 'correct_count', 'breaches'], 'integer'],
            [['mode', 'mcq_ids', 'status'], 'string'],
            [['topics_used', 'start_time', 'end_time', 'updated_at'], 'safe'],
            [['accuracy'], 'number'],
            ['mode', 'in', 'range' => array_keys(self::optsMode())],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSpecialties::class, 'targetAttribute' => ['specialty_id' => 'id']],
            [['exam_type'], 'exist', 'skipOnError' => true, 'targetClass' => ExamType::class, 'targetAttribute' => ['exam_type' => 'id']],
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
            'mode' => 'Mode',
            'exam_type' => 'Exam Type',
            'specialty_id' => 'Specialty ID',
            'topics_used' => 'Topics Used',
            'mcq_ids' => 'Mcq Ids',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'total_questions' => 'Total Questions',
            'time_spent_seconds' => 'Time Spent Seconds',
            'correct_count' => 'Correct Count',
            'status' => 'Status',
            'breaches' => 'Breaches',
            'accuracy' => 'Accuracy',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ExamType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamType()
    {
        return $this->hasOne(ExamType::class, ['id' => 'exam_type']);
    }

    /**
     * Gets query for [[Specialty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialty()
    {
        return $this->hasOne(ExamSpecialties::class, ['id' => 'specialty_id']);
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
     * Gets query for [[UserMcqInteractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserMcqInteractions()
    {
        return $this->hasMany(UserMcqInteractions::class, ['exam_session_id' => 'id']);
    }


    /**
     * column mode ENUM value labels
     * @return string[]
     */
    public static function optsMode()
    {
        return [
            self::MODE_PRACTICE => 'practice',
            self::MODE_EXAM => 'exam',
            self::MODE_MOCK => 'mock',
            self::MODE_TRAINING => 'training',
        ];
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_INPROGRESS => 'InProgress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_TERMINATED => 'Terminated',
        ];
    }

    /**
     * @return string
     */
    public function displayMode()
    {
        return self::optsMode()[$this->mode];
    }

    /**
     * @return bool
     */
    public function isModePractice()
    {
        return $this->mode === self::MODE_PRACTICE;
    }

    public function setModeToPractice()
    {
        $this->mode = self::MODE_PRACTICE;
    }

    /**
     * @return bool
     */
    public function isModeExam()
    {
        return $this->mode === self::MODE_EXAM;
    }

    public function setModeToExam()
    {
        $this->mode = self::MODE_EXAM;
    }

    /**
     * @return bool
     */
    public function isModeMock()
    {
        return $this->mode === self::MODE_MOCK;
    }

    public function setModeToMock()
    {
        $this->mode = self::MODE_MOCK;
    }

    /**
     * @return bool
     */
    public function isModeTraining()
    {
        return $this->mode === self::MODE_TRAINING;
    }

    public function setModeToTraining()
    {
        $this->mode = self::MODE_TRAINING;
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
    public function isStatusInprogress()
    {
        return $this->status === self::STATUS_INPROGRESS;
    }

    public function setStatusToInprogress()
    {
        $this->status = self::STATUS_INPROGRESS;
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
    public function isStatusTerminated()
    {
        return $this->status === self::STATUS_TERMINATED;
    }

    public function setStatusToTerminated()
    {
        $this->status = self::STATUS_TERMINATED;
    }
}

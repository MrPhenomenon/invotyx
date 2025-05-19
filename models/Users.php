<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $role
 * @property int|null $subscription_id
 * @property string|null $exam_type
 * @property int|null $specialty_id
 * @property string|null $expected_exam_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ExamSessions[] $examSessions
 * @property Mcqs[] $mcqs
 * @property Payments[] $payments
 * @property ExamSpecialties $specialty
 * @property Subscriptions $subscription
 * @property UserMcqInteractions[] $userMcqInteractions
 */
class Users extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const EXAM_TYPE_FCPS = 'FCPS';
    const EXAM_TYPE_USMLE = 'USMLE';
    const EXAM_TYPE_PLAB = 'PLAB';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscription_id', 'exam_type', 'specialty_id', 'expected_exam_date'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 0],
            [['name', 'email', 'password'], 'required'],
            [['role', 'subscription_id', 'specialty_id'], 'integer'],
            [['exam_type'], 'string'],
            [['expected_exam_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 255],
            ['exam_type', 'in', 'range' => array_keys(self::optsExamType())],
            [['email'], 'unique'],
            [['subscription_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriptions::class, 'targetAttribute' => ['subscription_id' => 'id']],
            [['specialty_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSpecialties::class, 'targetAttribute' => ['specialty_id' => 'id']],
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
            'password' => 'Password',
            'role' => 'Role',
            'subscription_id' => 'Subscription ID',
            'exam_type' => 'Exam Type',
            'specialty_id' => 'Specialty ID',
            'expected_exam_date' => 'Expected Exam Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ExamSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamSessions()
    {
        return $this->hasMany(ExamSessions::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Mcqs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMcqs()
    {
        return $this->hasMany(Mcqs::class, ['created_by' => 'id']);
    }

    /**
     * Gets query for [[Payments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payments::class, ['user_id' => 'id']);
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
     * Gets query for [[Subscription]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscription()
    {
        return $this->hasOne(Subscriptions::class, ['id' => 'subscription_id']);
    }

    /**
     * Gets query for [[UserMcqInteractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserMcqInteractions()
    {
        return $this->hasMany(UserMcqInteractions::class, ['user_id' => 'id']);
    }


    /**
     * column exam_type ENUM value labels
     * @return string[]
     */
    public static function optsExamType()
    {
        return [
            self::EXAM_TYPE_FCPS => 'FCPS',
            self::EXAM_TYPE_USMLE => 'USMLE',
            self::EXAM_TYPE_PLAB => 'PLAB',
        ];
    }

    /**
     * @return string
     */
    public function displayExamType()
    {
        return self::optsExamType()[$this->exam_type];
    }

    /**
     * @return bool
     */
    public function isExamTypeFcps()
    {
        return $this->exam_type === self::EXAM_TYPE_FCPS;
    }

    public function setExamTypeToFcps()
    {
        $this->exam_type = self::EXAM_TYPE_FCPS;
    }

    /**
     * @return bool
     */
    public function isExamTypeUsmle()
    {
        return $this->exam_type === self::EXAM_TYPE_USMLE;
    }

    public function setExamTypeToUsmle()
    {
        $this->exam_type = self::EXAM_TYPE_USMLE;
    }

    /**
     * @return bool
     */
    public function isExamTypePlab()
    {
        return $this->exam_type === self::EXAM_TYPE_PLAB;
    }

    public function setExamTypeToPlab()
    {
        $this->exam_type = self::EXAM_TYPE_PLAB;
    }
}

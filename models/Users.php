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
 * @property int $role 0 - User
 * @property int $exam_type
 * @property int|null $speciality_id
 * @property string|null $expected_exam_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ExamSessions[] $examSessions
 * @property ExamType $examType
 * @property Payments[] $payments
 * @property ExamSpecialties $speciality
 * @property UserMcqInteractions[] $userMcqInteractions
 * @property UserSubscriptions[] $userSubscriptions
 */
class Users extends \yii\db\ActiveRecord
{


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
            [['speciality_id', 'expected_exam_date'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 0],
            [['name', 'email', 'password', 'exam_type'], 'required'],
            [['role', 'exam_type', 'speciality_id'], 'integer'],
            [['expected_exam_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['exam_type'], 'exist', 'skipOnError' => true, 'targetClass' => ExamType::class, 'targetAttribute' => ['exam_type' => 'id']],
            [['speciality_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamSpecialties::class, 'targetAttribute' => ['speciality_id' => 'id']],
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
            'exam_type' => 'Exam Type',
            'speciality_id' => 'Speciality ID',
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
     * Gets query for [[ExamType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamType()
    {
        return $this->hasOne(ExamType::class, ['id' => 'exam_type']);
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
     * Gets query for [[Speciality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpeciality()
    {
        return $this->hasOne(ExamSpecialties::class, ['id' => 'speciality_id']);
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
     * Gets query for [[UserSubscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSubscriptions()
    {
        return $this->hasMany(UserSubscriptions::class, ['user_id' => 'id']);
    }

}

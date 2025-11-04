<?php

namespace app\models;
use yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property int|null $google_id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string|null $phone
 * @property string $auth_type
 * @property int|null $exam_type
 * @property int|null $specialty_id
 * @property string|null $expected_exam_date
 * @property string|null $profile_picture
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $weak_subjects
 * @property int|null $mcqs_per_day
 *
 * @property ExamSessions[] $examSessions
 * @property ExamType $examType
 * @property Payments[] $payments
 * @property ExamSpecialties $speciality
 * @property UserMcqInteractions[] $userMcqInteractions
 * @property UserSubscriptions[] $userSubscriptions
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{

    /**
     * ENUM field values
     */
    const AUTH_TYPE_GOOGLE = 'google';
    const AUTH_TYPE_LOCAL = 'local';

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
            [['google_id', 'password', 'exam_type', 'specialty_id', 'expected_exam_date', 'profile_picture'], 'default', 'value' => null],
            [['google_id', 'exam_type', 'specialty_id'], 'integer'],
            [['name', 'email', 'auth_type'], 'required'],
            [['auth_type'], 'string'],
            [['expected_exam_date', 'created_at', 'updated_at', 'weak_subjects', 'mcqs_per_day'], 'safe'],
            [['name', 'phone'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['password', 'profile_picture'], 'string', 'max' => 255],
            ['auth_type', 'in', 'range' => array_keys(self::optsAuthType())],
            [['email'], 'unique'],
            [['exam_type'], 'exist', 'skipOnError' => true, 'targetClass' => ExamType::class, 'targetAttribute' => ['exam_type' => 'id']],
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
            'google_id' => 'Google ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'auth_type' => 'Auth Type',
            'exam_type' => 'Exam Type',
            'specialty_id' => 'Speciality ID',
            'expected_exam_date' => 'Expected Exam Date',
            'profile_picture' => 'Profile Picture',
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
        return $this->hasOne(ExamSpecialties::class, ['id' => 'specialty_id']);
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


    /**
     * column auth_type ENUM value labels
     * @return string[]
     */
    public static function optsAuthType()
    {
        return [
            self::AUTH_TYPE_GOOGLE => 'google',
            self::AUTH_TYPE_LOCAL => 'local',
        ];
    }

    /**
     * @return string
     */
    public function displayAuthType()
    {
        return self::optsAuthType()[$this->auth_type];
    }

    /**
     * @return bool
     */
    public function isAuthTypeGoogle()
    {
        return $this->auth_type === self::AUTH_TYPE_GOOGLE;
    }

    public function setAuthTypeToGoogle()
    {
        $this->auth_type = self::AUTH_TYPE_GOOGLE;
    }

    /**
     * @return bool
     */
    public function isAuthTypeLocal()
    {
        return $this->auth_type === self::AUTH_TYPE_LOCAL;
    }

    public function setAuthTypeToLocal()
    {
        $this->auth_type = self::AUTH_TYPE_LOCAL;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByName($username)
    {
        return static::findOne(['name' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public static function TerminateSession()
    {
        $userId = Yii::$app->user->id;
        $sessions = ExamSessions::find()
            ->where(['user_id' => $userId, 'status' => ExamSessions::STATUS_INPROGRESS])
            ->orderBy(['end_time' => SORT_DESC])
            ->all();

        if (!$sessions) {
            return;
        }

        foreach ($sessions as $session) {
            $isExpired = false;
            $cacheKey = 'exam_state_' . $userId . '_' . $session->id;
            $cacheData = Yii::$app->cache->get($cacheKey);

            if (!$cacheData) {
                $isExpired = true;
            } else if (isset($cacheData['start_time']) && isset($session->time_limit) && $session->time_limit > 0) {
                $limitInSeconds = $session->time_limit * 60;
                if ((time() - $cacheData['start_time']) > $limitInSeconds) {
                    $isExpired = true;
                }
            }

            if ($isExpired && isset($cacheData['study_plan_day_id'])) {
                $plan = StudyPlanDays::findOne($cacheData['study_plan_day_id']);
                $plan->status = StudyPlanDays::STATUS_SKIPPED;
                $plan->save(false);
            }

            if ($isExpired) {
                $session->status = 'Terminated';
                $session->end_time = date('Y-m-d H:i:s');
                $session->updated_at = date('Y-m-d H:i:s');
                $session->save(false);
                Yii::$app->cache->delete($cacheKey);
            }
        }

        return;
    }
}

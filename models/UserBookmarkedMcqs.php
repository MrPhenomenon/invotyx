<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_bookmarked_mcqs".
 *
 * @property int $id
 * @property int $user_id
 * @property int $mcq_id
 * @property string $created_at
 *
 * @property Mcqs $mcq
 * @property Users $user
 */
class UserBookmarkedMcqs extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_bookmarked_mcqs';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'mcq_id'], 'required'],
            [['user_id', 'mcq_id'], 'integer'],

            [['user_id', 'mcq_id'], 'unique', 'targetAttribute' => ['user_id', 'mcq_id'], 'message' => 'This question is already bookmarked.'],
            [['created_at'], 'safe'],

            [['mcq_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mcqs::class, 'targetAttribute' => ['mcq_id' => 'id']],
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
            'mcq_id' => 'Mcq ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Mcq]].
     * @return \yii\db\ActiveQuery
     */
    public function getMcq()
    {
        return $this->hasOne(Mcqs::class, ['id' => 'mcq_id']);
    }

    /**
     * Gets query for [[User]].
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
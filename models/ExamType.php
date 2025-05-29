<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exam_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property ExamSessions[] $examSessions
 * @property ExamSpecialties[] $examSpecialties
 * @property Users[] $users
 */
class ExamType extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20],
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
        ];
    }

    /**
     * Gets query for [[ExamSessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamSessions()
    {
        return $this->hasMany(ExamSessions::class, ['exam_type' => 'id']);
    }

    /**
     * Gets query for [[ExamSpecialties]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamSpecialties()
    {
        return $this->hasMany(ExamSpecialties::class, ['exam_type' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['exam_type' => 'id']);
    }

}

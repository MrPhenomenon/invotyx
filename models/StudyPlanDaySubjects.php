<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "study_plan_day_subjects".
 *
 * @property int $id
 * @property int $study_plan_day_id
 * @property int $subject_id
 * @property int|null $chapter_id
 * @property int|null $topic_id
 * @property int $allocated_mcqs
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Chapters $chapter
 * @property StudyPlanDays $studyPlanDay
 * @property Subjects $subject
 * @property Topics $topic
 */
class StudyPlanDaySubjects extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'study_plan_day_subjects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chapter_id', 'topic_id'], 'default', 'value' => null],
            [['study_plan_day_id', 'subject_id', 'allocated_mcqs', 'created_at', 'updated_at'], 'required'],
            [['study_plan_day_id', 'subject_id', 'chapter_id', 'topic_id', 'allocated_mcqs'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['study_plan_day_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyPlanDays::class, 'targetAttribute' => ['study_plan_day_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
            [['chapter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chapters::class, 'targetAttribute' => ['chapter_id' => 'id']],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => Topics::class, 'targetAttribute' => ['topic_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'study_plan_day_id' => 'Study Plan Day ID',
            'subject_id' => 'Subject ID',
            'chapter_id' => 'Chapter ID',
            'topic_id' => 'Topic ID',
            'allocated_mcqs' => 'Allocated Mcqs',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Chapter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChapter()
    {
        return $this->hasOne(Chapters::class, ['id' => 'chapter_id']);
    }

    /**
     * Gets query for [[StudyPlanDay]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudyPlanDay()
    {
        return $this->hasOne(StudyPlanDays::class, ['id' => 'study_plan_day_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subjects::class, ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[Topic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(Topics::class, ['id' => 'topic_id']);
    }

}

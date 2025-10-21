<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hierarchy".
 *
 * @property int $id
 * @property int $organsys_id
 * @property int $subject_id
 * @property int $chapter_id
 * @property int $topic_id
 *
 * @property Chapters $chapter
 * @property OrganSystems $organsys
 * @property Subjects $subject
 * @property Topics $topic
 */
class Hierarchy extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hierarchy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['organsys_id', 'subject_id', 'chapter_id', 'topic_id'], 'required'],
            [['organsys_id', 'subject_id', 'chapter_id', 'topic_id'], 'integer'],
            [['chapter_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chapters::class, 'targetAttribute' => ['chapter_id' => 'id']],
            [['organsys_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrganSystems::class, 'targetAttribute' => ['organsys_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subjects::class, 'targetAttribute' => ['subject_id' => 'id']],
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
            'organsys_id' => 'Organsys ID',
            'subject_id' => 'Subject ID',
            'chapter_id' => 'Chapter ID',
            'topic_id' => 'Topic ID',
        ];
    }

    public function getMcqs()
    {
        return $this->hasMany(Mcqs::class, ['hierarchy_id' => 'id']);
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
     * Gets query for [[Organsys]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrgansys()
    {
        return $this->hasOne(OrganSystems::class, ['id' => 'organsys_id']);
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

    public static function getChaptersForSubjects(array $subjectIds)
    {
        $query = self::find()
            ->select(['chapter_id'])
            ->where(['subject_id' => $subjectIds])
            ->distinct();

        return Chapters::find()->where(['id' => $query])->all();
    }

    public static function getTopicsForChapters(array $chapterIds, array $subjectIds = [])
    {
        $query = self::find()->select(['topic_id'])->where(['chapter_id' => $chapterIds]);

        if (!empty($subjectIds)) {
            $query->andWhere(['subject_id' => $subjectIds]);
        }

        return Topics::find()->where(['id' => $query])->all();
    }

    public static function getSubjectsForOrganSystems(array $organSystemIds = [])
    {
        $query = self::find()->select(['subject_id'])->distinct();

        if (!empty($organSystemIds)) {
            $query->andWhere(['organsys_id' => $organSystemIds]);
        }

        return Subjects::find()->where(['id' => $query])->all();
    }

    public static function getMcqCounts($level, $ids = [])
    {
        $query = Mcqs::find()
            ->alias('m')
            ->select(["h.{$level}_id AS id", 'COUNT(m.id) AS mcq_count'])
            ->joinWith('hierarchy h')
            ->groupBy("h.{$level}_id");

        if (!empty($ids)) {
            $query->andWhere(["h.{$level}_id" => $ids]);
        }

        return $query->asArray()->all();
    }
}

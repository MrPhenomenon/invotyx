<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ExamSessions;
use app\models\ExamType; // Assuming you have this model

/**
 * ExamSessionSearch represents the model behind the search form of `app\models\ExamSessions`.
 */
class ExamSessionSearch extends ExamSessions
{
    // Custom attributes for filtering
    public $date_range;
    public $score_threshold_filter;
    public $breached_only_filter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Inherit rules from parent, but make them safe for searching
            [['id', 'user_id', 'exam_type', 'specialty_id', 'total_questions', 'time_spent_seconds', 'correct_count', 'breaches', 'part_number', 'mock_group_id'], 'integer'],
            [['mode', 'status', 'date_range', 'score_threshold_filter', 'breached_only_filter'], 'safe'],
            [['accuracy'], 'number'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ExamSessions::find()
            ->joinWith(['user', 'examType'])
            ->where(['exam_sessions.status' => ['Completed', 'Breached']]); // We usually analyze completed sessions

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['start_time' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Apply standard filters
        $query->andFilterWhere([
            'exam_sessions.exam_type' => $this->exam_type,
            'exam_sessions.mode' => $this->mode,
        ]);

        // Apply custom filters
        if ($this->breached_only_filter) {
            $query->andFilterWhere(['>', 'exam_sessions.breaches', 0]);
        }

        if ($this->score_threshold_filter) {
            // Assuming accuracy is stored as a float (e.g., 0.85 for 85%)
            $query->andFilterWhere(['<', 'exam_sessions.accuracy', $this->score_threshold_filter / 100]);
        }

        if (!empty($this->date_range) && strpos($this->date_range, ' - ') !== false) {
            list($start_date, $end_date) = explode(' - ', $this->date_range);
            $query->andFilterWhere(['between', 'DATE(exam_sessions.start_time)', date('Y-m-d', strtotime($start_date)), date('Y-m-d', strtotime($end_date))]);
        }

        return $dataProvider;
    }
}
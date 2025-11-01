<?php

namespace app\services;

use Yii;
use app\models\Users;
use app\models\StudyPlans;
use app\models\StudyPlanDays;
use app\models\StudyPlanDaySubjects;
use app\models\SpecialtyDistributions;
use app\models\SpecialtyDistributionChapters;
use app\models\Topics;
use app\models\Hierarchy;
use app\models\Mcqs;
use yii\db\Expression;
use yii\db\Query;

class StudyPlanGenerator
{
    public static function ensureWeeklyPlan(Users $user)
    {
        $today = date('Y-m-d');
        $mcqsPerDay = intval($user->mcqs_per_day) === 0 ? 180 : intval($user->mcqs_per_day);
        $totalAvailable = self::calculateTotalCurriculumMcqs($user->specialty_id);

        $plan = StudyPlans::findOne(['user_id' => $user->id]);
        if (!$plan) {
            $plan = new StudyPlans([
                'user_id' => $user->id,
                'start_date' => $today,
                'exam_date' => $user->expected_exam_date,
                'mcqs_per_day' => $mcqsPerDay,
                'total_capacity' => $totalAvailable,
                'status' => 'active',
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);
            if (!$plan->save(false)) {
                Yii::error("Failed to create StudyPlan for user {$user->id}: " . print_r($plan->errors, true));
                return;
            }
        }

        StudyPlanDays::updateAll(
            ['status' => 'skipped', 'updated_at' => new Expression('NOW()')],
            ['and', ['study_plan_id' => $plan->id], ['<', 'plan_date', $today], ['!=', 'status', 'completed']]
        );
        StudyPlanDays::updateAll(
            ['status' => 'pending', 'updated_at' => new Expression('NOW()')],
            ['and', ['study_plan_id' => $plan->id], ['plan_date' => $today], ['!=', 'status', 'completed']]
        );

        $latestGeneratedDate = (new Query())
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->max('plan_date');

        if (!$latestGeneratedDate) {
            $needed = 7;
        } else {
            if ($latestGeneratedDate <= $today) {
                $needed = 7;
            } else {
                $needed = 0;
            }
        }
        if (!$needed > 0) {
            return;
        }

        $prevWindowStart = date('Y-m-d', strtotime($today . ' -7 days'));
        $prevWindowEnd = date('Y-m-d', strtotime($today . ' -1 day'));

        $skippedDayIds = (new Query())
            ->select('id')
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->andWhere(['between', 'plan_date', $prevWindowStart, $prevWindowEnd])
            ->andWhere(['status' => 'skipped'])
            ->column();

        $skippedTotalMcqs = 0;
        $skippedPoolByTopic = [];
        if (!empty($skippedDayIds)) {
            $skippedSubjects = (new Query())
                ->from(StudyPlanDaySubjects::tableName())
                ->select(['topic_id', 'mcq_ids'])
                ->where(['in', 'study_plan_day_id', $skippedDayIds])
                ->all();

            foreach ($skippedSubjects as $ss) {
                if (empty($ss['mcq_ids']))
                    continue;
                $ids = @json_decode($ss['mcq_ids'], true);
                if (!is_array($ids))
                    continue;

                $topicKey = intval($ss['topic_id']);
                if (!isset($skippedPoolByTopic[$topicKey]))
                    $skippedPoolByTopic[$topicKey] = [];

                foreach ($ids as $mid) {
                    $mid = intval($mid);
                    $skippedPoolByTopic[$topicKey][$mid] = true;
                }
            }

            foreach ($skippedPoolByTopic as $topic => $map) {
                $count = count($map);
                $skippedTotalMcqs += $count;
                $skippedPoolByTopic[$topic] = array_values(array_keys($map));
            }
        }

        $planDayIds = (new Query())
            ->select('id')
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->column();

        $assignedPerHierarchy = [];
        if (!empty($planDayIds)) {
            $rows = (new Query())
                ->select(['topic_id', 'mcq_ids'])
                ->from(StudyPlanDaySubjects::tableName())
                ->where(['in', 'study_plan_day_id', $planDayIds])
                ->all();

            foreach ($rows as $r) {
                if (empty($r['mcq_ids']))
                    continue;
                $ids = @json_decode($r['mcq_ids'], true);
                if (!is_array($ids))
                    continue;

                $topicKey = intval($r['topic_id']);
                if (!isset($assignedPerHierarchy[$topicKey]))
                    $assignedPerHierarchy[$topicKey] = [];
                foreach ($ids as $mid)
                    $assignedPerHierarchy[$topicKey][intval($mid)] = true;
            }
        }

        $orderedHierarchies = [];
        $subjectOrder = (new Query())->select('subject_id')->from(SpecialtyDistributions::tableName())->where(['specialty_id' => $user->specialty_id])->orderBy(['subject_percentage' => SORT_DESC])->column();
        foreach ($subjectOrder as $subjectId) {
            $sd = SpecialtyDistributions::findOne(['specialty_id' => $user->specialty_id, 'subject_id' => $subjectId]);
            if (!$sd)
                continue;
            $chapterOrder = (new Query())->select('chapter_id')->from(SpecialtyDistributionChapters::tableName())->where(['specialty_distribution_id' => $sd->id])->orderBy(['chapter_percentage' => SORT_DESC])->column();
            foreach ($chapterOrder as $chapterId) {
                $hRows = (new Query())->select(['h.id as hierarchy_id', 'h.topic_id', 'h.subject_id', 'h.chapter_id'])->from(Hierarchy::tableName() . ' h')->where(['h.subject_id' => $subjectId, 'h.chapter_id' => $chapterId])->andWhere(['IS NOT', 'h.topic_id', null])->orderBy(['h.topic_id' => SORT_ASC])->all();
                foreach ($hRows as $hr) {
                    $orderedHierarchies[] = ['hierarchy_id' => intval($hr['hierarchy_id']), 'subject_id' => intval($hr['subject_id']), 'chapter_id' => intval($hr['chapter_id']), 'topic_id' => intval($hr['topic_id']),];
                }
            }
        }
        if (empty($orderedHierarchies)) {
            $fallback = (new Query())->select(['h.id as hierarchy_id', 'h.subject_id', 'h.chapter_id', 'h.topic_id'])->from(Hierarchy::tableName() . ' h')->where(['IS NOT', 'h.topic_id', null])->orderBy(['h.subject_id' => SORT_ASC, 'h.chapter_id' => SORT_ASC, 'h.topic_id' => SORT_ASC])->all();
            foreach ($fallback as $f) {
                $orderedHierarchies[] = ['hierarchy_id' => intval($f['hierarchy_id']), 'subject_id' => intval($f['subject_id']), 'chapter_id' => intval($f['chapter_id']), 'topic_id' => intval($f['topic_id']),];
            }
        }

        $planAssignedByTopic = $assignedPerHierarchy;

        self::generateWeekSequential(
            $plan,
            $today,
            $needed,
            $skippedTotalMcqs,
            $user,
            $orderedHierarchies,
            $planAssignedByTopic,
            $skippedPoolByTopic
        );
    }

    protected static function generateWeekSequential(
        StudyPlans $plan,
        string $startDate,
        int $needed,
        int $skippedTotalMcqs,
        Users $user,
        array $orderedHierarchies,
        array &$planAssignedByTopic,
        array &$skippedPoolByTopic
    ) {
        if ($needed <= 0)
            return;

        $tentEnd = date('Y-m-d', strtotime($startDate . ' +' . $needed . ' days'));
        if (strtotime($tentEnd) > strtotime($plan->exam_date))
            $tentEnd = $plan->exam_date;

        $daysToCreate = (int) ((strtotime($tentEnd) - strtotime($startDate)) / 86400) + 1;
        if ($daysToCreate <= 0)
            return;

        // compute how many skipped mcqs to add per day (evenly split)
        $extraPerDayBase = intdiv($skippedTotalMcqs, max(1, $daysToCreate));
        $remainder = $skippedTotalMcqs - ($extraPerDayBase * $daysToCreate);

        // --- FIX START ---

        // Get the max existing day_number so we can continue sequence properly
        $maxDayNumber = (int) (new Query())
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->max('day_number');
        if ($maxDayNumber < 0)
            $maxDayNumber = 0;

        // Build a global assigned MCQ map for exclusion
        $globalExclude = [];
        $allAssignedRows = (new Query())
            ->select(['mcq_ids'])
            ->from(StudyPlanDaySubjects::tableName())
            ->where([
                'study_plan_day_id' => (new Query())
                    ->select('id')
                    ->from(StudyPlanDays::tableName())
                    ->where(['study_plan_id' => $plan->id])
            ])
            ->all();

        // Yii::debug($allAssignedRows);

        foreach ($allAssignedRows as $r) {
            if (empty($r['mcq_ids']))
                continue;

            $decoded = json_decode($r['mcq_ids'], true);

            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            if (!is_array($decoded))
                continue;

            foreach ($decoded as $mid) {
                $globalExclude[(int) $mid] = true;
            }
        }

        Yii::debug($globalExclude);


        $cursor = new \DateTime($startDate);
        $created = 0;

        while ($created < $daysToCreate && $cursor <= new \DateTime($plan->exam_date)) {
            $date = $cursor->format('Y-m-d');

            $existing = StudyPlanDays::findOne(['study_plan_id' => $plan->id, 'plan_date' => $date]);
            if ($existing) {
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            // --- FIX: proper continuous day_number ---
            $dayNumber = $maxDayNumber + $created + 1;
            // -----------------------------------------

            $day = new StudyPlanDays([
                'study_plan_id' => $plan->id,
                'day_number' => $dayNumber,
                'plan_date' => $date,
                'is_mock_exam' => 0,
                'status' => 'upcoming',
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);

            if (!$day->save(false)) {
                Yii::error("Failed to save StudyPlanDay for plan {$plan->id} on {$date}: " . print_r($day->errors, true));
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            $daysToExam = (int) ((strtotime($plan->exam_date) - strtotime($date)) / 86400);

            if ($daysToExam === 10 || $daysToExam === 1) {
                $day->is_mock_exam = 1;
                $day->new_mcqs = 0;
                $day->review_mcqs = 0;
                $day->save(false);
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            if ($daysToExam < 10 && $daysToExam >= 0) {
                $day->is_mock_exam = 0;
                $day->new_mcqs = 0;
                $day->review_mcqs = null;
                $day->save(false);
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            // normal day
            $base = (int) $plan->mcqs_per_day;
            $extra = $extraPerDayBase + ($remainder > 0 ? 1 : 0);
            if ($remainder > 0)
                $remainder--;

            // pass the global exclude list for MCQ allocation
            self::allocateSequentialDayFillingQuota(
                $day,
                $user,
                $plan,
                $base,
                $extra,
                $orderedHierarchies,
                $planAssignedByTopic,
                $skippedPoolByTopic,
                array_keys($globalExclude)
            );

            // update exclude with what was used this day
            if (isset($day->assigned_mcqs) && is_array($day->assigned_mcqs)) {
                foreach ($day->assigned_mcqs as $mid) {
                    $globalExclude[$mid] = true;
                }
            }

            $cursor->modify('+1 day');
            $created++;
        }
    }


    protected static function allocateSequentialDayFillingQuota(
        StudyPlanDays $day,
        Users $user,
        StudyPlans $plan,
        int $baseQuota,
        int $extraFromSkipped,
        array $orderedHierarchies,
        array &$planAssignedByTopic,
        array &$skippedPoolByTopic,
        $globalExclude
    ) {
        $allocatedNew = 0;
        $allocatedFromSkipped = 0;

        // ---------- PHASE 1: allocate the base quota from fresh/new MCQs ----------
        $remaining = $baseQuota;
        foreach ($orderedHierarchies as $oh) {
            if ($remaining <= 0)
                break;

            $hierId = intval($oh['hierarchy_id']);
            $topicId = intval($oh['topic_id']);
            $subjectId = intval($oh['subject_id']);
            $chapterId = intval($oh['chapter_id']);

            if (!isset($planAssignedByTopic[$topicId]))
                $planAssignedByTopic[$topicId] = [];

            $totalInHierarchy = (int) (new Query())
                ->from(Mcqs::tableName())
                ->where(['hierarchy_id' => $hierId])
                ->count();

            if ($totalInHierarchy <= 0)
                continue;

            $alreadyAssignedCount = count($planAssignedByTopic[$topicId]);
            $available = max(0, $totalInHierarchy - $alreadyAssignedCount);
            if ($available <= 0)
                continue;

            $take = min($available, $remaining);

            $q = (new Query())->select('id')->from(Mcqs::tableName())->where(['hierarchy_id' => $hierId]);
            if (!empty($planAssignedByTopic[$topicId])) {
                $exclude = array_keys($planAssignedByTopic[$topicId]);
                $q->andWhere(['not in', 'id', $exclude]);
            }
            if (!empty($globalExclude)) {
                $q->andWhere(['not in', 'id', $globalExclude]);
            }
            $ids = $q->orderBy(['id' => SORT_ASC])->limit($take)->column();

            if (empty($ids))
                continue;

            $sp = new StudyPlanDaySubjects([
                'study_plan_day_id' => $day->id,
                'subject_id' => $subjectId,
                'chapter_id' => $chapterId,
                'topic_id' => $topicId,
                'allocated_mcqs' => count($ids),
                'mcq_ids' => json_encode(array_values($ids)),
                'type' => 'new_content',
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);
            if ($sp->save(false)) {
                foreach ($ids as $mid)
                    $planAssignedByTopic[$topicId][intval($mid)] = true;

                $remaining -= count($ids);
                $allocatedNew += count($ids);
            } else {
                Yii::error("Failed to save StudyPlanDaySubjects sequential (topic {$topicId}): " . print_r($sp->errors, true));
            }
        }

        // ---------- PHASE 2: allocate the extra quota from skipped pool ----------
        $extraRemaining = $extraFromSkipped;
        // try to consume skipped pool in the same subject/topic order for predictability
        foreach ($orderedHierarchies as $oh) {
            if ($extraRemaining <= 0)
                break;

            $topicId = intval($oh['topic_id']);
            $subjectId = intval($oh['subject_id']);
            $chapterId = intval($oh['chapter_id']);

            if (empty($skippedPoolByTopic[$topicId]))
                continue;

            // pop IDs from skipped pool for this topic
            while ($extraRemaining > 0 && !empty($skippedPoolByTopic[$topicId])) {
                $id = array_shift($skippedPoolByTopic[$topicId]); // use array_shift to consume
                $id = intval($id);
                // ensure we do not duplicate if the same MCQ already used as new content
                if (isset($planAssignedByTopic[$topicId]) && isset($planAssignedByTopic[$topicId][$id])) {
                    continue;
                }

                // create or append a StudyPlanDaySubjects row for skipped content
                // check if there is already a skipped-type row for this topic on this day
                $existingSp = (new Query())
                    ->from(StudyPlanDaySubjects::tableName())
                    ->where(['study_plan_day_id' => $day->id, 'topic_id' => $topicId, 'type' => 'skipped_content'])
                    ->one();

                if ($existingSp) {
                    // append id to mcq_ids
                    $currentIds = @json_decode($existingSp['mcq_ids'], true);
                    if (!is_array($currentIds))
                        $currentIds = [];
                    $currentIds[] = $id;
                    (new Query())->createCommand()->update(
                        StudyPlanDaySubjects::tableName(),
                        ['mcq_ids' => json_encode($currentIds), 'allocated_mcqs' => count($currentIds), 'updated_at' => new Expression('NOW()')],
                        ['id' => $existingSp['id']]
                    )->execute();
                } else {
                    $sp = new StudyPlanDaySubjects([
                        'study_plan_day_id' => $day->id,
                        'subject_id' => $subjectId,
                        'chapter_id' => $chapterId,
                        'topic_id' => $topicId,
                        'allocated_mcqs' => 1,
                        'mcq_ids' => json_encode([$id]),
                        'type' => 'skipped_content',
                        'created_at' => new Expression('NOW()'),
                        'updated_at' => new Expression('NOW()'),
                    ]);
                    if (!$sp->save(false)) {
                        Yii::error("Failed to save StudyPlanDaySubjects (skipped content topic {$topicId}): " . print_r($sp->errors, true));
                        continue;
                    }
                }

                // mark as used globally to avoid duplication
                if (!isset($planAssignedByTopic[$topicId]))
                    $planAssignedByTopic[$topicId] = [];
                $planAssignedByTopic[$topicId][$id] = true;

                $extraRemaining--;
                $allocatedFromSkipped++;
            }
        }

        // Save totals
        $day->new_mcqs = $allocatedNew;
        $day->review_mcqs = $allocatedFromSkipped;
        $day->save(false);
    }


    protected static function calculateTotalCurriculumMcqs(int $specialtyId): int
    {
        $relevantSubjectIds = (new Query())
            ->select('subject_id')
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $specialtyId])
            ->column();

        if (empty($relevantSubjectIds))
            return 0;

        return (int) (new Query())
            ->from(Mcqs::tableName())
            ->innerJoin(Hierarchy::tableName(), 'mcqs.hierarchy_id = hierarchy.id')
            ->where(['hierarchy.subject_id' => $relevantSubjectIds])
            ->count();
    }
    protected static function calculateDayNumber(string $startDate, string $currentDate): int
    {
        return (int) ((strtotime($currentDate) - strtotime($startDate)) / 86400) + 1;
    }
}
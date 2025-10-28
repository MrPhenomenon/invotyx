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
    /**
     * Add to StudyPlanGenerator (replace/augment weekly generation logic accordingly)
     */

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
        } else {
            $plan->mcqs_per_day = $mcqsPerDay;
            $plan->exam_date = $user->expected_exam_date;
            $plan->total_capacity = $totalAvailable;
            $plan->updated_at = new Expression('NOW()');
            $plan->save(false);
        }

        StudyPlanDays::updateAll(
            ['status' => 'skipped', 'updated_at' => new Expression('NOW()')],
            ['and', ['study_plan_id' => $plan->id], ['<', 'plan_date', $today], ['!=', 'status', 'completed']]
        );
        StudyPlanDays::updateAll(
            ['status' => 'pending', 'updated_at' => new Expression('NOW()')],
            ['and', ['study_plan_id' => $plan->id], ['plan_date' => $today], ['!=', 'status', 'completed']]
        );

        self::ensureDayLite($plan, $today);

        $existingAheadCount = (int) (new Query())
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->andWhere(['>=', 'plan_date', $today])
            ->count();

        $needed = max(0, 7 - $existingAheadCount);
        if ($needed <= 0)
            return;

        $latestAheadDate = (new Query())
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->andWhere(['>=', 'plan_date', $today])
            ->max('plan_date');

        $startGenerationDate = $latestAheadDate ? date('Y-m-d', strtotime($latestAheadDate . ' +1 day')) : $today;

        $prevWindowStart = date('Y-m-d', strtotime($startGenerationDate . ' -7 days'));
        $prevWindowEnd = date('Y-m-d', strtotime($startGenerationDate . ' -1 day'));

        $skippedRows = (new Query())
            ->from(StudyPlanDays::tableName())
            ->select(['new_mcqs', 'review_mcqs'])
            ->where(['study_plan_id' => $plan->id])
            ->andWhere(['between', 'plan_date', $prevWindowStart, $prevWindowEnd])
            ->andWhere(['status' => 'skipped'])
            ->all();

        $skippedTotalMcqs = 0;
        foreach ($skippedRows as $r) {
            $skippedTotalMcqs += intval($r['new_mcqs']) + intval($r['review_mcqs']);
        }

        // PRELOAD PLAN-LEVEL ALREADY-ASSIGNED MCQ IDS (by hierarchy_id)
        // Get plan day ids
        $planDayIds = (new Query())
            ->select('id')
            ->from(StudyPlanDays::tableName())
            ->where(['study_plan_id' => $plan->id])
            ->column();

        $assignedPerHierarchy = [];
        if (!empty($planDayIds)) {
            $rows = (new Query())
                ->select(['chapter_id', 'topic_id', 'mcq_ids', 'study_plan_day_id', 'subject_id', 'topic_id', 'chapter_id', 'study_plan_day_id', 'id'])
                ->from(StudyPlanDaySubjects::tableName())
                ->where(['in', 'study_plan_day_id', $planDayIds])
                ->all();

            foreach ($rows as $r) {
                if (empty($r['mcq_ids']))
                    continue;
                $ids = @json_decode($r['mcq_ids'], true);
                if (!is_array($ids))
                    continue;

                // We don't have direct hierarchy_id stored in study_plan_day_subjects.
                // Need to map subject->chapter->topic to hierarchy.id when needed.
                // For preload, we will group by topic_id index if hierarchy mapping varies.
                // We'll store assigned ids per topic_id to speed exclusion; later we will convert to hierarchy-level exclusions when querying.
                $topicKey = intval($r['topic_id']);
                if (!isset($assignedPerHierarchy[$topicKey]))
                    $assignedPerHierarchy[$topicKey] = [];
                foreach ($ids as $mid)
                    $assignedPerHierarchy[$topicKey][intval($mid)] = true;
            }
        }

        // Build ordered list of hierarchy rows (hierarchy.id, subject_id, chapter_id, topic_id) in the exact sequence we should iterate:
        // subject order by specialty_distribution.subject_percentage desc
        // chapter order by specialty_distribution_chapters.chapter_percentage desc
        // topic order by topic.id asc (as earlier)
        $orderedHierarchies = [];
        $subjectOrder = (new Query())
            ->select(['subject_id'])
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $plan->user_id ? $plan->user_id : $plan->id]) // fallback but we will use $user->specialty_id below
            ->orderBy(['subject_percentage' => SORT_DESC])
            ->column();

        // ensure correct subject list via user's specialty
        $subjectOrder = (new Query())
            ->select('subject_id')
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $user->specialty_id])
            ->orderBy(['subject_percentage' => SORT_DESC])
            ->column();

        foreach ($subjectOrder as $subjectId) {
            $sd = SpecialtyDistributions::findOne(['specialty_id' => $user->specialty_id, 'subject_id' => $subjectId]);
            if (!$sd)
                continue;

            $chapterOrder = (new Query())
                ->select('chapter_id')
                ->from(SpecialtyDistributionChapters::tableName())
                ->where(['specialty_distribution_id' => $sd->id])
                ->orderBy(['chapter_percentage' => SORT_DESC])
                ->column();

            foreach ($chapterOrder as $chapterId) {
                // get hierarchy rows matching this subject+chapter (with topic)
                $hRows = (new Query())
                    ->select(['h.id as hierarchy_id', 'h.topic_id', 'h.subject_id', 'h.chapter_id'])
                    ->from(Hierarchy::tableName() . ' h')
                    ->where(['h.subject_id' => $subjectId, 'h.chapter_id' => $chapterId])
                    ->andWhere(['IS NOT', 'h.topic_id', null])
                    ->orderBy(['h.topic_id' => SORT_ASC])
                    ->all();

                foreach ($hRows as $hr) {
                    $orderedHierarchies[] = [
                        'hierarchy_id' => intval($hr['hierarchy_id']),
                        'subject_id' => intval($hr['subject_id']),
                        'chapter_id' => intval($hr['chapter_id']),
                        'topic_id' => intval($hr['topic_id']),
                    ];
                }
            }
        }

        // If the specialty distribution misses some hierarchies (edge-case), append remaining hierarchies for completeness
        if (empty($orderedHierarchies)) {
            $fallback = (new Query())
                ->select(['h.id as hierarchy_id', 'h.subject_id', 'h.chapter_id', 'h.topic_id'])
                ->from(Hierarchy::tableName() . ' h')
                ->where(['IS NOT', 'h.topic_id', null])
                ->orderBy(['h.subject_id' => SORT_ASC, 'h.chapter_id' => SORT_ASC, 'h.topic_id' => SORT_ASC])
                ->all();
            foreach ($fallback as $f) {
                $orderedHierarchies[] = [
                    'hierarchy_id' => intval($f['hierarchy_id']),
                    'subject_id' => intval($f['subject_id']),
                    'chapter_id' => intval($f['chapter_id']),
                    'topic_id' => intval($f['topic_id']),
                ];
            }
        }

        // We'll keep an in-memory per-topic assigned set that includes DB assigned plus new in-run assignments.
        // $planAssignedByTopic[topic_id] = [mcqId => true,...]
        $planAssignedByTopic = $assignedPerHierarchy; // keyed by topic_id

        // Now generate days while respecting exam_date and distributing skippedTotalMcqs
        self::generateWeekSequential(
            $plan,
            $startGenerationDate,
            $needed,
            $skippedTotalMcqs,
            $user,
            $orderedHierarchies,
            $planAssignedByTopic
        );
    }

    /**
     * generateWeekSequential: generates $needed days from $startDate using $orderedHierarchies order
     * $planAssignedByTopic is passed by reference and will be updated with newly assigned ids
     */
    protected static function generateWeekSequential(
        StudyPlans $plan,
        string $startDate,
        int $needed,
        int $skippedTotalMcqs,
        Users $user,
        array $orderedHierarchies,
        array &$planAssignedByTopic
    ) {
        if ($needed <= 0)
            return;

        $tentEnd = date('Y-m-d', strtotime($startDate . ' +' . ($needed - 1) . ' days'));
        if (strtotime($tentEnd) > strtotime($plan->exam_date))
            $tentEnd = $plan->exam_date;

        $daysToCreate = (int) ((strtotime($tentEnd) - strtotime($startDate)) / 86400) + 1;
        if ($daysToCreate <= 0)
            return;

        // Distribute skippedTotal evenly across daysToCreate
        $extraPerDayBase = intdiv($skippedTotalMcqs, max(1, $daysToCreate));
        $remainder = $skippedTotalMcqs - ($extraPerDayBase * $daysToCreate);

        // Precompute a mapping from hierarchy_id -> topic_id (for quick lookups)
        $hierarchyToTopic = [];
        foreach ($orderedHierarchies as $oh) {
            $hierarchyToTopic[intval($oh['hierarchy_id'])] = intval($oh['topic_id']);
        }

        $cursor = new \DateTime($startDate);
        $created = 0;

        // For performance: prebuild a map from topic_id -> hierarchy_ids (most times one-to-one)
        $topicToHierarchy = [];
        foreach ($orderedHierarchies as $oh) {
            $topicToHierarchy[intval($oh['topic_id'])][] = intval($oh['hierarchy_id']);
        }

        while ($created < $daysToCreate && $cursor <= new \DateTime($plan->exam_date)) {
            $date = $cursor->format('Y-m-d');

            // skip if day exists
            $existing = StudyPlanDays::findOne(['study_plan_id' => $plan->id, 'plan_date' => $date]);
            if ($existing) {
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            $day = new StudyPlanDays([
                'study_plan_id' => $plan->id,
                'day_number' => self::calculateDayNumber($plan->start_date, $date),
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
                // Mock day
                $day->is_mock_exam = 1;
                $day->new_mcqs = 0;
                $day->review_mcqs = 0;
                $day->save(false);
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            if ($daysToExam < 10 && $daysToExam >= 0) {
                // Review placeholder: set new_mcqs=0 and review_mcqs=null (you asked null)
                $day->is_mock_exam = 0;
                $day->new_mcqs = 0;
                $day->review_mcqs = null;
                $day->save(false);
                $cursor->modify('+1 day');
                $created++;
                continue;
            }

            // normal day
            $base = intval($plan->mcqs_per_day);
            $extra = $extraPerDayBase + ($remainder > 0 ? 1 : 0);
            if ($remainder > 0)
                $remainder--;
            $dailyTarget = max(0, $base + $extra);

            // allocate sequentially using orderedHierarchies and respecting planAssignedByTopic (which contains DB-assigned + this-run assigned)
            self::allocateSequentialDayFillingQuota(
                $day,
                $user,
                $plan,
                $dailyTarget,
                $orderedHierarchies,
                $planAssignedByTopic,
                $topicToHierarchy
            );

            $cursor->modify('+1 day');
            $created++;
        }
    }

    /**
     * Core allocator:
     * - Iterates orderedHierarchies sequentially and picks unassigned MCQs.
     * - Uses $planAssignedByTopic to exclude ids already assigned within this plan (DB + in-run).
     * - Continues across hierarchies until $dailyTarget satisfied or curriculum exhausted.
     *
     * $planAssignedByTopic is passed by reference and updated with newly assigned mcq ids
     */
    protected static function allocateSequentialDayFillingQuota(
        StudyPlanDays $day,
        Users $user,
        StudyPlans $plan,
        int $dailyTarget,
        array $orderedHierarchies,
        array &$planAssignedByTopic,
        array $topicToHierarchy
    ) {
        $remaining = $dailyTarget;
        $totalAllocated = 0;

        // For quicker DB access, we will use orderedHierarchies as the sequence.
        // We must exclude already assigned mcq IDs for that topic (DB + in-memory).
        foreach ($orderedHierarchies as $oh) {
            if ($remaining <= 0)
                break;

            $hierId = intval($oh['hierarchy_id']);
            $subjectId = intval($oh['subject_id']);
            $chapterId = intval($oh['chapter_id']);
            $topicId = intval($oh['topic_id']);

            // Ensure $planAssignedByTopic[topicId] exists as map for quick 'in' checks
            if (!isset($planAssignedByTopic[$topicId]))
                $planAssignedByTopic[$topicId] = [];

            // Count total MCQs in this hierarchy
            $totalInHierarchy = (int) (new Query())
                ->from(Mcqs::tableName())
                ->where(['hierarchy_id' => $hierId])
                ->count();

            // If zero, skip
            if ($totalInHierarchy <= 0)
                continue;

            // already assigned for this topic in-plan
            $alreadyAssignedCount = count($planAssignedByTopic[$topicId]);
            $available = max(0, $totalInHierarchy - $alreadyAssignedCount);
            if ($available <= 0)
                continue;

            // take how many we can from this hierarchy to fill remaining
            $take = min($available, $remaining);

            // fetch next $take ids excluding already assigned
            $q = (new Query())->select('id')->from(Mcqs::tableName())->where(['hierarchy_id' => $hierId]);
            if (!empty($planAssignedByTopic[$topicId])) {
                $exclude = array_keys($planAssignedByTopic[$topicId]);
                $q->andWhere(['not in', 'id', $exclude]);
            }
            $ids = $q->orderBy(['id' => SORT_ASC])->limit($take)->column();

            if (empty($ids))
                continue;

            // save StudyPlanDaySubjects row
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
                // update in-memory assigned map for this topic
                foreach ($ids as $mid)
                    $planAssignedByTopic[$topicId][intval($mid)] = true;

                $remaining -= count($ids);
                $totalAllocated += count($ids);
            } else {
                Yii::error("Failed to save StudyPlanDaySubjects sequential (topic {$topicId}): " . print_r($sp->errors, true));
            }
        }

        $day->new_mcqs = $totalAllocated;
        $day->review_mcqs = 0;
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

    protected static function ensureDayLite(StudyPlans $plan, string $date)
    {
        $existing = StudyPlanDays::findOne(['study_plan_id' => $plan->id, 'plan_date' => $date]);
        if ($existing) {
            return $existing;
        }

        $day = new StudyPlanDays([
            'study_plan_id' => $plan->id,
            'day_number' => self::calculateDayNumber($plan->start_date, $date),
            'plan_date' => $date,
            'is_mock_exam' => 0,
            'status' => 'pending',
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);
        $day->save(false);
        return $day;
    }
}
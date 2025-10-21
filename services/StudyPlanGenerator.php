<?php

namespace app\services;

use Yii;
use app\models\Users;
use app\models\StudyPlans;
use app\models\StudyPlanDays;
use app\models\StudyPlanDaySubjects;
use app\models\SpecialtyDistributions;
use app\models\SpecialtyDistributionChapters;
use app\models\Subjects;
use app\models\Chapters;
use app\models\Topics;
use app\models\Hierarchy;
use app\models\Mcqs;
use yii\db\Expression;
use yii\db\Query;

class StudyPlanGenerator
{
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SKIPPED = 'skipped';
    const STATUS_UPCOMING = 'upcoming';
    const STATUS_IN_PROGRESS = 'in_progress';

    public static function ensurePlan(Users $user)
    {
        $today = date('Y-m-d');
        $mcqsPerDay = intval($user->mcqs_per_day) === 0 ? 180 : intval($user->mcqs_per_day);
        $totalAvailableCurriculumMcqs = self::calculateTotalCurriculumMcqs($user->specialty_id);

        $plan = StudyPlans::findOne(['user_id' => $user->id]);
        if (!$plan) {
            $plan = new StudyPlans([
                'user_id' => $user->id,
                'start_date' => $today,
                'exam_date' => $user->expected_exam_date,
                'mcqs_per_day' => $mcqsPerDay,
                'total_capacity' => $totalAvailableCurriculumMcqs,
                'status' => 'active',
                'last_generated_week' => $today,
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);
            if (!$plan->save(false)) {
                Yii::error("Failed to save StudyPlan for user {$user->id}: " . print_r($plan->errors, true));
                return;
            }
            self::generatePlanPeriod($plan, $plan->start_date, $plan->exam_date, $user, true);
        } else {
            $plan->mcqs_per_day = $mcqsPerDay;
            $plan->exam_date = $user->expected_exam_date;
            $plan->total_capacity = $totalAvailableCurriculumMcqs;
            $plan->updated_at = new Expression('NOW()');
            $plan->save(false);

            $lastGeneratedWeek = new \DateTime($plan->last_generated_week);
            $nextRegenerationDate = (clone $lastGeneratedWeek)->modify('+1 week');

            if ($today >= $nextRegenerationDate->format('Y-m-d')) {
                $startDateForGeneration = (new \DateTime($plan->last_generated_week))->modify('+1 day')->format('Y-m-d');
                $endDateForGeneration = (new \DateTime($startDateForGeneration))->modify('+6 days')->format('Y-m-d');
                $endDateForGeneration = min($endDateForGeneration, $plan->exam_date);

                self::generatePlanPeriod($plan, $startDateForGeneration, $endDateForGeneration, $user, false);
                $plan->last_generated_week = $today;
                $plan->save(false);
            }

            self::ensureDay($plan, $today, $user);
            self::updatePastDayStatuses($plan, $today);
        }
    }


    public static function generateFullStudyPlan(Users $user): StudyPlans
    {
        $mcqsPerDay = intval($user->mcqs_per_day) === 0 ? 180 : intval($user->mcqs_per_day);
        $totalAvailableCurriculumMcqs = self::calculateTotalCurriculumMcqs($user->specialty_id);
        $today = date('Y-m-d');

        $plan = StudyPlans::findOne(['user_id' => $user->id]);
        $planStartDate = date('Y-m-d');
        if ($plan) {
            $planStartDate = $plan->start_date;
            $plan->mcqs_per_day = $mcqsPerDay;
            $plan->exam_date = $user->expected_exam_date;
            $plan->total_capacity = $totalAvailableCurriculumMcqs;
            $plan->last_generated_week = $today;
            $plan->updated_at = new Expression('NOW()');
            $plan->save(false);
        } else {
            $plan = new StudyPlans([
                'user_id' => $user->id,
                'start_date' => $planStartDate,
                'exam_date' => $user->expected_exam_date,
                'mcqs_per_day' => $mcqsPerDay,
                'total_capacity' => $totalAvailableCurriculumMcqs,
                'status' => 'active',
                'last_generated_week' => $today,
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);
            if (!$plan->save(false)) {
                Yii::error("Failed to save StudyPlan for user {$user->id} during full generation: " . print_r($plan->errors, true));
                throw new \Exception("Could not create study plan.");
            }
        }
        self::generatePlanPeriod($plan, $plan->start_date, $plan->exam_date, $user, true);
        return $plan;
    }


    protected static function generatePlanPeriod(StudyPlans $plan, string $startDate, string $endDate, Users $user, bool $isFullRebuild = false)
    {
        $today = date('Y-m-d');
        $accumulatedSkippedMcqs = 0;

        if ($isFullRebuild) {
            StudyPlanDaySubjects::deleteAll(['study_plan_day_id' => (new Query())->select('id')->from(StudyPlanDays::tableName())->where(['study_plan_id' => $plan->id])->andWhere(['>=', 'plan_date', $today])->andWhere(['!=', 'status', self::STATUS_COMPLETED])]);
            StudyPlanDays::deleteAll(['study_plan_id' => $plan->id])->andWhere(['>=', 'plan_date', $today])->andWhere(['!=', 'status', self::STATUS_COMPLETED]);
        } else {
            StudyPlanDaySubjects::deleteAll(['study_plan_day_id' => (new Query())->select('id')->from(StudyPlanDays::tableName())->where(['study_plan_id' => $plan->id])->andWhere(['between', 'plan_date', $startDate, $endDate])->andWhere(['!=', 'status', self::STATUS_COMPLETED])]);
            StudyPlanDays::deleteAll(['study_plan_id' => $plan->id])->andWhere(['between', 'plan_date', $startDate, $endDate])->andWhere(['!=', 'status', self::STATUS_COMPLETED]);

            $skippedDays = StudyPlanDays::find()
                ->where(['study_plan_id' => $plan->id, 'status' => self::STATUS_SKIPPED])
                ->andWhere(['<', 'plan_date', $startDate])
                ->all();

            foreach ($skippedDays as $skippedDay) {
                $accumulatedSkippedMcqs += $skippedDay->new_mcqs;
            }
        }

        $currentDate = new \DateTime($startDate);
        $loopEndDate = new \DateTime($endDate);

        while ($currentDate <= $loopEndDate) {
            $dateString = $currentDate->format('Y-m-d');
            self::ensureDay($plan, $dateString, $user, null, $accumulatedSkippedMcqs);
            $currentDate->modify('+1 day');
        }
    }


    protected static function updatePastDayStatuses(StudyPlans $plan, string $currentDate)
    {
        $pastDays = StudyPlanDays::find()
            ->where(['study_plan_id' => $plan->id])
            ->andWhere(['<', 'plan_date', $currentDate])
            ->andWhere(['IN', 'status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_UPCOMING]])
            ->all();

        foreach ($pastDays as $day) {
            $day->status = self::STATUS_SKIPPED;
            $day->updated_at = new Expression('NOW()');
            if (!$day->save(false)) {
                Yii::error("Failed to update status for past day {$day->id}: " . print_r($day->errors, true));
            }
        }
    }

    protected static function calculateTotalCurriculumMcqs(int $specialtyId): int
    {
        $relevantSubjectIds = (new Query())
            ->select('subject_id')
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $specialtyId])
            ->column();

        if (empty($relevantSubjectIds)) {
            return 0;
        }

        return (int) (new Query())
            ->from(Mcqs::tableName())
            ->innerJoin(Hierarchy::tableName(), 'mcqs.hierarchy_id = hierarchy.id')
            ->where(['hierarchy.subject_id' => $relevantSubjectIds])
            ->count();
    }

    protected static function ensureDay(StudyPlans $plan, string $date, Users $user, string $todayString = null, int $accumulatedSkippedMcqs = 0): StudyPlanDays
    {
        if ($todayString === null) {
            $todayString = date('Y-m-d');
        }

        $existing = StudyPlanDays::findOne([
            'study_plan_id' => $plan->id,
            'plan_date' => $date,
        ]);

        if ($existing) {
            if ($date === $todayString && ($existing->status === self::STATUS_UPCOMING || $existing->status === self::STATUS_PENDING)) {
                $existing->status = self::STATUS_PENDING;
                $existing->updated_at = new Expression('NOW()');
                $existing->save(false);
            }
            return $existing;
        }

        $day = new StudyPlanDays([
            'study_plan_id' => $plan->id,
            'day_number' => self::calculateDayNumber($plan->start_date, $date),
            'plan_date' => $date,
            'is_mock_exam' => 0,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);

        if ($date === $todayString) {
            $day->status = self::STATUS_PENDING;
        } elseif ($date > $todayString) {
            $day->status = self::STATUS_UPCOMING;
        } else {
            $day->status = self::STATUS_SKIPPED;
        }

        if (!$day->save(false)) {
            Yii::error("Failed to save StudyPlanDay for plan {$plan->id} on {$date}: " . print_r($day->errors, true));
            throw new \Exception("Failed to save StudyPlanDay.");
        }

        $daysToExam = (int) ((strtotime($plan->exam_date) - strtotime($date)) / 86400);

        $dailyTargetWithSkipped = intval($plan->mcqs_per_day);
        $daysInNextWeek = 7;
        if ($accumulatedSkippedMcqs > 0) {
            $skippedPerDay = ceil($accumulatedSkippedMcqs / $daysInNextWeek);
            $dailyTargetWithSkipped += $skippedPerDay;
        }

        if ($daysToExam < 10 && $daysToExam >= 0) {
            self::allocateRevisionDay($day, $user, $dailyTargetWithSkipped);
        } else {
            self::allocateSequentialCoverageDay($day, $user, $dailyTargetWithSkipped);
        }

        return $day;
    }

    protected static function calculateDayNumber(string $startDate, string $currentDate): int
    {
        return (int) ((strtotime($currentDate) - strtotime($startDate)) / 86400) + 1;
    }

    protected static function allocateSequentialCoverageDay(StudyPlanDays $day, Users $user, int $dailyTarget)
    {
        $totalAllocatedToday = 0;
        // --- Correction 1: Pass $plan ---
        // The $plan variable needs to be passed to fillDailyTargetFromChapter.
        // It's available as an argument to this function, so we just need to pass it down.
        $plan = $day->studyPlan; // Retrieve the StudyPlans model from the StudyPlanDays model

        $orderedSubjectIds = self::getOrderedSubjectIdsByDistribution($user->specialty_id);

        foreach ($orderedSubjectIds as $subjectId) {
            if ($totalAllocatedToday >= $dailyTarget) {
                break;
            }

            $orderedChapterIds = self::getOrderedChapterIdsByDistribution($user->specialty_id, $subjectId);

            foreach ($orderedChapterIds as $chapterId) {
                if ($totalAllocatedToday >= $dailyTarget) {
                    break;
                }

                $needed = $dailyTarget - $totalAllocatedToday;

                list($allocatedFromChapter, $mcqIdsAllocated, $mcqTopicMap) = self::fillDailyTargetFromChapter($day, $plan, $subjectId, $chapterId, $needed);

                if (!empty($mcqIdsAllocated)) {
                    foreach ($mcqTopicMap as $mapTopicId => $mappedMcqIds) {
                        if (empty($mappedMcqIds))
                            continue;

                        $sp = new StudyPlanDaySubjects([
                            'study_plan_day_id' => $day->id,
                            'subject_id' => $subjectId,
                            'chapter_id' => $chapterId,
                            'topic_id' => $mapTopicId,
                            'allocated_mcqs' => count($mappedMcqIds),
                            'mcq_ids' => json_encode($mappedMcqIds),
                            'created_at' => new Expression('NOW()'),
                            'updated_at' => new Expression('NOW()'),
                        ]);
                        if (!$sp->save(false)) {
                            Yii::error("Failed to save StudyPlanDaySubjects for topic {$mapTopicId} on day {$day->id}: " . print_r($sp->errors, true));
                        }
                    }

                    $totalAllocatedToday += $allocatedFromChapter;
                }
            }
        }

        $day->new_mcqs = $totalAllocatedToday;
        $day->review_mcqs = 0;
        $day->save(false);
    }

    protected static function getOrderedSubjectIdsByDistribution(int $specialtyId): array
    {
        return (new Query())
            ->select('subject_id')
            ->from(SpecialtyDistributions::tableName())
            ->where(['specialty_id' => $specialtyId])
            ->orderBy(['subject_percentage' => SORT_DESC])
            ->column();
    }

    protected static function getOrderedChapterIdsByDistribution(int $specialtyId, int $subjectId): array
    {
        $sd = SpecialtyDistributions::findOne(['specialty_id' => $specialtyId, 'subject_id' => $subjectId]);
        if (!$sd) {
            return [];
        }

        return (new Query())
            ->select('chapter_id')
            ->from(SpecialtyDistributionChapters::tableName())
            ->where(['specialty_distribution_id' => $sd->id])
            ->orderBy(['chapter_percentage' => SORT_DESC])
            ->column();
    }

    protected static function fillDailyTargetFromChapter(StudyPlanDays $day, StudyPlans $plan, int $subjectId, int $chapterId, int $targetToFill): array
    {
        $allocatedInThisChapter = 0;
        $mcqIdsAllocatedOverall = [];
        $mcqTopicMap = [];

        if ($targetToFill <= 0) {
            return [0, [], []];
        }

        $eligibleTopicsAndHierarchyIds = (new Query())
            ->select([
                'h.id AS hierarchy_id',
                't.id AS topic_id',
                'GROUP_CONCAT(mcqs.id ORDER BY mcqs.id) AS mcq_ids_available'
            ])
            ->from(['h' => Hierarchy::tableName()])
            ->innerJoin(['t' => Topics::tableName()], 't.id = h.topic_id')
            ->leftJoin(Mcqs::tableName(), 'mcqs.hierarchy_id = h.id')
            ->where(['h.subject_id' => $subjectId, 'h.chapter_id' => $chapterId])
            ->andWhere(['IS NOT', 'h.topic_id', null])
            ->andWhere(['IS NOT', 'mcqs.id', null])
            ->groupBy(['h.id', 't.id'])
            ->orderBy(['t.id' => SORT_ASC, 'h.id' => SORT_ASC])
            ->all();

        $allPlanAllocatedMcqIds = (new Query())
            ->select('JSON_UNQUOTE(JSON_EXTRACT(mcq_ids, "$[*]")) AS mcq_id_str')
            ->from(StudyPlanDaySubjects::tableName() . ' s')
            ->innerJoin(StudyPlanDays::tableName() . ' d', 'd.id = s.study_plan_day_id')
            ->where(['d.study_plan_id' => $plan->id])
            ->andWhere(['is not', 's.mcq_ids', new Expression('NULL')])
            ->column();

        $alreadyAllocatedMcqIds = [];
        foreach ($allPlanAllocatedMcqIds as $jsonString) {

            $ids = json_decode($jsonString);
            if (is_array($ids)) {
                $alreadyAllocatedMcqIds = array_merge($alreadyAllocatedMcqIds, $ids);
            }
        }

        $alreadyAllocatedMcqIds = array_unique($alreadyAllocatedMcqIds);


        foreach ($eligibleTopicsAndHierarchyIds as $row) {
            if ($allocatedInThisChapter >= $targetToFill) {
                break;
            }

            $topicId = (int) $row['topic_id'];
            $allMcqIdsForHierarchyPath = !empty($row['mcq_ids_available']) ? explode(',', $row['mcq_ids_available']) : [];

            if (empty($allMcqIdsForHierarchyPath)) {
                continue;
            }

            $availableMcqIdsInPath = array_diff($allMcqIdsForHierarchyPath, $alreadyAllocatedMcqIds);

            if (empty($availableMcqIdsInPath)) {
                continue;
            }

            $takeCount = min(count($availableMcqIdsInPath), ($targetToFill - $allocatedInThisChapter));

            if ($takeCount > 0) {
                $mcqsToTake = array_slice($availableMcqIdsInPath, 0, $takeCount);

                $mcqIdsAllocatedOverall = array_merge($mcqIdsAllocatedOverall, $mcqsToTake);
                $allocatedInThisChapter += count($mcqsToTake);

                if (!isset($mcqTopicMap[$topicId])) {
                    $mcqTopicMap[$topicId] = [];
                }
                $mcqTopicMap[$topicId] = array_merge($mcqTopicMap[$topicId], $mcqsToTake);

                $alreadyAllocatedMcqIds = array_merge($alreadyAllocatedMcqIds, $mcqsToTake);
            }
        }

        return [$allocatedInThisChapter, $mcqIdsAllocatedOverall, $mcqTopicMap];
    }

    protected static function allocateRevisionDay(StudyPlanDays $day, Users $user, int $perDay)
    {
        $day->new_mcqs = $perDay;
        $day->review_mcqs = 0;
        $day->save(false);

        $topSubjects = SpecialtyDistributions::find()
            ->where(['specialty_id' => $user->specialty_id])
            ->orderBy(['subject_percentage' => SORT_DESC])
            ->limit(4)
            ->all();

        $count = count($topSubjects) ?: 1;
        $share = intval(round($perDay / $count));

        $allocatedTotal = 0;
        foreach ($topSubjects as $subj) {
            $currentShare = min($share, $perDay - $allocatedTotal);

            if ($currentShare > 0) {
                // For revision, we might need to select specific MCQs for review.
                // For now, it's just a count, but if you store MCQ IDs for review, this needs modification.
                $spds = new StudyPlanDaySubjects([
                    'study_plan_day_id' => $day->id,
                    'subject_id' => $subj->subject_id,
                    'allocated_mcqs' => $currentShare,
                    'mcq_ids' => json_encode([]), // Placeholder, needs actual review MCQ IDs if implemented
                    'created_at' => new Expression('NOW()'),
                    'updated_at' => new Expression('NOW()'),
                ]);
                if ($spds->save(false)) {
                    $allocatedTotal += $currentShare;
                } else {
                    Yii::error("Failed to save StudyPlanDaySubjects for revision day (subject {$subj->subject_id}): " . print_r($spds->errors, true));
                }
            }
        }
        if ($allocatedTotal < $perDay && !empty($topSubjects)) {
            $firstSubjectSpds = StudyPlanDaySubjects::findOne([
                'study_plan_day_id' => $day->id,
                'subject_id' => $topSubjects[0]->subject_id
            ]);
            if ($firstSubjectSpds) {
                $firstSubjectSpds->allocated_mcqs += ($perDay - $allocatedTotal);
                $firstSubjectSpds->save(false);
            }
        }
    }
}
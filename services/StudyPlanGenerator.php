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

    const SPDS_TYPE_NEW_CONTENT = 'new_content';
    const SPDS_TYPE_REDISTRIBUTED_SKIPPED = 'redistributed_skipped';
    const SPDS_TYPE_REVIEW = 'review';


    public static function ensurePlan(Users $user)
    {
        $today = date('Y-m-d');
        $mcqsPerDay = intval($user->mcqs_per_day) === 0 ? 180 : intval($user->mcqs_per_day);
        $totalAvailableCurriculumMcqs = self::calculateTotalCurriculumMcqs($user->specialty_id);

        $plan = StudyPlans::findOne(['user_id' => $user->id]);
        $isNewPlan = false;

        if (!$plan) {
            $isNewPlan = true;
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
        } else {
            $plan->mcqs_per_day = $mcqsPerDay;
            $plan->exam_date = $user->expected_exam_date;
            $plan->total_capacity = $totalAvailableCurriculumMcqs;
            $plan->updated_at = new Expression('NOW()');
            $plan->save(false);
        }

        $shouldGeneratePeriod = false;
        if ($isNewPlan) {
            $shouldGeneratePeriod = true;
        } else {
            $lastGeneratedWeek = new \DateTime($plan->last_generated_week);
            $nextGenerationTriggerDate = (clone $lastGeneratedWeek)->modify('+7 days');
            
            if (new \DateTime($today) >= $nextGenerationTriggerDate) {
                $shouldGeneratePeriod = true;
            }
        }

        if ($shouldGeneratePeriod) {
            $periodStartDate = $isNewPlan ? $plan->start_date : (new \DateTime($plan->last_generated_week))->modify('+1 day')->format('Y-m-d');
            $periodEndDate = (new \DateTime($periodStartDate))->modify('+6 days')->format('Y-m-d');
            $periodEndDate = min($periodEndDate, $plan->exam_date);

            if (new \DateTime($periodStartDate) <= new \DateTime($periodEndDate)) { 
                self::generatePlanPeriod($plan, $periodStartDate, $periodEndDate, $user);
                $plan->last_generated_week = $today;
                $plan->save(false);
            }
        }

        self::ensureDay($plan, $today, $user);
        self::ensureDay($plan, (new \DateTime($today))->modify('+1 day')->format('Y-m-d'), $user);

        self::updatePastDayStatuses($plan, $today);
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


    protected static function generatePlanPeriod(StudyPlans $plan, string $periodStartDate, string $periodEndDate, Users $user, bool $isFullPlanGeneration = false)
    {
        $today = date('Y-m-d');
        $accumulatedSkippedMcqs = []; 

        $skippedDays = StudyPlanDays::find()
            ->where(['study_plan_id' => $plan->id, 'status' => self::STATUS_SKIPPED])
            ->andWhere(['<', 'plan_date', $periodStartDate])
            ->all();

        foreach ($skippedDays as $skippedDay) {
            $spdsEntries = StudyPlanDaySubjects::find()
                ->where(['study_plan_day_id' => $skippedDay->id])
                ->andWhere(['is not', 'mcq_ids', new Expression('NULL')])
                ->column();

            foreach ($spdsEntries as $jsonMcqIdString) {
                $ids = json_decode($jsonMcqIdString);
                if (is_array($ids)) {
                    $accumulatedSkippedMcqs = array_merge($accumulatedSkippedMcqs, $ids);
                }
            }
        }
        $accumulatedSkippedMcqs = array_unique($accumulatedSkippedMcqs);

        $currentDate = new \DateTime($periodStartDate);
        $loopEndDate = new \DateTime($periodEndDate);

        while ($currentDate <= $loopEndDate) {
            $dateString = $currentDate->format('Y-m-d');
            self::ensureDay($plan, $dateString, $user, $today, $accumulatedSkippedMcqs, $periodStartDate, $periodEndDate, $isFullPlanGeneration);
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

    protected static function ensureDay(StudyPlans $plan, string $date, Users $user, string $todayString = null, array $accumulatedSkippedMcqIds = [], string $generationPeriodStartDate = null, string $generationPeriodEndDate = null, bool $isFullPlanGeneration = false): StudyPlanDays
    {
        if ($todayString === null) {
            $todayString = date('Y-m-d');
        }

        $existing = StudyPlanDays::findOne([
            'study_plan_id' => $plan->id,
            'plan_date' => $date,
        ]);

        $dayToAllocate = $existing;
        $shouldReallocateContent = false;

        if ($existing) {

            if ($date === $todayString && ($existing->status === self::STATUS_UPCOMING || $existing->status === self::STATUS_PENDING) && !$existing->is_mock_exam) {
                $existing->status = self::STATUS_PENDING;
                $existing->updated_at = new Expression('NOW()');
                $existing->save(false);
            }

            if ($existing->status === self::STATUS_COMPLETED || $existing->status === self::STATUS_SKIPPED) {
                 return $existing; 
            }

            $targetDailyMcqs = intval($plan->mcqs_per_day);
            $skippedMcqsForThisDayCount = 0;

            if ($accumulatedSkippedMcqIds && $generationPeriodStartDate && $generationPeriodEndDate &&
                (new \DateTime($date) >= new \DateTime($generationPeriodStartDate)) &&
                (new \DateTime($date) <= new \DateTime($generationPeriodEndDate))) {
                
                $daysInRedistributionWindow = (new \DateTime($generationPeriodEndDate))->diff(new \DateTime($generationPeriodStartDate))->days + 1;
                if ($daysInRedistributionWindow <= 0) $daysInRedistributionWindow = 1;
                $skippedMcqsForThisDayCount = ceil(count($accumulatedSkippedMcqIds) / $daysInRedistributionWindow);
                $targetDailyMcqs += $skippedMcqsForThisDayCount;
            }

            if ($isFullPlanGeneration || $existing->new_mcqs === 0 || $existing->new_mcqs !== $targetDailyMcqs) {
                $shouldReallocateContent = true;
            }
            
        } else {
            $dayToAllocate = new StudyPlanDays([
                'study_plan_id' => $plan->id,
                'day_number' => self::calculateDayNumber($plan->start_date, $date),
                'plan_date' => $date,
                'is_mock_exam' => 0,
                'new_mcqs' => 0,
                'redistributed_skipped_mcqs' => 0,
                'created_at' => new Expression('NOW()'),
                'updated_at' => new Expression('NOW()'),
            ]);

            if ($date === $todayString) {
                $dayToAllocate->status = self::STATUS_PENDING;
            } elseif ($date > $todayString) {
                $dayToAllocate->status = self::STATUS_UPCOMING;
            } else {
                $dayToAllocate->status = self::STATUS_SKIPPED;
            }

            if (!$dayToAllocate->save(false)) {
                Yii::error("Failed to save StudyPlanDay for plan {$plan->id} on {$date} before allocation (new record): " . print_r($dayToAllocate->errors, true));
                throw new \Exception("Failed to save StudyPlanDay before allocation (new record).");
            }
            $shouldReallocateContent = true;
        }

        if ($shouldReallocateContent) {

            StudyPlanDaySubjects::deleteAll(['study_plan_day_id' => $dayToAllocate->id]);

            $daysToExam = (int) ((strtotime($plan->exam_date) - strtotime($date)) / 86400);
            $dailyTargetWithSkipped = intval($plan->mcqs_per_day);
            $mcqIdsToRedistributeToday = [];

            if ($accumulatedSkippedMcqIds && $generationPeriodStartDate && $generationPeriodEndDate &&
                (new \DateTime($date) >= new \DateTime($generationPeriodStartDate)) &&
                (new \DateTime($date) <= new \DateTime($generationPeriodEndDate))
            ) {
                $daysInRedistributionWindow = (new \DateTime($generationPeriodEndDate))->diff(new \DateTime($generationPeriodStartDate))->days + 1;
                if ($daysInRedistributionWindow <= 0) $daysInRedistributionWindow = 1;

                $skippedPerDay = ceil(count($accumulatedSkippedMcqIds) / $daysInRedistributionWindow);
                $dayToAllocate->redistributed_skipped_mcqs = $skippedPerDay;
                $dailyTargetWithSkipped += $skippedPerDay;


                $mcqIdsToRedistributeToday = array_slice($accumulatedSkippedMcqIds, 0, $skippedPerDay);

            } else {
                $dayToAllocate->redistributed_skipped_mcqs = 0;
            }

            if ($daysToExam < 10 && $daysToExam >= 0) {
                self::allocateRevisionDay($dayToAllocate, $user, $dailyTargetWithSkipped, $mcqIdsToRedistributeToday);
            } else {
                self::allocateSequentialCoverageDay($dayToAllocate, $user, $dailyTargetWithSkipped, $mcqIdsToRedistributeToday);
            }

            if (!$dayToAllocate->save(false)) {
                Yii::error("Failed to save StudyPlanDay for plan {$plan->id} on {$date} after allocation: " . print_r($dayToAllocate->errors, true));
                throw new \Exception("Failed to save StudyPlanDay after allocation.");
            }
        } else {
            if ($dayToAllocate->redistributed_skipped_mcqs !== 0) {
                $dayToAllocate->redistributed_skipped_mcqs = 0;
                $dayToAllocate->save(false);
            }
        }
        
        return $dayToAllocate;
    }

    protected static function calculateDayNumber(string $startDate, string $currentDate): int
    {
        return (int) ((strtotime($currentDate) - strtotime($startDate)) / 86400) + 1;
    }

    protected static function allocateSequentialCoverageDay(StudyPlanDays $day, Users $user, int $dailyTarget, array $mcqIdsToRedistributeToday = [])
    {
        $totalAllocatedToday = 0;
        $plan = $day->studyPlan;


        $skippedMcqsRemainingToAllocate = $mcqIdsToRedistributeToday;
        $allocatedSkippedCount = 0;

        if (!empty($skippedMcqsRemainingToAllocate)) {
            $allocatedSkippedCount = min(count($skippedMcqsRemainingToAllocate), $dailyTarget);
            $mcqsForSpds = array_slice($skippedMcqsRemainingToAllocate, 0, $allocatedSkippedCount);

            $mcqHierarchyDetails = (new Query())
                ->select(['h.subject_id', 'h.chapter_id', 'h.topic_id', 'mcqs.id'])
                ->from(Mcqs::tableName())
                ->innerJoin('hierarchy h', 'h.id = mcqs.hierarchy_id')
                ->where(['mcqs.id' => $mcqsForSpds])
                ->all();

            $groupedSkippedMcqs = [];
            foreach ($mcqHierarchyDetails as $detail) {
                $groupedSkippedMcqs[$detail['subject_id']][$detail['chapter_id']][$detail['topic_id']][] = $detail['id'];
            }

            foreach ($groupedSkippedMcqs as $subjId => $chapters) {
                foreach ($chapters as $chapId => $topics) {
                    foreach ($topics as $topId => $ids) {
                        if (empty($ids)) continue;
                        $sp = new StudyPlanDaySubjects([
                            'study_plan_day_id' => $day->id,
                            'subject_id' => $subjId,
                            'chapter_id' => $chapId,
                            'topic_id' => $topId,
                            'allocated_mcqs' => count($ids),
                            'mcq_ids' => json_encode($ids),

                            'created_at' => new Expression('NOW()'),
                            'updated_at' => new Expression('NOW()'),
                        ]);
                        if (!$sp->save(false)) {
                            Yii::error("Failed to save Redistributed Skipped SPDS for topic {$topId}: " . print_r($sp->errors, true));
                        }
                    }
                }
            }
            $totalAllocatedToday += $allocatedSkippedCount;
        }


        $remainingTarget = $dailyTarget - $totalAllocatedToday;

        if ($remainingTarget > 0) {
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
                    list($allocatedFromChapter, $mcqIdsAllocatedOverall, $mcqTopicMap) = self::fillDailyTargetFromChapter($day, $plan, $subjectId, $chapterId, $needed, $mcqIdsToRedistributeToday);

                    if (!empty($mcqIdsAllocatedOverall)) {
                        foreach ($mcqTopicMap as $mapTopicId => $mappedMcqIds) {
                            if (empty($mappedMcqIds)) continue;

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
                                Yii::error("Failed to save New Content SPDS for topic {$mapTopicId} on day {$day->id}: " . print_r($sp->errors, true));
                            }
                        }
                        $totalAllocatedToday += $allocatedFromChapter;
                    }
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

    protected static function fillDailyTargetFromChapter(StudyPlanDays $day, StudyPlans $plan, int $subjectId, int $chapterId, int $targetToFill, array $alreadyIncludedMcqIds = []): array
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


        $jsonMcqIdStrings = (new Query())
            ->select('s.mcq_ids')
            ->from(StudyPlanDaySubjects::tableName() . ' s')
            ->innerJoin(StudyPlanDays::tableName() . ' d', 'd.id = s.study_plan_day_id')
            ->where(['d.study_plan_id' => $plan->id])
            ->andWhere(['is not', 's.mcq_ids', new Expression('NULL')])
            ->column();

        $alreadyAllocatedMcqIds = [];
        foreach ($jsonMcqIdStrings as $jsonString) {
            $ids = json_decode($jsonString);
            if (is_array($ids)) {
                $alreadyAllocatedMcqIds = array_merge($alreadyAllocatedMcqIds, $ids);
            }
        }
        $alreadyAllocatedMcqIds = array_unique(array_merge($alreadyAllocatedMcqIds, $alreadyIncludedMcqIds));

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
                $alreadyAllocatedMcqIds = array_unique($alreadyAllocatedMcqIds);
            }
        }

        return [$allocatedInThisChapter, $mcqIdsAllocatedOverall, $mcqTopicMap];
    }

    protected static function allocateRevisionDay(StudyPlanDays $day, Users $user, int $perDay, array $mcqIdsToRedistributeToday = [])
    {
        $plan = $day->studyPlan;
        
        $allocatedTotal = 0;
        $mcqIdsAllocatedOverall = [];
        
        $skippedMcqsRemainingToAllocate = $mcqIdsToRedistributeToday;
        $allocatedSkippedCount = 0;

        if (!empty($skippedMcqsRemainingToAllocate)) {
            $allocatedSkippedCount = min(count($skippedMcqsRemainingToAllocate), $perDay);
            $mcqsForSpds = array_slice($skippedMcqsRemainingToAllocate, 0, $allocatedSkippedCount);

            $mcqHierarchyDetails = (new Query())
                ->select(['h.subject_id', 'h.chapter_id', 'h.topic_id', 'mcqs.id'])
                ->from(Mcqs::tableName())
                ->innerJoin('hierarchy h', 'h.id = mcqs.hierarchy_id')
                ->where(['mcqs.id' => $mcqsForSpds])
                ->all();

            $groupedSkippedMcqs = [];
            foreach ($mcqHierarchyDetails as $detail) {
                $groupedSkippedMcqs[$detail['subject_id']][$detail['chapter_id']][$detail['topic_id']][] = $detail['id'];
            }

            foreach ($groupedSkippedMcqs as $subjId => $chapters) {
                foreach ($chapters as $chapId => $topics) {
                    foreach ($topics as $topId => $ids) {
                        if (empty($ids)) continue;
                        $sp = new StudyPlanDaySubjects([
                            'study_plan_day_id' => $day->id,
                            'subject_id' => $subjId,
                            'chapter_id' => $chapId,
                            'topic_id' => $topId,
                            'allocated_mcqs' => count($ids),
                            'mcq_ids' => json_encode($ids),

                            'created_at' => new Expression('NOW()'),
                            'updated_at' => new Expression('NOW()'),
                        ]);
                        if (!$sp->save(false)) {
                            Yii::error("Failed to save Redistributed Skipped SPDS for topic {$topId} in revision day: " . print_r($sp->errors, true));
                        }
                    }
                }
            }
            $allocatedTotal += $allocatedSkippedCount;
        }
        
        $remainingTarget = $perDay - $allocatedTotal;

        if ($remainingTarget > 0) {
            $topSubjects = SpecialtyDistributions::find()
                ->where(['specialty_id' => $user->specialty_id])
                ->orderBy(['subject_percentage' => SORT_DESC])
                ->limit(4)
                ->column();

            $jsonMcqIdStrings = (new Query())
                ->select('s.mcq_ids')
                ->from(StudyPlanDaySubjects::tableName() . ' s')
                ->innerJoin(StudyPlanDays::tableName() . ' d', 'd.id = s.study_plan_day_id')
                ->where(['d.study_plan_id' => $plan->id])
                ->andWhere(['is not', 's.mcq_ids', new Expression('NULL')])
                ->column();

            $alreadyAllocatedMcqIds = [];
            foreach ($jsonMcqIdStrings as $jsonString) {
                $ids = json_decode($jsonString);
                if (is_array($ids)) {
                    $alreadyAllocatedMcqIds = array_merge($alreadyAllocatedMcqIds, $ids);
                }
            }
            $alreadyAllocatedMcqIds = array_unique(array_merge($alreadyAllocatedMcqIds, $mcqIdsToRedistributeToday));

            $share = intval(round($remainingTarget / (count($topSubjects) ?: 1)));

            foreach ($topSubjects as $subjectId) {
                if ($allocatedTotal >= $perDay) break;

                $currentShare = min($share, $perDay - $allocatedTotal);
                if ($currentShare <= 0) continue;

                $availableReviewMcqs = (new Query())
                    ->select('mcqs.id')
                    ->from(Mcqs::tableName())
                    ->innerJoin(Hierarchy::tableName(), 'mcqs.hierarchy_id = hierarchy.id')
                    ->where(['hierarchy.subject_id' => $subjectId])
                    ->andWhere(['IN', 'mcqs.id', $alreadyAllocatedMcqIds])
                    ->orderBy(new Expression('RAND()'))
                    ->limit($currentShare)
                    ->column();

                if (count($availableReviewMcqs) < $currentShare) {
                    $additionalMcqs = (new Query())
                        ->select('mcqs.id')
                        ->from(Mcqs::tableName())
                        ->innerJoin(Hierarchy::tableName(), 'mcqs.hierarchy_id = hierarchy.id')
                        ->where(['hierarchy.subject_id' => $subjectId])
                        ->andWhere(['NOT IN', 'mcqs.id', array_merge($alreadyAllocatedMcqIds, $availableReviewMcqs)])
                        ->orderBy(new Expression('RAND()'))
                        ->limit($currentShare - count($availableReviewMcqs))
                        ->column();
                    $availableReviewMcqs = array_merge($availableReviewMcqs, $additionalMcqs);
                }


                if (!empty($availableReviewMcqs)) {
                    $spds = new StudyPlanDaySubjects([
                        'study_plan_day_id' => $day->id,
                        'subject_id' => $subjectId,
                        'allocated_mcqs' => count($availableReviewMcqs),
                        'mcq_ids' => json_encode($availableReviewMcqs),

                        'created_at' => new Expression('NOW()'),
                        'updated_at' => new Expression('NOW()'),
                    ]);
                    if (!$spds->save(false)) {
                        Yii::error("Failed to save general review SPDS for subject {$subjectId}: " . print_r($spds->errors, true));
                    } else {
                        $allocatedTotal += count($availableReviewMcqs);
                        $mcqIdsAllocatedOverall = array_merge($mcqIdsAllocatedOverall, $availableReviewMcqs);
                    }
                }
            }
        }
        $day->new_mcqs = $allocatedTotal;
        $day->review_mcqs = $allocatedTotal;
        $day->save(false);
    }
}
<?php
namespace app\commands;

use yii\console\Controller;
use app\models\OrganSystem;
use app\models\Subject;
use app\models\Chapters;
use app\models\Topics;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportHierarchyController extends Controller
{
    public function actionRun($filePath = '@app/data/unique_system_subject_chapter_topic.xlsx')
    {
        $filePath = \Yii::getAlias($filePath);

        if (!file_exists($filePath)) {
            $this->stderr("Error: File not found at {$filePath}\n");
            return Controller::EXIT_CODE_ERROR;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        array_shift($rows);

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($rows as $row) {
                $organSystemName = trim($row[0]);
                $subjectName = trim($row[1]);
                $chapterName = trim($row[2]);
                $topicName = trim($row[3]);

                // 1. Get or Create Organ System
                $organSystem = OrganSystem::findOne(['name' => $organSystemName]);
                if (!$organSystem) {
                    $organSystem = new OrganSystem();
                    $organSystem->name = $organSystemName;
                    if (!$organSystem->save()) {
                        throw new \Exception("Failed to save Organ System '{$organSystemName}': " . implode(', ', $organSystem->getErrorSummary(true)));
                    }
                    $this->stdout("Created Organ System: {$organSystemName}\n");
                }

                // 2. Get or Create Subject
                $subject = Subject::findOne(['name' => $subjectName]);
                if (!$subject) {
                    $subject = new Subject();
                    $subject->name = $subjectName;
                    if (!$subject->save()) {
                        throw new \Exception("Failed to save Subject '{$subjectName}': " . implode(', ', $subject->getErrorSummary(true)));
                    }
                    $this->stdout("Created Subject: {$subjectName}\n");
                }

                // 3. Get or Create Chapter (linked to the correct subject if chapter names are unique across subjects,
                // otherwise you might need a composite key or a lookup table as discussed initially.
                // For now, assuming Chapter.name is unique enough, or we'll allow duplicates in `chapters` table
                // and rely on `topic` to link to the *specific* chapter instance with the right parent hierarchy
                // which is not how Chapter is currently modeled.
                // ***IMPORTANT: The current `Chapter` model only has `name`. If 'abdomen' means different things
                // for different subjects/organ systems, we need to adjust Chapter creation.
                // Given the file, 'abdomen' appears for multiple (OS, Subject) combos.
                // The simplest interpretation is that the `Chapter` table holds distinct chapter names,
                // and the *full context* is determined by the combination `Organ System` + `Subject` + `Chapter` + `Topic`.
                // For now, we'll treat Chapter names as globally unique, if possible, or expect duplicates in the `chapters` table.
                // Let's stick with the simplified DB for now, meaning 'abdomen' is just 'abdomen', and the OS/Subject links are on MCQ.***

                $chapter = Chapters::findOne(['name' => $chapterName]);
                if (!$chapter) {
                    $chapter = new Chapters();
                    $chapter->name = $chapterName;
                    if (!$chapter->save()) {
                        throw new \Exception("Failed to save Chapter '{$chapterName}': " . implode(', ', $chapter->getErrorSummary(true)));
                    }
                    $this->stdout("Created Chapter: {$chapterName}\n");
                }

                // 4. Get or Create Topic
                // A Topic belongs to a Chapter. The Topic name might be duplicated across different chapters,
                // but its combination with a specific chapter should be unique.
                $topic = Topics::findOne(['name' => $topicName, 'chapter_id' => $chapter->id]);
                if (!$topic) {
                    $topic = new Topics();
                    $topic->name = $topicName;
                    $topic->chapter_id = $chapter->id;
                    if (!$topic->save()) {
                        throw new \Exception("Failed to save Topic '{$topicName}' for Chapter '{$chapterName}': " . implode(', ', $topic->getErrorSummary(true)));
                    }
                    $this->stdout("Created Topic: {$topicName} (Chapter: {$chapterName})\n");
                }
            }
            $transaction->commit();
            $this->stdout("Hierarchy import completed successfully.\n");
            return Controller::EXIT_CODE_NORMAL;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stderr("Error during hierarchy import: " . $e->getMessage() . "\n");
            return Controller::EXIT_CODE_ERROR;
        }
    }
}
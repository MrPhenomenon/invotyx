<?php

namespace app\commands;

use app\models\ExamSpecialties;
use Yii;
use yii\console\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\models\Specialties;
use app\models\Subjects;
use app\models\Chapters;
use app\models\SpecialtyDistributions;
use app\models\SpecialtyDistributionChapters;

class DistributionController extends Controller
{
    /**
     * Import specialty distributions from XLSX
     * Usage: php yii distribution/import /path/to/file.xlsx
     */
    public function actionImport($filePath)
    {
        $filePath = Yii::getAlias($filePath);
        if (!file_exists($filePath)) {
            $this->stderr("File not found: $filePath\n");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);

        foreach ($spreadsheet->getSheetNames() as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            $rows = $sheet->toArray(null, true, true, true);

            // find specialties by name = sheetName
            $specialties = ExamSpecialties::find()->where(['name' => $sheetName])->all();
            if (empty($specialties)) {
                $this->stderr("No specialties found for sheet: $sheetName\n");
                continue;
            }

            $this->stdout("Processing sheet: $sheetName\n");

            $subjectRows = [];
            $rowIndex = 2;
            while (!empty($rows[$rowIndex]['A'])) {
                $subjectRows[] = [
                    'subject'    => trim($rows[$rowIndex]['A']),
                    'count'      => (int) $rows[$rowIndex]['B'],
                    'percentage' => (float) $rows[$rowIndex]['C'],
                ];
                $rowIndex++;
            }

            while (empty($rows[$rowIndex]['A']) && $rowIndex < count($rows)) {
                $rowIndex++;
            }
            $rowIndex++;

            $chapterRows = [];
            while (!empty($rows[$rowIndex]['A'])) {
                $chapterRows[] = [
                    'subject'    => trim($rows[$rowIndex]['A']),
                    'chapter'    => trim($rows[$rowIndex]['B']),
                    'count'      => (int) $rows[$rowIndex]['C'],
                    'percentage' => (float) $rows[$rowIndex]['E'], // "Chapter % within subject"
                ];
                $rowIndex++;
            }

            foreach ($specialties as $specialty) {
                foreach ($subjectRows as $sRow) {
                    $subject = Subjects::findOne(['name' => $sRow['subject']]);
                    if (!$subject) {
                        $this->stderr("Subject not found: {$sRow['subject']}\n");
                        continue;
                    }

                    $distribution = new SpecialtyDistributions();
                    $distribution->specialty_id = $specialty->id;
                    $distribution->subject_id   = $subject->id;
                    $distribution->subject_count        = $sRow['count'];
                    $distribution->subject_percentage   = $sRow['percentage'];
                    if (!$distribution->save()) {
                        $this->stderr("Failed to save distribution: " . json_encode($distribution->errors) . "\n");
                        continue;
                    }

                    foreach ($chapterRows as $cRow) {
                        if ($cRow['subject'] !== $sRow['subject']) {
                            continue;
                        }

                        $chapter = Chapters::findOne(['name' => $cRow['chapter']]);
                        if (!$chapter) {
                            $this->stderr("Chapter not found: {$cRow['chapter']}\n");
                            continue;
                        }

                        $distChapter = new SpecialtyDistributionChapters();
                        $distChapter->specialty_distribution_id = $distribution->id;
                        $distChapter->chapter_id      = $chapter->id;
                        $distChapter->chapter_count           = $cRow['count'];
                        $distChapter->chapter_percentage      = $cRow['percentage'];
                        if (!$distChapter->save()) {
                            $this->stderr("Failed to save distribution_chapter: " . json_encode($distChapter->errors) . "\n");
                        }
                    }
                }
            }
        }

        $this->stdout("Import finished.\n");
    }
}

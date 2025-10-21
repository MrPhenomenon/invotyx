<?php
namespace app\modules\admin\controllers;

use app\models\Chapters;
use app\models\Topics;
use Yii;
use yii\web\Controller;
use app\models\Hierarchy;
use yii\web\Response;

class HierarchyController extends AdminBaseController
{
    protected function allowedRoles(): array
    {
        return ['Super Admin', 'Content Manager'];
    }
    public function actionIndex()
    {
        $hierarchies = Hierarchy::find()
            ->with(['organsys', 'subject', 'chapter', 'topic'])
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        return $this->render('index', [
            'hierarchies' => $hierarchies,
        ]);
    }

    /**
     * Display Topics and Chapters side by side
     */
    public function actionTopicsChapters()
    {
        $topics = \app\models\Topics::find()->asArray()->all();
        $chapters = \app\models\Chapters::find()->asArray()->all();

        return $this->render('topics-chapters', [
            'topics' => $topics,
            'chapters' => $chapters,
        ]);
    }

    /**
     * Display Organ Systems and Subjects side by side
     */
    public function actionSystemsSubjects()
    {
        $organSystems = \app\models\OrganSystems::find()->asArray()->all();
        $subjects = \app\models\Subjects::find()->asArray()->all();

        return $this->render('organ-systems-subjects', [
            'organSystems' => $organSystems,
            'subjects' => $subjects,
        ]);
    }

    public function actionAddTopic()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $model = new Topics();
        $model->name = $data['name'];
        return $model->save()
            ? ['success' => true, 'message' => 'Added Topic']
            : ['success' => false, 'message' => 'Topic couldnt be added'];
    }
    public function actionAddChapter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Chapters();
        $model->name = Yii::$app->request->post('name');
        return $model->save()
            ? ['success' => true, 'message' => 'Added Chapter']
            : ['success' => false, 'message' => 'Chapter couldnt be added'];
    }
    public function actionDeleteChapter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing chapter ID'];
        }
        $model = Chapters::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Chapter not found'];
        }
        if ($model->delete()) {
            return ['success' => true, 'message' => 'Chapter deleted'];
        } else {
            return ['success' => false, 'message' => 'Delete failed'];
        }
    }

    public function actionDeleteTopics()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if (!$id) {
            return ['success' => false, 'message' => 'Missing topic ID'];
        }
        $model = Topics::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Topic not found'];
        }
        if ($model->delete()) {
            return ['success' => true, 'message' => 'Topic deleted'];
        } else {
            return ['success' => false, 'message' => 'Delete failed'];
        }
    }
}

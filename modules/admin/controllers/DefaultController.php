<?php

namespace app\modules\admin\controllers;

use app\models\ManagementTeam;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTeamManagement()
    {
        $data = ManagementTeam::find()
            ->asArray()
            ->all();

        return $this->render('management', [
            'members' => $data,
        ]);
    }

    public function actionAddManagement()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ManagementTeam();

        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $model->created_at = date('Y-m-d H:i:s');

            if ($model->save()) {
                return ['response' => 'success', 'message' => 'Team member added successfully.'];
            }

            return ['response' => 'error', 'message' => 'Failed to save.'];
        }

        return ['response' => 'error', 'message' => 'Invalid input.', 'errors' => $model->getErrors()];
    }

    public function actionDeleteUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $model = ManagementTeam::findOne($id);

        if (!$model) {
            return ['success' => false, 'message' => 'Entry Not Found'];
        }

        return $model->delete()
            ? ['success' => true]
            : ['success' => false, 'message' => 'There was an error deleting this entry'];

    }
}

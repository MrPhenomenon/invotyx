<?php

namespace app\modules\user\controllers;

use app\models\Users;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider; // For pagination and sorting
use app\models\UserBookmarkedMcqs;
use app\models\Mcqs;

class BookmarksController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only authenticated users can access
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a list of bookmarked questions for the current user.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $user = Users::findOne($userId);

        if (!$user) {
            Yii::$app->session->setFlash('error', 'User not found.');
            return $this->goHome();
        }

        $query = Mcqs::find()
            ->joinWith('userBookmarks')
            ->where(['user_bookmarked_mcqs.user_id' => $userId])
            ->orderBy(['user_bookmarked_mcqs.created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'userId' => $userId,
            'userDefaultExamType' => $user->examType->name ?? 'N/A',
            'userDefaultSpecialty' => $user->speciality->name ?? 'N/A',
        ]);
    }
}
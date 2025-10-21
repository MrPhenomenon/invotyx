<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class AdminBaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    throw new \yii\web\ForbiddenHttpException('Access denied.');
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [],
                        'matchCallback' => function () {
                            $admin = \Yii::$app->admin->identity;
                            return $admin && in_array($admin->role, $this->allowedRoles());
                        },
                    ],
                ],
            ],
        ];
    }

    protected function allowedRoles(): array
    {
         return ['Super Admin', 'Content Manager', 'Support Team'];
    }

    public function actionIndex()
    {
        $user = Yii::$app->admin->identity;
        return $this->redirect($user->getDefaultRedirect());
    }
}

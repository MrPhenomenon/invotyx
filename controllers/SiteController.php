<?php

namespace app\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use app\models\ManagementTeam;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPricing()
    {
        return $this->render('pricing');
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        return $this->render('login');
    }
    public function actionRegistration()
    {
        $exams = ExamType::find()->asArray()->all();

        return $this->render('registration', [
            'exams' => $exams,
        ]);
    }

    public function actionGetSpecialization()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $specs = ExamSpecialties::find()
        ->where(['exam_type' => $id])
        ->asArray()
        ->all();
        return [
            'data' => $specs
        ];
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAdminLogin()
    {
        return $this->render('admin-login');
    }

    public function actionLoginAdmin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $email = $request->post('email');
        $password = $request->post('password');

        $admin = ManagementTeam::findOne(['email' => $email]);

        if (!$admin || !Yii::$app->getSecurity()->validatePassword($password, $admin->password)) {
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        Yii::$app->admin->login($admin, 3600 * 3);

        return ['success' => true];
    }
}

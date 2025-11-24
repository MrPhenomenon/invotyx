<?php

namespace app\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use app\models\ManagementTeam;
use app\models\Subjects;
use app\models\Subscriptions;
use app\models\Users;
use app\models\UserSubscriptions;
use app\services\StudyPlanGenerator;
use DateTime;
use yii\authclient\ClientInterface;
use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\User;

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
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
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
        $plans = Subscriptions::find()->asArray()->all();
        return $this->render('pricing', [
            'plans' => $plans,
        ]);
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $error = null;
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $email = Yii::$app->request->post('email');
            $password = Yii::$app->request->post('password');

            $user = Users::findOne(['email' => $email, 'auth_type' => 'local']);
            if ($user && Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
                if ($user && Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
                    $redirect = \app\components\UserService::loginUser($user);
                    Yii::$app->response->data = ['success' => true, 'redirectUrl' => $redirect];
                    return Yii::$app->response;
                } else {
                    $error = 'Invalid email or password';
                    Yii::$app->response->data = ['success' => false, 'error' => $error];
                    return Yii::$app->response;
                }
            }
        } else {
            if (!Yii::$app->user->isGuest) {
                $this->redirect(Url::to(['/user/default/index']));
            }
        }
        return $this->render('login');
    }
    public function actionRegistration()
    {
        $exams = ExamType::find()->asArray()->all();
        $plans = Subscriptions::find()->asArray()->all();
        $subjects = Subjects::find()->asArray()->all();
        return $this->render('registration', [
            'exams' => $exams,
            'plans' => $plans,
            'subjects' => $subjects
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

    public function actionRegisterUser()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $data = Yii::$app->request->post();
        $isGoogle = Yii::$app->session->has('google_user');
        $transaction = Yii::$app->db->beginTransaction();

        $user = new Users();
        $userSub = new UserSubscriptions();

        try {
            if ($isGoogle) {
                $googleData = Yii::$app->session->get('google_user');
                $user->email = $googleData['email'];
                $user->name = $googleData['name'];
                $user->auth_type = 'google';
                $user->password = null;
                $user->exam_type = $data['exam_type'];
                $user->specialty_id = $data['specialty_id'];
                $user->expected_exam_date = $data['expected_exam_date'];

            } else {
                $user->load($data, '');
                $user->auth_type = 'local';
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($data['password']);
            }
           
            $user->mcqs_per_day = $data['mcqs_per_day'];
            if (!empty($data['weak_subjects'])) {
                $user->weak_subjects = json_encode($data['weak_subjects']);
            }
            if (!$user->save(false)) {
                throw new \Exception('User validation failed');
            }

            $subscription = Subscriptions::findOne($data['subscription_id']);
            if (!$subscription) {
                throw new \Exception('Invalid subscription selected');
            }

            $userSub->user_id = $user->id;
            $userSub->subscription_id = $subscription->id;
            $userSub->start_date = date('Y-m-d');
            $userSub->end_date = date('Y-m-d', strtotime("+{$subscription->duration_days} days"));
            $userSub->is_active = 1;

            if (!$userSub->save()) {
                throw new \Exception('User subscription save failed');
            }

            $transaction->commit();
            StudyPlanGenerator::ensureWeeklyPlan($user);
            $redirect = \app\components\UserService::loginUser($user);
            $data['evaluation'] == 1 ? $redirect = Url::to(['/user/exam/start-evaluation-exam']) : $redirect;
            return ['success' => true, 'redirect' => $redirect];

        } catch (\Exception $e) {
            $transaction->rollBack();

            return [
                'success' => false,
                'err' => array_merge(
                    $user->getErrors(),
                    $userSub->getErrors()
                )
            ];
        }
    }
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        $email = $attributes['email'];
        $name = $attributes['name'] ?? explode('@', $email)[0];

        $user = Users::findOne(['email' => $email]);
        if ($user) {
            $redirect = \app\components\UserService::loginUser($user);
            return Yii::$app->response->redirect($redirect);
        }

        Yii::$app->session->set('google_user', [
            'email' => $email,
            'name' => $name,
            'auth_type' => 'google',
        ]);

        return Yii::$app->response->redirect(['/site/registration', 'step' => 2]);
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
    public function actionLogoutAdmin()
    {
        Yii::$app->admin->logout();
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        return $this->render('contact');
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

    public function actionPrivacyPolicy()
    {
        return $this->render('privacy-policy');
    }

    public function actionRefundPolicy()
    {
        return $this->render('refund-policy');
    }

    public function actionServicePolicy()
    {
        return $this->render('service-policy');
    }

    public function actionTermsAndConditions()
    {
        return $this->render('terms-and-conditions');
    }
    public function actionAdminLogin()
    {
        if (Yii::$app->request->isAjax) {
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
        } else {
            return $this->render('admin-login');
        }
    }



    public function actionCache($key)
    {
        $data = Yii::$app->cache->get($key);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data ?: ['error' => 'Not found'];
    }

    public function actionExam()
    {
        return $this->render('exam');
    }
}

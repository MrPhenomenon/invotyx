<?php

namespace app\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use app\models\ManagementTeam;
use app\models\Subscriptions;
use app\models\Users;
use app\models\UserSubscriptions;
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
                Yii::$app->user->login($user, 3600 * 24 * 30);
                return $this->redirect('user');
            } else {
                return ['success' => false, 'message' => 'Invalid username or password.'];
            }
        } else {
            return $this->render('login', [
                'error' => $error,
            ]);
        }
    }
    public function actionRegistration()
    {
        $exams = ExamType::find()->asArray()->all();
        $plans = Subscriptions::find()->asArray()->all();
        return $this->render('registration', [
            'exams' => $exams,
            'plans' => $plans,
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

        $transaction = Yii::$app->db->beginTransaction();

        $user = new Users();
        $userSub = new UserSubscriptions();

        try {
            $user->attributes = $data;
            if (!empty($data['password'])) {
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($data['password']);
            }
            $user->auth_type = 'local';

            if (!$user->save()) {
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
            Yii::$app->user->login($user);
            return ['success' => true, 'redirect' => Url::to(['user//'])];

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



    public function onAuthSuccess(ClientInterface $client)
    {
        $attributes = $client->getUserAttributes();
        Yii::debug($attributes);
        $googleId = $attributes['id'];
        $email = $attributes['email'];
        $name = $attributes['name'];
        $picture = $attributes['picture'] ?? null;

        $user = Users::find()->where(['email' => $email])->one();

        if (!$user) {
            $user = new Users([
                'google_id' => $googleId,
                'email' => $email,
                'name' => $name,
                'profile_picture' => $picture,
                'auth_type' => 'google',
            ]);
            $user->save(false);
            return $this->redirect('/register');
        }
        Yii::$app->user->login($user);
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



    public function actionDebugCache($key)
    {
        $data = Yii::$app->cache->get($key);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $data ?: ['error' => 'Not found'];
    }
}

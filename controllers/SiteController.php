<?php

namespace app\controllers;

use app\models\ExamSpecialties;
use app\models\ExamType;
use app\models\ManagementTeam;
use app\models\Subscriptions;
use app\models\Users;
use app\models\UserSubscriptions;
use Psr\Http\Client\ClientInterface;
use Yii;
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

            if (!$user->save()) {
                throw new \Exception('User validation failed');
            }

            $subscription = Subscriptions::findOne($data['subscription_id']);
            if (!$subscription) {
                throw new \Exception('Invalid subscription selected');
            }

            $userSub->user_id = $user->id;
            $userSub->subscription_id = $subscription->id;
            $userSub->end_date = date('Y-m-d', strtotime("+{$subscription->duration_days} days"));
            $userSub->is_active = 1;

            if (!$userSub->save()) {
                throw new \Exception('User subscription save failed');
            }

            $transaction->commit();
            return ['success' => true];

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

        $googleId = $attributes['sub'];
        $email = $attributes['email'];
        $name = $attributes['name'];
        $picture = $attributes['picture'] ?? null;

        $user = Users::find()->where(['google_id' => $googleId])->one();

        if (!$user) {
            // Create new user
            $user = new Users([
                'google_id' => $googleId,
                'email' => $email,
                'name' => $name,
                'profile_picture' => $picture,
                'auth_type' => 'google',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $user->save(false); // skip validation if you're confident
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

<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Subscriptions;
use app\models\UserSubscriptions;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class SubscriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'], // Ensure delete is a POST request
                ],
            ],
            // Add your access control rules here if this is for an admin panel
        ];
    }

    /**
     * Main dashboard for subscriptions.
     * Shows analytics, a list of plans, and active user subscriptions.
     */
    public function actionIndex()
    {
        // --- Analytics Data ---
        $analytics = [
            'active_subscribers' => UserSubscriptions::find()->where(['is_active' => 1])->count(),
            'total_plans' => Subscriptions::find()->count(),
            'expiring_soon' => UserSubscriptions::find()
                ->where(['is_active' => 1])
                ->andWhere(['between', 'end_date', date('Y-m-d'), date('Y-m-d', strtotime('+7 days'))])
                ->count(),
        ];
        
        // Find most popular plan
        $mostPopular = UserSubscriptions::find()
            ->select(['subscription_id', 'COUNT(*) as count'])
            ->groupBy('subscription_id')
            ->orderBy(['count' => SORT_DESC])
            ->limit(1)
            ->with('subscription')
            ->one();
        $analytics['most_popular_plan'] = $mostPopular->subscription->name ?? 'N/A';

        // --- Data Providers for Tables ---
        $subscriptionDataProvider = new ActiveDataProvider([
            'query' => Subscriptions::find(),
            'sort' => ['defaultOrder' => ['price' => SORT_ASC]],
        ]);

        $userSubscriptionDataProvider = new ActiveDataProvider([
            'query' => UserSubscriptions::find()->where(['is_active' => 1])->with(['user', 'subscription']),
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['end_date' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'subscriptionDataProvider' => $subscriptionDataProvider,
            'userSubscriptionDataProvider' => $userSubscriptionDataProvider,
            'analytics' => $analytics,
        ]);
    }

    /**
     * Creates a new Subscriptions plan.
     */
    public function actionCreate()
    {
        $model = new Subscriptions();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Subscription plan created successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Subscriptions plan.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Subscription plan updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Subscriptions plan.
     * Note: You may want to add logic to prevent deletion if users are subscribed.
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Subscription plan deleted.');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Subscriptions model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Subscriptions::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested subscription plan does not exist.');
    }
}
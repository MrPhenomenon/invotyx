<?php
namespace app\modules\partners\components;

use yii\web\Controller;
use app\models\ExternalPartners;
use Yii;

class PartnerBaseController extends Controller
{
    public $partner;

    public function beforeAction($action)
    {
        $token = Yii::$app->request->get('access');
        $this->partner = ExternalPartners::findOne(['access_token' => $token, 'status' => 'active']);

        if (!$this->partner) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }

        Yii::$app->view->params['partner'] = $this->partner;

        return parent::beforeAction($action);
    }

}

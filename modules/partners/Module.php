<?php

namespace app\modules\partners;

/**
 * partners module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\partners\controllers';
    public $layout = '@app/modules/partners/views/layouts/main';
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}

<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Subscriptions $model */

$this->title = 'Update Subscription Plan: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Subscription Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="subscriptions-update">

    <h1 class="h3 mb-4 text-gray-800"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
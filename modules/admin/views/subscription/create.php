<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Subscriptions $model */

$this->title = 'Create New Subscription Plan';
$this->params['breadcrumbs'][] = ['label' => 'Subscription Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscriptions-create">

    <h1 class="h3 mb-4 text-gray-800"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
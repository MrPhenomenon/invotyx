<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\ExternalPartners */

$this->title = 'Add External Partner';
echo $this->render('_form', ['model' => $model]);
?>

<?php if (Yii::$app->session->hasFlash('partner')): ?>
    <?php
    $flash = Yii::$app->session->getFlash('partner');
    $type = $flash['type'] ?? 'info';
    $msg = $flash['message'] ?? '';
    $js = <<<JS
        showToast('$msg', '$type');
    JS;
    $this->registerJs($js);
    ?>
<?php endif; ?>

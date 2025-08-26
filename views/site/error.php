<?php
use yii\helpers\Html;
use yii\helpers\Url;
/**
 * @var \yii\web\View $this
 * @var string $name
 * @var string $message
 * @var \Exception $exception
 */

$isHttpException = $exception instanceof \yii\web\HttpException;
$statusCode = $isHttpException ? $exception->statusCode : 500;

$userFriendlyTitle = Html::encode($this->title); 
$userFriendlyMessage = nl2br(Html::encode($message));

$customExplanation = "Well, this is awkward. Looks like our study session hit a snag!";
$errorImage = Yii::getAlias('@web') . '/error.png';

if ($statusCode === 404) {
    $userFriendlyTitle = "Page Not Found";
    $customExplanation = "Oops! We couldn't find that study note. Maybe the link is old or mistyped?";
   
} elseif ($statusCode >= 500) {
    $userFriendlyTitle = "Server Hiccup";
    $customExplanation = "Yikes! It seems our system is having a momentary glitch. We're already on it!";
}
?>
<style>
    .custom-error-page {
    margin: 30px auto;
    max-width: 700px;
    font-family: 'Arial', sans-serif;
}
.error-image-container {
    margin-bottom: 30px;
}

.error-title {
    color: #0e273c;
    margin-bottom: 15px;
    font-weight: bold;
}
.error-explanation {
    font-size: 1.25rem;
    color: #333;
    margin-bottom: 10px;
}
.error-message-detail {
    font-size: 1rem;
    color: #555;
    margin-bottom: 25px;
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #eee;
    word-break: break-word;
}
.error-reference {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 25px;
}
.error-actions {
    margin-top: 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}
.error-action-btn {
    padding: 10px 20px;
    font-size: 1rem;
}
.error-search-suggestion {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

</style>
<div class="custom-error-page text-center py-5">
    <div class="error-image-container">
        <img src="<?= $errorImage ?>" alt="Error Illustration" width="180" height="180">
    </div>

    <h1 class="display-4 error-title"><?= $userFriendlyTitle ?></h1>
    <p class="error-explanation">
        <?= $customExplanation ?>
    </p>
    <p class="error-message-detail">
        <?= $userFriendlyMessage ?>
    </p>

    <?php if ($errorReferenceId): ?>
        <p class="error-reference">
            If you contact support, please provide this reference ID: <strong><?= $errorReferenceId ?></strong>
        </p>
    <?php endif; ?>

    <div class="error-actions">
        <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary error-action-btn">Go to Homepage</a>
        <?php if ($statusCode !== 404):?>
            <a href="<?= Html::encode(Yii::$app->request->getUrl()) ?>" class="btn btn-secondary error-action-btn">Try Again</a>
        <?php endif; ?>
        <a href="<?= Url::to(['/site/contact']) ?>" class="btn btn-outline-secondary error-action-btn">Contact Support</a>
    </div>
</div>



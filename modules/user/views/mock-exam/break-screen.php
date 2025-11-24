<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Exam Break';

?>
<style>
    .break-screen-container {
        display: flex;
        justify-content: center;
        text-align: center;
        padding: 20px;
    }

    .break-card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); 
        padding: 40px;
        max-width: 650px; 
        width: 100%;
        transition: transform 0.3s ease; 
    }
    .break-card:hover {
        transform: translateY(-3px);
    }

    .break-card h2 {
        font-size: 2.8rem;
        color: var(--bs-primary);
        margin-bottom: 20px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .break-card h3 {
        font-size: 1.6rem;
        color: #333d47;
        margin-bottom: 25px;
        font-weight: 500;
        line-height: 1.4;
    }

    .break-card p {
        font-size: 1.05rem;
        color: #6a737d;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .countdown-timer {
        font-family: 'Montserrat', sans-serif;
        font-size: 5rem;
        font-weight: 800; 
        color: #28a745;
        margin: 40px 0;
        padding: 15px 30px;
        border: 4px solid #28a745;
        border-radius: 100px;
        display: inline-block;
        background-color: #eafbea;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
        transition: all 0.5s ease-in-out;
        letter-spacing: 2px; 
        animation: pulseGreen 2s infinite alternate;
        width: 350px;
    }

    .countdown-timer.red {
        color: #dc3545;
        border-color: #dc3545;
        background-color: #ffeaea;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15);
        animation: pulseRed 1.5s infinite alternate; 
    }

    .countdown-timer.finished {
        color: #6c757d;
        border-color: #ced4da;
        background-color: #f8f9fa;
        box-shadow: none;
        animation: none;
    }

    .btn-start-part2 {
        padding: 18px 45px;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 50px;
        margin-top: 35px;
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
    }
    .btn-start-part2:hover:not(:disabled) {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 115, 232, 0.3);
    }
    .btn-start-part2:disabled {
        cursor: not-allowed;
        opacity: 0.5;
        background-color: #a0a6ad;
        border-color: #a0a6ad;
        box-shadow: none;
    }

    .note-text {
        margin-top: 30px;
        font-size: 0.9em;
        color: #909aa3;
        font-style: italic;
    }
    .fa-icon {
        margin-right: 8px;
        font-size: 1.1em;
    }

    @keyframes pulseGreen {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15); }
        100% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); }
    }
    @keyframes pulseRed {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15); }
        100% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3); }
    }

    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap');
</style>

<div class="break-screen-container">
    <div class="break-card">
        <h2><i class="fa-solid fa-mug-hot fa-icon"></i> Take a Break</h2>
        <h3>Part 1 of your exam is completed.</h3>

        <p>This is a mandatory break. Please relax and recharge before proceeding.</p>

        <div id="break-countdown" class="countdown-timer">
            <?= gmdate("i:s", $timeLeftInBreak) ?>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => Url::to(['break-screen', 'session' => $attempt]),
            'method' => 'post',
        ]); ?>
            <?= Html::hiddenInput('action', 'continue_part2') ?>
            <?= Html::submitButton('Start Part 2', [
                'class' => 'btn btn-success btn-start-part2',
                'id' => 'start-part2-btn',
                'disabled' => ($timeLeftInBreak > 0)
            ]) ?>
        <?php ActiveForm::end(); ?>

        <p class="note-text">
            <i class="fa-solid fa-lightbulb fa-icon"></i>
            The "Start Part 2" button will activate automatically when the break period concludes.
            Part 2 will begin with its own dedicated 2-hour timer.
        </p>
    </div>
</div>

<?php

$jsTimeLeft = $timeLeftInBreak;

$this->registerJs(<<<JS
    var timeLeft = {$jsTimeLeft};
    var countdownDisplay = $('#break-countdown');
    var startButton = $('#start-part2-btn');

    function updateCountdown() {
        var minutes = Math.floor(timeLeft / 60);
        var seconds = timeLeft % 60;

        countdownDisplay.text(
            ("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2)
        );

        if (timeLeft <= 300 && timeLeft > 0) {
            countdownDisplay.addClass('red').removeClass('finished');
        } else if (timeLeft <= 0) {
            clearInterval(timerInterval);
            countdownDisplay.removeClass('red').addClass('finished');
            countdownDisplay.text('00:00');
            startButton.prop('disabled', false);

            if (!countdownDisplay.data('toast-shown')) {
                showToast("✅ Mandatory break is over! You can now proceed to Part 2.", "success");
                countdownDisplay.data('toast-shown', true);
            }
        } else {
            countdownDisplay.removeClass('red').removeClass('finished');
        }
        timeLeft--;
    }

  updateCountdown();

    var timerInterval;
    if (timeLeft > 0) {
        timerInterval = setInterval(updateCountdown, 1000);
    } else {

        startButton.prop('disabled', false);
        countdownDisplay.text('00:00');
        countdownDisplay.addClass('finished');
    }
JS
, \yii\web\View::POS_END);
?>
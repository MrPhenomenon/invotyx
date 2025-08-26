<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $attempt int */
/* @var $timeLeftInBreak int */ // Remaining seconds until break is over
/* @var $mandatoryBreakDuration int */ // Total duration of mandatory break

$this->title = 'Exam Break';

// Register FontAwesome for icons
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
?>
<style>
    /* Overall page background, assuming this is applied to the root element by your layout */
    body, html {
        height: 100%;
        background-color: #f5f7fa; /* A subtle, professional light grey */
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    }

    .break-screen-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #e0efff, #f5f7fa); /* A soft gradient for depth */
    }

    .break-card {
        background-color: #ffffff;
        border-radius: 12px; /* Slightly less rounded for sleekness */
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); /* More subtle, diffused shadow */
        padding: 40px;
        max-width: 650px; /* Optimal width for readability */
        width: 100%;
        transition: transform 0.3s ease; /* Gentle hover effect for the card */
    }
    .break-card:hover {
        transform: translateY(-3px); /* Lift slightly on hover */
    }

    .break-card h2 {
        font-size: 2.8rem; /* Slightly smaller, more refined */
        color: #1a73e8; /* A professional, Google-like blue */
        margin-bottom: 20px;
        font-weight: 700;
        letter-spacing: -0.5px; /* Tighter letter spacing for polish */
    }

    .break-card h3 {
        font-size: 1.6rem;
        color: #333d47; /* Darker grey for stronger contrast */
        margin-bottom: 25px;
        font-weight: 500; /* Medium weight for sleekness */
        line-height: 1.4;
    }

    .break-card p {
        font-size: 1.05rem;
        color: #6a737d; /* Muted grey for body text */
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .countdown-timer {
        font-family: 'Montserrat', sans-serif; /* More modern, strong font */
        font-size: 5rem; /* Even larger for impact */
        font-weight: 800; /* Extra bold */
        color: #28a745; /* Green for go */
        margin: 40px 0;
        padding: 15px 30px;
        border: 4px solid #28a745; /* Slightly thicker border */
        border-radius: 100px; /* Pill shape for the timer display */
        display: inline-block;
        background-color: #eafbea; /* Very light green background */
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15); /* Softer shadow */
        transition: all 0.5s ease-in-out; /* Slower, smoother transitions */
        letter-spacing: 2px; /* Add some spacing for numbers */
        animation: pulseGreen 2s infinite alternate; /* Subtle pulse animation */
        width: 350px;
    }

    .countdown-timer.red {
        color: #dc3545; /* Standard danger red */
        border-color: #dc3545;
        background-color: #ffeaea; /* Very light red background */
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15);
        animation: pulseRed 1.5s infinite alternate; /* Faster pulse for urgency */
    }

    .countdown-timer.finished {
        color: #6c757d; /* Muted grey when finished */
        border-color: #ced4da; /* Lighter border */
        background-color: #f8f9fa; /* Off-white background */
        box-shadow: none;
        animation: none; /* No pulse when finished */
    }

    .btn-start-part2 {
        padding: 18px 45px; /* Larger padding for a more substantial button */
        font-size: 1.5rem; /* Larger text */
        font-weight: bold;
        border-radius: 50px; /* Consistent pill shape */
        margin-top: 35px;
        background-color: #1a73e8; /* Primary button matches heading blue */
        border-color: #1a73e8;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
    }
    .btn-start-part2:hover:not(:disabled) {
        background-color: #0c63e4; /* Slightly darker blue on hover */
        border-color: #0c63e4;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 115, 232, 0.3); /* Blue shadow on hover */
    }
    .btn-start-part2:disabled {
        cursor: not-allowed;
        opacity: 0.5; /* More visible disabled state */
        background-color: #a0a6ad; /* Grey background when disabled */
        border-color: #a0a6ad;
        box-shadow: none;
    }

    .note-text {
        margin-top: 30px;
        font-size: 0.9em;
        color: #909aa3; /* Even lighter grey for notes */
        font-style: italic;
    }
    .fa-icon {
        margin-right: 8px; /* Slightly less margin for icons */
        font-size: 1.1em; /* Ensure icons scale with text */
    }

    /* Keyframe animations for subtle pulsing */
    @keyframes pulseGreen {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15); }
        100% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); }
    }
    @keyframes pulseRed {
        0% { transform: scale(1); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.15); }
        100% { transform: scale(1.02); box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3); }
    }

    /* Import a web font for Montserrat if not already included globally */
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
            'action' => Url::to(['break-screen', 'attempt' => $attempt, 'passkey' => $passkey]),
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
            Part 2 will begin with its own dedicated 2.5-hour timer.
        </p>
    </div>
</div>

<?php
// Ensure Montserrat font is loaded for the countdown timer if not already global
$this->registerLinkTag([
    'rel' => 'preconnect',
    'href' => 'https://fonts.googleapis.com'
]);
$this->registerLinkTag([
    'rel' => 'preconnect',
    'href' => 'https://fonts.gstatic.com',
    'crossorigin' => true
]);
$this->registerLinkTag([
    'href' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800&display=swap',
    'rel' => 'stylesheet'
]);

$jsTimeLeft = $timeLeftInBreak; // Remaining time in seconds

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

        if (timeLeft <= 300 && timeLeft > 0) { // Less than 5 minutes remaining (5 minutes = 300 seconds)
            countdownDisplay.addClass('red').removeClass('finished'); // Ensure 'finished' is removed if time ticks back up (unlikely but safe)
        } else if (timeLeft <= 0) {
            clearInterval(timerInterval);
            countdownDisplay.removeClass('red').addClass('finished');
            countdownDisplay.text('00:00'); // Ensure it displays exactly 00:00
            startButton.prop('disabled', false); // Enable the button
            // Only show toast if it hasn't been shown yet for this state transition
            if (!countdownDisplay.data('toast-shown')) {
                showToast("✅ Mandatory break is over! You can now proceed to Part 2.", "success");
                countdownDisplay.data('toast-shown', true);
            }
        } else {
            countdownDisplay.removeClass('red').removeClass('finished'); // Reset to default green if time is sufficient
        }
        timeLeft--;
    }

    // Initial call to set the display and button state correctly
    updateCountdown();

    // Set up the interval only if there's time left
    var timerInterval;
    if (timeLeft > 0) {
        timerInterval = setInterval(updateCountdown, 1000);
    } else {
        // If time is already 0 or less on load, ensure button is enabled and state is finished
        startButton.prop('disabled', false);
        countdownDisplay.text('00:00');
        countdownDisplay.addClass('finished');
    }
JS
, \yii\web\View::POS_END);
?>
<div class="toast-container position-fixed top-0 end-0 mt-5 me-3" style="z-index: 9999;">
  <div id="global-toast" class="toast align-items-center text-white bg-success border-0" role="alert"
    aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="global-toast-body">
        Toast message
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
        aria-label="Close"></button>
    </div>
  </div>
</div>

<?php
$js = '';
foreach (Yii::$app->session->getAllFlashes() as $type => $message) {
  if (!is_string($message)) {
    continue;
  }
  $js .= "showToast(" . json_encode($message) . ", " . json_encode($type) . ");\n";
}
if ($js) {
  $this->registerJs($js, \yii\web\View::POS_READY);
}
?>
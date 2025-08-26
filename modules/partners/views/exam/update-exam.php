<?php
$this->title = 'Update Exam';
echo $this->render('form', [
    'model' => $model,
    'title' => $this->title,
    'accessList' => $accessList,
]);

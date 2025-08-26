<?php
namespace app\components;

use yii\log\FileTarget;

class PlainFileTarget extends FileTarget
{
    public function formatMessage($message)
    {
        // Only the raw message, no timestamp/category/level
        return $message[0] . PHP_EOL;
    }
}

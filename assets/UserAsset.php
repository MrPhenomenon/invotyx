<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UserAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css',
        'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css',
        'userassets/css/style.css',
        'userassets/css/style-preset.css',
        'userassets/css/landing.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js',
        'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js',
        'userassets/js/plugins/apexcharts.min.js',
        'userassets/js/plugins/simplebar.min.js',
        'userassets/js/pages/dashboard-default.js',
        'userassets/js/pcoded.js',
        'userassets/js/plugins/feather.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}

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
class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap',
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css',
        'dassets/css/plugins/animate.min.css',
        'dassets/css/style.css',
        'dassets/css/style-preset.css',
        'dassets/css/landing.css',
    ];
    public $js = [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js',
        'dassets/js/plugins/apexcharts.min.js',
        'dassets/js/pages/dashboard-default.js',
        'dassets/js/plugins/popper.min.js',
        'dassets/js/plugins/simplebar.min.js',
        'dassets/js/plugins/bootstrap.min.js',
        'dassets/js/fonts/custom-font.js',
        'dassets/js/pcoded.js',
        'dassets/js/plugins/feather.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}

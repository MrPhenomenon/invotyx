<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        'partners' => [
            'class' => 'app\modules\partners\Module',
        ],
    ],
    'components' => [
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => '425138559645-82jehbe38vplgr2o99nce8bk3l65fgmh.apps.googleusercontent.com',
                    'clientSecret' => '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',
                    'returnUrl' => 'https://www.ikjimpex.com/invotyx/site/auth?authclient=google',
                ],
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'USD', // <--- ADD THIS LINE (or your preferred currency code like 'EUR', 'GBP', etc.)
        ],
        'request' => [
            'cookieValidationKey' => 'hjcmbQv20kzanekS7an6c7MTaYPK_wHB',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Users',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'admin' => [ //for admin
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\ManagementTeam',
            'enableAutoLogin' => false,
            'loginUrl' => ['site/admin-login'],
            'idParam' => '_adminId',
            'identityCookie' => ['name' => '_adminIdentity', 'httpOnly' => true],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/admin/team-management' => '/admin/default/team-management',
                '/register' => 'site/registration',
                '/pricing' => 'site/pricing',
                '/about' => 'site/about',
                '/contact' => 'site/contact',
                '/login' => 'site/login',
                '/admin/login' => 'site/admin-login',

                // User Panel

                'user/results/<id:\d+>' => 'user/results/view',
                'user/profile' => 'user/default/profile',

                //Orthopedic Exam
                'user/orthopedic-exam/start-exam/<id:\w+>' => 'user/orthopedic-exam/start-exam',
                'user/orthopedic-exam/take-exam/<attempt:\d+>/<passkey:[a-zA-Z0-9_-]+>' => 'user/orthopedic-exam/take-exam',
                'user/orthopedic-exam/break-screen/<attempt:\d+>/<passkey:[a-zA-Z0-9_-]+>' => 'user/orthopedic-exam/break-screen',
                'user/orthopedic-exam/finalize-exam-and-redirect/<attempt:\d+>/<passkey:[a-zA-Z0-9_-]+>' => 'user/orthopedic-exam/finalize-exam-and-redirect',
                'user/orthopedic-exam/result/<attempt:\d+>/<passkey:[a-zA-Z0-9_-]+>' => 'user/orthopedic-exam/result',
            ],
        ],

    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

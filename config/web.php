<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';


$config = [
    'id' => 'basic',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //    'layout' => 'main',
    // 'language' => 'vi-VN',
    // 'sourceLanguage' => 'en-US',
    'defaultRoute' => 'quanly/dashboard/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'forceCopy' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => []
                ],
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'o60lq8tkQdSpe0YVVgspsSNPn-jIuk1q',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'hcmgis\user\models\AuthUser',
            'loginUrl' => ['user/auth/login'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/main-html',
            'textLayout' => 'layouts/main-text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['nvhong@hcmgis.vn' => 'GIS'],
                'replyTo' => 'nvhong148@gmail.com'
            ],
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'host07.emailserver.vn',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'nvhong@hcmgis.vn',
                'password' => '@nvhong',
                'port' => '465', // Port 25 is a very common port too
                'encryption' => 'ssl', // It is often used, check your provider or mail server specs
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            //            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ],
        ],
        'db' => $db,
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => []
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '<i></i>',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
        ],
        // 'i18n' => [
        //     'translations' => [
        //         'app*' => [
        //             'class' => 'yii\i18n\PhpMessageSource',
        //             //'basePath' => '@app/messages',
        //             //'sourceLanguage' => 'en-US',
        //             'fileMap' => [
        //                 'app' => 'app.php',
        //                 'app/error' => 'error.php',
        //             ],
        //         ],
        //     ],
        // ],
        // 'response' => [
        //     'class' => 'yii\web\Response',
        //     'on beforeSend' => function ($event) {
        //         $response = $event->sender;
        //         $response->headers->add('Access-Control-Allow-Origin', 'https://tanuyen.vietinfo.tech'); // Thay '*' bằng domain của bên thứ ba
        //         $response->headers->add('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        //         $response->headers->add('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        //     },
        // ],
    ],
    'modules' => [
        'user' => [
            'class' => 'hcmgis\user\Module',
            'actionModules' => ['quanly', 'user'], // Danh sách module cần phân quyền,
            'layout' => '@app/modules/layouts/main'
        ]
    ],
    'as beforeRequest' => [
        'class' => \yii\filters\AccessControl::className(),
        'rules' => [
            [
                // SCADA gateway duoc phep goi api-san-luong khong can login
                'controllers' => ['quanly/nhat-ky'],
                'actions' => ['api-san-luong'],
                'allow' => true,
                'ips' => ['192.168.31.6'],
            ],
            [
                //                'controllers' => ['site'],
                'actions' => ['login', 'register'],
                'allow' => true,
            ],
            [
                'controllers' => ['quanly/map'],
                'allow' => true,
                //                'actions' => ['index'], // Chỉ cho phép truy cập action index trong controller API
                'roles' => ['?'], // Cho phép truy cập không cần đăng nhập
            ],
            [
                'allow' => true,
                'roles' => ['@'],
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

$config['modules']['contrib'] = [
    'class' => 'app\modules\contrib\Module'
];

//$config['modules']['app'] = [
//    'class' => 'app\modules\app\Module'
//];

$config['modules']['quanly'] = [
    'class' => 'app\modules\quanly\Module'
];
$config['modules']['gridview'] = [
    'class' => 'app\widgets\gridview\Module',
];

$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
    //    'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
    'generators' => [
        'DCrud' => [
            'class' => 'app\widgets\crud\generators\Generator',
            //                'templates' => [
            //                    'my' => '@app/myTemplates/crud/default',
            //                ]
        ]
    ],
];

return $config;

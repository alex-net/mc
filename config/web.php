<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
///$urlmanrules= require __DIR__.'/urlmanrules.php';

$config = [
    'id' => 'basic',
    'defaultRoute'=>'mc',
    'name'=>'Молодёжный центр',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',['class'=>'app\components\UrlmanagerInit']],
    'language'=>'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@files' => '@app/web/files',
        '@filesUrl'=>'/files',
        '@presets'=>'@app/presets',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'OpwZTrORcY1xYgaTJ6IjPsQQ8OfyZikD',
            'enableCsrfCookie'=>false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            //'useFileTransport' => true,
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
        'db' => $db,
        'assetManager'=>[
            'linkAssets'=>true,
        ],
        'session'=>[
            'class'=>'yii\web\DbSession',
            'sessionTable'=>'sessi',
            'timeout'=>3600,
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing'=>false,
            'showScriptName' => false,
            'rules'=>[],
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
        'allowedIPs' => ['127.0.0.1','109.191.216.143', '::1'],
    ];
/*
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    s*/
}

return $config;

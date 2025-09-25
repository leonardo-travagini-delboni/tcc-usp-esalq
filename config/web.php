<?php

namespace app\config;

use yii\rest\UrlRule;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => $params['appName'],
    'language' => 'pt-BR',
    'timeZone' => 'America/Sao_Paulo',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => $params['dev']['cookie_validation_key'],
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
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            // 'useFileTransport' => true,          // true = save mail to file
            'useFileTransport' => false,            // false = send mail
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'scheme' => 'smtp',
                'host' => $params['smtp']['host'],
                'username' => $params['smtp']['username'],
                'password' => $params['smtp']['password'],
                'port' => $params['smtp']['port'],
                'encryption' => $params['smtp']['encryption'],
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Controladores API padrão
                [
                    'class' => UrlRule::class, 
                    'controller' => [
                        'api-coordenada',
                        'api-consumo',
                        'api-dimensionamento',
                        'api-painel',
                        'api-mppt',
                        'api-bateria',
                    ],
                    'pluralize' => false,
                ],
                // Controlador API específico
                [
                    'class' => UrlRule::class,
                    'controller' => [
                        'api-site',
                    ],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST signup' => 'signup',
                        'POST login' => 'login',
                        'POST logout' => 'logout',
                    ],
                ],
            ],
        ],
        // Deginindo as durações das sessões
        'session' => [
            'class' => 'yii\web\Session',
            'timeout' => $params['user.token_expiration'],
            'cookieParams' => [
                'lifetime' => $params['user.token_expiration'],
                'httponly' => true,
            ],
        ],
    ],
    'params' => $params,
];

if ($params['dev']['debug_toolbar']=='true') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];
}

if ($params['dev']['gii']=='true') {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];
}

return $config;

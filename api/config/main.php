<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$routes = include __DIR__ . '/routes.php';
$host_name = array_value($params, 'domain_name', get_host());

$main_config = array(
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'params' => $params,
//    'bootstrap' => ['gii'],
//    'modules' => [
//        'gii' => [
//            'class' => 'yii\gii\Module',
//            'allowedIPs' => ['*'] // adjust this to your needs
//          ],
//    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser'
            ]
        ],
        'response' => [
            'format' =>  \yii\web\Response::FORMAT_JSON
        ],
//        'telegram' => [
//            'class' => 'aki\telegram\Telegram',
//            'botToken' => '7176000262:AAFyAKqLshC2FONHE1YARKCRIWCrtmxW76g',
//        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'loginUrl' => null,
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => 'identity-user',
                'path' => '/',
            ],
        ],
        'urlManager' => [
//            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => $routes,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@api/translations',
                    'fileMap' => [
                        'app' => 'app.php',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
);

return check_app_config_files($main_config);

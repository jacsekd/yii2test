<?php
return[
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => false,
    'rules' => [
        '/' => 'site/index',

// Default rules:
        '<controller:[\w-]+>' => '<controller>/index',
        '<controller:[\w-]+>/<id:\d+>' => '<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:\d+>' => '<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',

    ],
];

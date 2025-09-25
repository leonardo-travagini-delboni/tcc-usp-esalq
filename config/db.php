<?php

// Gettings params
$params = require __DIR__ . '/params.php';

return [
    // DB Settings
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . $params['db']['host'] . ';dbname=' . $params['db']['name'] . ';port=' . $params['db']['port'],
    'username' => $params['db']['user'],
    'password' => $params['db']['password'],
    'charset' => $params['db']['charset'],

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

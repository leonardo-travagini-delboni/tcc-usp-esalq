<?php

// Gettings settings from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Defining production/development environment:
if ($_ENV['IS_PRODUCTION']=='true') {
    // BOOL PARAMS
    defined('YII_ENV') or define('YII_ENV', 'prod');
    defined('YII_DEBUG') or define('YII_DEBUG', false);
    // DB SETTINGS
    $final_db_host = $_ENV['DB_HOST_PROD'];
    $final_db_name = $_ENV['DB_NAME_PROD'];
    $final_db_port = $_ENV['DB_PORT_PROD'];
    $final_db_user = $_ENV['DB_USER_PROD'];
    $final_db_pass = $_ENV['DB_PASS_PROD'];
    $final_db_char = $_ENV['DB_CHAR_PROD'];
} else {
    // BOOL PARAMS
    defined('YII_ENV') or define('YII_ENV', 'dev');
    defined('YII_DEBUG') or define('YII_DEBUG', true);
    // DB SETTINGS
    $final_db_host = $_ENV['DB_HOST_DEV'];
    $final_db_name = $_ENV['DB_NAME_DEV'];
    $final_db_port = $_ENV['DB_PORT_DEV'];
    $final_db_user = $_ENV['DB_USER_DEV'];
    $final_db_pass = $_ENV['DB_PASS_DEV'];
    $final_db_char = $_ENV['DB_CHAR_DEV'];
}

return [
    // ORIGINAL
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    // OTHER PARAMS
    'appName' => $_ENV['APP_NAME'],             // Nome da aplicação
    'user.passwordResetTokenExpire' => 3600,    // 1 hora (3600 segundos, padrão)
    'user.passwordMinLength' => 8,              // 8 caracteres (mínimo)
    'user.token_expiration' => 1800,            // 30 minutos (1800 segundos, padrão)

    // DATABASE SETTINGS
    'db' => [
        'host' => $final_db_host,
        'name' => $final_db_name,
        'port' => $final_db_port,
        'user' => $final_db_user,
        'password' => $final_db_pass,
        'charset' => $final_db_char,
    ],

    // DEV PARAMS
    'dev' => [
        'cookie_validation_key' => $_ENV['COOKIE_VALIDATION_KEY'],
        'debug_toolbar' => $_ENV['ENABLE_DEBUG'],
        'gii' => $_ENV['ENABLE_GII'],
    ],

    // SMTP SETTINGS
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'],
        'port' => $_ENV['SMTP_PORT'],
        'username' => $_ENV['SMTP_USERNAME'],
        'email' => $_ENV['SMTP_EMAIL'],
        'password' => $_ENV['SMTP_PASSWORD'],
        'encryption' => $_ENV['SMTP_ENCRYPTION'],
    ],
];

<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => $_ENV['DB_DSN'] ?? 'mysql:host=mysql_db;dbname=yii2basic',
    'username' => $_ENV['DB_USERNAME'] ?? 'user',
    'password' => $_ENV['DB_PASSWORD'] ?? 'pw',
    'charset' => 'utf8',
    'tablePrefix' => 'taskforce_'

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];

<?php

return [
    'app_name' => 'Solynx',
    'timezone' => 'UTC',
    'log_path' => __DIR__ . '/../storage/logs',
    'db' => [
        'driver' => 'mysql',
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT'),
        'database' => getenv('DB_DATABASE'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
    ],
];

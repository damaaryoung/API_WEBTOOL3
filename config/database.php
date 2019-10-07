<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysl' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            // 'options'   => [
            //     \PDO::ATTR_EMULATE_PREPARES => true
            // ]
        ]
    ],

    'migrations' => 'migrations',
];
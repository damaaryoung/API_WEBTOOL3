<?php

return [
    'default' => 'mysql',
    'connections' => [
        'web' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '103.31.232.148'),
            'port' => env('DB_PORT', 3307),
            'database' => env('DB_DATABASE', 'newwebtool'),
            'username' => env('DB_USERNAME', 'u2Qi7Jfui'),
            'password' => env('DB_PASSWORD', 'qJ9ysIkg9ce!'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],

   'centro' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST_CENTRO', '103.31.232.148'),
            'port' => env('DB_PORT', 3308),
            'database' => env('DB_DATABASE_CENTRO', 'centro'),
            'username' => env('DB_USERNAME_CENTRO', 'u2Qi7Jfui'),
            'password' => env('DB_PASSWORD_CENTRO', 'qJ9ysIkg9ce!'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],


 'jari' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '103.31.232.148'),
            'port' => env('DB_PORT', 3307),
            'database' => env('DB_DATABASE_JARI', 'jari_collection'),
            'username' => env('DB_USERNAME_JARI', 'UT21jwKreq9'),
            'password' => env('DB_PASSWORD_JARI', 'RciFgHOceQHG'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],
'simar' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE_SIMAR','simar'),
           'username' => env('DB_USERNAME', 'u2Qi7Jfui'),
            'password' => env('DB_PASSWORD', 'qJ9ysIkg9ce!'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],

        'dpm' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE_DPM'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],

'webtool' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST','103.31.232.148'),
            'port' => env('DB_PORT',3307),
            'database' => env('DB_DATABASE_WEBTOOL','webtool'),
            'username' => env('DB_USERNAME','u2Qi7Jfui'),
            'password' => env('DB_PASSWORD','qJ9ysIkg9ce!'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', false),
            'engine' => env('DB_ENGINE', null),
            // 'timezone' => env('DB_TIMEZONE', '+00:00'),
            'options'   => [
                \PDO::ATTR_EMULATE_PREPARES => true
            ]
        ],
    ],
    'redis' => [

       'client' => env('REDIS_CLIENT', 'predis'),

        'default' => [
           'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
           'database' => env('REDIS_DB', 0),
       ],

        'cache' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
           'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
           'database' => env('REDIS_CACHE_DB', 1),
       ],

   ],

    // 'migrations' => 'migrations',
];

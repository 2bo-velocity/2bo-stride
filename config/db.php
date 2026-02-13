<?php

return [
    'master' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'stride'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
    'slaves' => [
        [
            'driver' => 'mysql',
            'host' => env('DB_SLAVE_HOST_1', '127.0.0.1'),
            'port' => env('DB_SLAVE_PORT_1', '3306'),
            'database' => env('DB_DATABASE', 'stride'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'weight' => (int) env('DB_SLAVE_WEIGHT_1', 1),
        ],
    ],
    'sticky_master_seconds' => 3,
];

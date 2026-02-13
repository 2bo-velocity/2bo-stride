<?php

return [
    'name' => env('APP_NAME', 'Stride'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'UTC',
    'locale' => 'en',
    
    'asset_url' => env('ASSET_URL', null),
    'asset_version' => env('ASSET_VERSION', null),
    
    'providers' => [
        // Service Providers
    ],
];

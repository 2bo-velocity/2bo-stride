<?php

use Stride\Core\App;
use Stride\Core\Config\Config;
use Stride\Core\Config\Env;
use Stride\Core\Container\ServiceProvider;
use Stride\Core\Routing\Router;
use Stride\Core\View\View;
use Stride\Core\Database\ConnectionManager;

// Load Env
Env::load(__DIR__ . '/../.env');

// Maintenance Check
// Skip for CLI to allow maintenance commands
$isCli = (php_sapi_name() === 'cli');

if (!$isCli && Env::get('APP_MAINTENANCE') === 'true') {
    http_response_code(503);
    echo "Service Unavailable"; // Or generic maintenance view
    exit;
}

// Load Config
Config::load(__DIR__ . '/../config');

// Initialize App
$app = new App();

// Register Core Services
// Register Core Services
$app->config = Stride\Core\Config\Config::all(); // Ensure Config::all() exists or use reflection/public property
$app->router = new Router();
$app->view = new View(__DIR__ . '/../resources/views');

// Database Provider
$app->register(new Stride\Core\Database\DatabaseProvider());

// Redis Initialization
if (extension_loaded('redis') && Env::get('REDIS_HOST')) {
    $redis = new Redis();
    $redis->connect(Env::get('REDIS_HOST'), Env::get('REDIS_PORT', 6379));
    if ($pass = Env::get('REDIS_PASSWORD')) {
        $redis->auth($pass);
    }
    $app->redis = $redis;
    
    // Queue Initialization
    $app->queue = new Stride\Core\Queue\Queue($redis);
}

// Operational & Resilience Services
$app->featureFlags = new Stride\Core\Operations\FeatureFlag(Config::get('features', []));
$app->metrics = new Stride\Core\Operations\Metrics();
$app->circuitBreaker = new Stride\Core\Resilience\CircuitBreaker();

// RateLimiter (needs Redis if available, else null/in-memory fallback depending on implementation)
$app->rateLimiter = new Stride\Core\Resilience\RateLimiter($app->redis ?? null);

return $app;

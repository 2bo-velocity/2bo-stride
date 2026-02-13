<?php

use Stride\Core\Routing\Router;
use App\Controllers\HomeController;

/** @var Router $router */
$router = app()->router;

$router->get('/', [HomeController::class, 'index']);
$router->get('/hello', function() {
    return 'Hello World from Closure';
});
$router->get('/health/live', function() {
    $status = \Stride\Core\Health\HealthCheck::run();
    return (new \Stride\Core\Http\Response())
        ->json($status);
});

$router->get('/health/ready', function() {
    $status = \Stride\Core\Health\HealthCheck::run();
    // In real world, ready check might be more strict (e.g. migrations up to date)
    return (new \Stride\Core\Http\Response())
        ->json($status);
});

$router->get('/metrics', function() {
    $metrics = app()->metrics->render();
    return (new \Stride\Core\Http\Response($metrics))
        ->setHeader('Content-Type', 'text/plain');
});

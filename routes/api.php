<?php

use Stride\Core\Routing\Router;

/** @var Router $router */
$router = app()->router;

$router->get('/api/ping', function() {
    return json(['status' => 'ok']);
});

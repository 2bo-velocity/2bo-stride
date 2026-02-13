<?php

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

// Load Routes
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

// Capture Request
$request = Request::capture();

// Run Application
$app->run($request);

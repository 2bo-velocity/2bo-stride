<?php

namespace Stride\Core\Middleware;

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;
use Stride\Core\Config\Env;

class MaintenanceMiddleware
{
    public function __invoke(Request $request, callable $next): Response
    {
        if (Env::get('APP_MAINTENANCE') === 'true') {
            return (new Response())
                ->setStatus(503)
                ->setBody('Service Unavailable (Maintenance Mode)');
        }

        return $next($request);
    }
}

<?php

namespace Stride\Core\Middleware;

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;
use Stride\Core\Support\Logger;
use Stride\Core\Operations\RequestId;

class LoggingMiddleware
{
    public function __invoke(Request $request, callable $next): Response
    {
        $start = microtime(true);
        $method = $request->method();
        $path = $request->path();
        $rid = RequestId::get();

        Logger::info("Request Started", [
            'method' => $method,
            'path' => $path,
            'request_id' => $rid
        ]);

        try {
            /** @var Response $response */
            $response = $next($request);
        } catch (\Throwable $e) {
            Logger::error("Request Failed", [
                'method' => $method,
                'path' => $path,
                'error' => $e->getMessage(),
                'request_id' => $rid
            ]);
            throw $e;
        }

        $duration = (microtime(true) - $start) * 1000;
        
        Logger::info("Request Finished", [
            'method' => $method,
            'path' => $path,
            'status' => $response->status(),
            'duration_ms' => $duration,
            'request_id' => $rid
        ]);

        return $response;
    }
}

<?php

namespace Stride\Core;

use Stride\Core\Container\ServiceProvider;

class App
{
    private static ?App $instance = null;
    
    // Service Registry
    public $db;
    public $cache;
    public $logger;
    public $config;
    public $router;
    public $view;
    public $featureFlags;
    public $rateLimiter;
    public $circuitBreaker;
    public $metrics;
    public $redis;
    public $queue;
    
    public function __construct()
    {
        self::$instance = $this;
        (new \Stride\Core\Exceptions\ExceptionHandler())->register();
    }

    public static function getInstance(): App
    {
        return self::$instance;
    }
    
    public static function setInstance(App $app): void
    {
        self::$instance = $app;
    }

    public function register(ServiceProvider $provider): void
    {
        $provider->register($this);
    }
    
    private array $middlewares = [];

    public function middleware(array $middlewares): void
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
    }

    public function run(\Stride\Core\Http\Request $request): void
    {
        $router = $this->router;
        
        $response = (new \Stride\Core\Http\Pipeline())
            ->send($request)
            ->through($this->middlewares)
            ->then(function ($request) use ($router) {
                // Dispatch to Router
                $match = $router->match($request);
                
                if (!$match) {
                    return (new \Stride\Core\Http\Response())
                        ->setStatus(404)
                        ->setBody('404 Not Found');
                }
                
                // Route Specific Middleware
                $routeMiddleware = $match['middleware'] ?? [];
                $handler = $match['handler'];
                $params = $match['params'] ?? [];
                
                if (!empty($routeMiddleware)) {
                    return (new \Stride\Core\Http\Pipeline())
                        ->send($request)
                        ->through($routeMiddleware)
                        ->then(function ($request) use ($handler, $params) {
                            return $this->dispatch($handler, $params, $request);
                        });
                }
                
                return $this->dispatch($handler, $params, $request);
            });
            
        $response->send();
    }
    
    private function dispatch($handler, $params, $request)
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            // Simple instantiation (can be improved with container/dependency injection)
            $instance = new $class(); 
            return $instance->$method($request, ...$params);
        } elseif (is_callable($handler)) {
            return $handler($request, ...$params);
        }
        
        throw new \RuntimeException("Invalid route handler");
    }
}

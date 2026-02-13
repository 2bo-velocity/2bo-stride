<?php

namespace Stride\Core\Routing;

use Stride\Core\Http\Request;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): self
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => []
        ];
        // Return a proxy to allow chaining ->middleware(...)
        // But Router itself usually manages collection.
        // For simplicity matching the spec "Route::get()->middleware()", 
        // we need to return something that knows the current route.
        // Since we are inside Router instance, we can track "last added route" or return a Route object.
        // The Spec 3.3.1 Router returns "void" but 3.4.3 shows chaining.
        // Let's implementation a simple chaining mechanism on Router itself by tracking last route KEY.
        $this->lastRoute = ['method' => $method, 'path' => $path];
        return $this;
    }
    
    private array $lastRoute = [];

    public function middleware(array $middlewares): self
    {
        if (empty($this->lastRoute)) return $this;
        
        $method = $this->lastRoute['method'];
        $path = $this->lastRoute['path'];
        
        if (isset($this->routes[$method][$path])) {
            $this->routes[$method][$path]['middleware'] = array_merge(
                $this->routes[$method][$path]['middleware'] ?? [],
                $middlewares
            );
        }
        
        return $this;
    }

    public function match(Request $request): ?array
    {
        $method = $request->method();
        $path = $request->path();

        // Exact match
        if (isset($this->routes[$method][$path])) {
            $route = $this->routes[$method][$path];
            return [
                'handler' => $route['handler'],
                'params' => [],
                'middleware' => $route['middleware'] ?? []
            ];
        }

        // Parameter matching
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $route) {
                // $route is now array ['handler' => ..., 'middleware' => ...]
                
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
                $pattern = "#^" . $pattern . "$#";
                
                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    return [
                        'handler' => $route['handler'],
                        'params' => $matches,
                        'middleware' => $route['middleware'] ?? []
                    ];
                }
            }
        }

        return null;
    }
}

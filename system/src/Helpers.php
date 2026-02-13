<?php

use Stride\Core\App;
use Stride\Core\Config\Config;
use Stride\Core\Config\Env;
use Stride\Core\Http\Response;

if (!function_exists('app')) {
    function app(): App
    {
        return App::getInstance();
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('view')) {
    function view(string $file, array $vars = []): string
    {
        return app()->view->render($file, $vars);
    }
}

if (!function_exists('h')) {
    function h(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return base_path('resources/views') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('partial')) {
    function partial(string $name, array $vars = []): void
    {
        extract($vars, EXTR_SKIP);
        $path = view_path('partials/' . $name . '.php');
        if (file_exists($path)) {
            require $path;
        }
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $base = rtrim(config('app.asset_url', ''), '/');
        $version = config('app.asset_version');
        
        $url = ($base ? $base . '/' : '/') . ltrim($path, '/');
        
        if ($version) {
            $url .= '?v=' . $version;
        }
        
        return $url;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \Stride\Core\Security\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . h(csrf_token()) . '">';
    }
}

if (!function_exists('paginate')) {
    function paginate(\Stride\Core\Pagination\Paginator $p, string $param = 'page'): string
    {
        if ($p->pages() <= 1) return '';
        
        $html = '<nav class="pagination">';
        
        // Simple implementation: 1 to Total
        // In real app, we might want "..." for large numbers.
        // Spec 5.3 shows simple loop.
        for ($i = 1; $i <= $p->pages(); $i++) {
            $active = ($i === $p->page) ? ' active' : '';
            // Retain other query params?
            // For now simple append.
            $html .= sprintf(
                '<a href="?%s=%d" class="page-link%s">%d</a> ',
                $param,
                $i,
                $active,
                $i
            );
        }
        
        return $html . '</nav>';
    }
}

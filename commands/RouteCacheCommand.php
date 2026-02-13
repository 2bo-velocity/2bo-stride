<?php

namespace Stride\Commands;

use Stride\Core\Console\Command;

class RouteCacheCommand extends Command
{
    public static string $description = 'Create a route cache file for faster route registration';
    public static ?string $commandName = 'route:cache';

    public function run(array $args = []): void
    {
        echo "Caching routes...\n";

        // To cache routes, we need to load them first. A bit tricky since `App` might have loaded them already
        // or we need to rely on `routes/web.php` returning something or populating Router.
        
        // In this framework, `routes/web.php` calls `$app->router->get(...)`.
        // So we can inspect `$app->router`.
        
        $app = app();
        // Since CLI bootstrap loads routes/web.php (checked in public/index.php, but CLI bootstrap?),
        // let's check `bootstrap.php` for CLI.
        
        // Assuming CLI bootstrap loads routes like index.php does or similar.
        // If not, we might need to require them here.
        file_exists(base_path('routes/web.php')) && require base_path('routes/web.php');
        file_exists(base_path('routes/api.php')) && require base_path('routes/api.php');
        
        // Router property `routes` is private. We might need reflection or a getter.
        // Or adding a `getRoutes()` method to Router.
        // For now, let's assume we add `getRoutes()` to Router or use Reflection.
        // Modification of Router needed.
        
        $reflection = new \ReflectionClass($app->router);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $routes = $property->getValue($app->router);

        $cachePath = base_path('bootstrap/cache/routes.php');
        $content = "<?php\n\nreturn " . var_export($routes, true) . ";\n";
        
        // In a real optimized framework, we would "compile" routes (e.g. regex optimization).
        // Here we just dump the array as per spec 3.3.3 example.
        
        file_put_contents($cachePath, $content);
        
        echo "Routes cached successfully!\n";
    }
}

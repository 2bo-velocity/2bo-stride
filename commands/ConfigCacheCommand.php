<?php

namespace Stride\Commands;

use Stride\Core\Console\Command;
use Stride\Core\Config\Config;

class ConfigCacheCommand extends Command
{
    public static string $description = 'Create a cache file for faster configuration loading';
    public static ?string $commandName = 'config:cache';

    public function run(array $args = []): void
    {
        echo "Caching configuration...\n";

        // Load all config files
        $configPath = base_path('config');
        $files = glob($configPath . '/*.php');
        $config = [];

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config[$key] = require $file;
        }
        
        // Also include .env values via Env handling if needed, 
        // but typically config files use Env::get(). 
        // A true config cache would evaluate Env::get() at runtime OR build time.
        // Spec 3.5.3 says: merge loadEnv and loadConfigFiles.
        // Since `Config` class usually handles this, we can just dump what `Config::all()` has if implemented,
        // or manually rebuild as above.
        // Let's dump the aggregated array.
        
        // Ensure directory exists
        $cacheDir = base_path('bootstrap/cache');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $cachePath = $cacheDir . '/config.php';
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        
        file_put_contents($cachePath, $content);
        
        echo "Configuration cached successfully!\n";
    }

    public static function clear(array $args = []): void
    {
        $cacheFile = base_path('bootstrap/cache/config.php');
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            echo "Configuration cache cleared.\n";
        } else {
            echo "No configuration cache found to clear.\n";
        }
    }
}

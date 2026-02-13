<?php

namespace Stride\Core\Config;

class ConfigGuard
{
    /**
     * Enforce config caching in production
     */
    public static function enforce(): void
    {
        // Check if production
        if (config('app.env') !== 'production') {
            return;
        }

        $cacheFile = base_path('bootstrap/cache/config.php');

        if (!file_exists($cacheFile)) {
            echo "Config cache missing in production.\n";
            echo "Run: php stride config:cache\n";
            exit(10);
        }
    }
}

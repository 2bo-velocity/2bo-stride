<?php

namespace Stride\Core\Runtime;

use Stride\Core\Support\Logger;

class StatelessGuard
{
    /**
     * Enforce stateless configuration checks in production
     */
    public static function enforce(): void
    {
        // Assuming app()->env() or similar way to check environment
        // For now using Config/Env directly as referenced in similar checks
        if (config('app.env') !== 'production') {
            return;
        }

        if (config('session.driver') === 'file') {
            Logger::error("File session in production (not container safe)");
        }

        if (config('cache.driver') === 'file') {
            Logger::error("File cache in production (not container safe)");
        }

        if (config('storage.driver') === 'local') {
            Logger::error("Local storage in production (not container safe)");
        }
    }
}

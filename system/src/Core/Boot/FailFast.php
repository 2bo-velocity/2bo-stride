<?php

namespace Stride\Core\Boot;

class FailFast
{
    /**
     * Perform pre-boot checks
     */
    public static function check(): void
    {
        // 1. Required PHP Extensions
        if (!extension_loaded('pdo')) {
            self::fail("PDO extension missing");
        }

        // Redis is critical for Batch/Queue system if configured
        // We warn or fail depending on strictness. Spec says Exit 20.
        // But maybe only if queue driver is redis? For now, enforcing as per spec.
        if (!extension_loaded('redis')) {
            self::fail("Redis extension missing (required for Queue/Lock)", 20);
        }

        // 2. Storage Writability
        if (!is_writable(base_path('storage'))) {
             self::fail("Storage directory not writable: " . base_path('storage'), 21);
        }

        // 3. Cache directory
        if (!is_dir(base_path('bootstrap/cache'))) {
             // Try to create? Or fail? Spec says fail "Cache directory missing"
             // But usually we might want to create it. Sticking to spec 'FailFast'.
             self::fail("Cache directory missing: " . base_path('bootstrap/cache'), 21);
        }
    }

    private static function fail(string $message, int $code = 1): void
    {
        // Write to stderr
        fwrite(STDERR, "[FailFast] {$message}\n");
        exit($code);
    }
}

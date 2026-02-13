<?php

namespace Stride\Core\Runtime;

use Stride\Core\Support\Logger;

class SignalManager
{
    private static bool $shutdown = false;

    /**
     * Initialize signal handlers
     */
    public static function init(): void
    {
        if (!function_exists('pcntl_signal')) {
            return;
        }

        // SIGTERM (Kubernetes termination)
        pcntl_signal(SIGTERM, function () {
            Logger::error("SIGTERM received"); // Notice in spec, using Error for visibility in simple logger
            self::$shutdown = true;
        });

        // SIGINT (Ctrl+C)
        pcntl_signal(SIGINT, function () {
            Logger::error("SIGINT received");
            self::$shutdown = true;
        });
    }

    /**
     * Check if shutdown is requested
     *
     * @return bool
     */
    public static function shouldShutdown(): bool
    {
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }

        return self::$shutdown;
    }
}

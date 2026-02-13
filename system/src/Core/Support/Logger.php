<?php

namespace Stride\Core\Support;

class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::log('NOTICE', $message, $context);
    }
    
    private static function log(string $level, string $message, array $context = []): void
    {
        $logFile = base_path('storage/logs/app.log');
        $date = date('Y-m-d H:i:s');
        $ctx = $context ? ' ' . json_encode($context) : '';
        $logMessage = sprintf("[%s] %s: %s%s%s", $date, $level, $message, $ctx, PHP_EOL);
        
        // Ensure directory exists
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Also write to stderr for CLI / Docker logs
        error_log(trim($logMessage));
    }
}

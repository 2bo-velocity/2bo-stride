<?php

namespace Stride\Core\Foundation;

class Version
{
    /**
     * Framework Version
     * Semantic Versioning
     */
    public const VERSION = '1.0.0';

    /**
     * Required PHP Version
     */
    public const PHP_REQUIRED = '8.1.0';

    /**
     * Framework Name
     */
    public const FRAMEWORK_NAME = '2bo Stride Framework';

    /**
     * Get version string
     * 
     * @return string
     */
    public static function get(): string
    {
        return self::VERSION;
    }

    /**
     * Get full framework name and version
     * 
     * @return string
     */
    public static function full(): string
    {
        return self::FRAMEWORK_NAME . ' ' . self::VERSION;
    }

    /**
     * Get detailed version info
     * 
     * @return array
     */
    public static function details(): array
    {
        return [
            'framework' => self::FRAMEWORK_NAME,
            'version' => self::VERSION,
            'php_version' => PHP_VERSION,
            'php_required' => self::PHP_REQUIRED,
            'environment' => config('app.env', 'unknown'),
            'safe_mode' => config('app.safe_mode', false),
            'maintenance' => config('app.maintenance', false),
        ];
    }

    /**
     * Check PHP version requirements
     * 
     * @throws \RuntimeException
     */
    public static function checkPhpVersion(): void
    {
        if (version_compare(PHP_VERSION, self::PHP_REQUIRED, '<')) {
            throw new \RuntimeException(
                sprintf(
                    '%s requires PHP %s or higher (current: %s)',
                    self::FRAMEWORK_NAME,
                    self::PHP_REQUIRED,
                    PHP_VERSION
                )
            );
        }
    }

    /**
     * Short version string for logs
     * 
     * @return string Example: "Stride 1.0.0"
     */
    public static function short(): string
    {
        return 'Stride ' . self::VERSION;
    }
}

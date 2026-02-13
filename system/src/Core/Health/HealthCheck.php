<?php

namespace Stride\Core\Health;

use Stride\Core\Deploy\DeployMode;

class HealthCheck
{
    /**
     * Run all health checks
     *
     * @return array Status of various components
     */
    public static function run(): array
    {
        return [
            'db' => self::checkDb(),
            'redis' => self::checkRedis(),
            'queue' => self::checkQueue(),
            'safe_mode' => DeployMode::enabled(),
        ];
    }

    private static function checkDb(): bool
    {
        try {
            \Stride\Core\App::getInstance()->db->master()->query("SELECT 1");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function checkRedis(): bool
    {
        try {
            \Stride\Core\App::getInstance()->redis->ping();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function checkQueue(): bool
    {
        try {
            // Simple check: can we connect to queue/redis?
            // If checkRedis passes, this likely passes too unless queue has specific logic
            return \Stride\Core\App::getInstance()->queue !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

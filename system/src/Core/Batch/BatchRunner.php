<?php

namespace Stride\Core\Batch;

use Stride\Core\Maintenance\MaintenanceGuard;
use Stride\Core\Deploy\DeployMode;
use Stride\Core\Support\Logger;
use Stride\Core\Support\ExitCode;
use Stride\Core\Foundation\Version;

class BatchRunner
{
    /**
     * バッチジョブを実行
     * 
     * @param string $class バッチクラスのFQCN
     * @return int Exit code
     */
    public static function run(string $class): int
    {
        // Version log
        self::logVersion($class);

        // Maintenance & Deploy Mode Check
        MaintenanceGuard::check();
        DeployMode::guardExit();

        // Lock
        $lock = BatchLock::acquire($class);
        if (!$lock) {
            Logger::warning("Batch locked", [
                'job' => $class,
                'version' => Version::get(),
            ]);
            return ExitCode::LOCKED;
        }

        $start = microtime(true);

        try {
            // ... (Checks) ...
            if (!class_exists($class)) {
                Logger::error("Batch class not found", ['job' => $class]);
                return ExitCode::ERROR;
            }
            
            $job = new $class();
            if (!$job instanceof \Stride\Core\Batch\BatchJob) { // Fix interface check
                Logger::error("Class does not implement BatchJob interface", ['job' => $class]);
                return ExitCode::ERROR;
            }
            
            $code = $job->handle();

            Logger::info("Batch success", [
                'job' => $class,
                'version' => Version::get(),
                'ms' => (microtime(true) - $start) * 1000,
                'exit_code' => $code,
            ]);

            return $code;

        } catch (\Throwable $e) {
            Logger::error("Batch error", [
                'job' => $class,
                'version' => Version::get(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ExitCode::ERROR;

        } finally {
            $lock->release();
        }
    }

    /**
     * Log version info
     * 
     * @param string $batchName
     */
    private static function logVersion(string $batchName): void
    {
        Logger::info("Batch starting", [
            'framework' => Version::short(),
            'php' => PHP_VERSION,
            'batch' => $batchName,
        ]);
    }
}

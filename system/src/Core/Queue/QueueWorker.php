<?php

namespace Stride\Core\Queue;

use Stride\Core\Deploy\DeployMode;
use Stride\Core\Support\Logger;
use Stride\Core\Runtime\SignalManager;

class QueueWorker
{
    public function run(): void
    {
        $maxJobs = (int) config('worker.max_jobs', 1000);
        $maxMemoryMb = (int) config('worker.max_memory_mb', 256);
        $maxRuntimeSec = (int) config('worker.max_runtime_sec', 3600);
        
        $start = time();
        $jobCount = 0;
        
        // Ensure signal manager is initialized if available
        if (class_exists(SignalManager::class)) {
            SignalManager::init();
        }

        // Worker main loop
        Logger::info("Worker starting", [
            'framework' => \Stride\Core\Foundation\Version::short(),
            'php' => PHP_VERSION,
        ]);

        $draining = false;
        $logged = false;

        while (true) {
             // Check Safe Mode (Deploy)
            if (DeployMode::enabled()) {
                $draining = true;

                if (!$logged) {
                    Logger::notice("Worker entering drain mode", [
                        'version' => \Stride\Core\Foundation\Version::get(),
                    ]);
                    echo "[" . \Stride\Core\Foundation\Version::short() . "] Worker draining. No new jobs.\n";
                    $logged = true;
                }
            }

            // Draining check
            if ($draining) {
                break;
            }

            // Check Process Limits
            if ($jobCount >= $maxJobs) {
                Logger::notice("Worker max jobs reached ({$jobCount})");
                break;
            }

            if (memory_get_usage(true) > $maxMemoryMb * 1024 * 1024) {
                Logger::notice("Worker max memory reached");
                break;
            }

            if (time() - $start > $maxRuntimeSec) {
                Logger::notice("Worker max runtime reached");
                break;
            }
            
            // Check Sigterm
            if (class_exists(SignalManager::class) && SignalManager::shouldShutdown()) {
                 Logger::notice("Worker graceful shutdown (Signal)");
                 break;
            }

            $job = app()->queue->pop();

            if (!$job) {
                sleep(1);
                continue;
            }

            try {
                // Assuming job has handle method
                if (method_exists($job, 'handle')) {
                    $job->handle();
                }
                $jobCount++;
            } catch (\Throwable $e) {
                Logger::error("Queue job failed", [
                    'version' => \Stride\Core\Foundation\Version::get(),
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        Logger::notice("Worker stopped gracefully", [
            'version' => \Stride\Core\Foundation\Version::get(),
        ]);
    }
}

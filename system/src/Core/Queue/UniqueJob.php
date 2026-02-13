<?php

namespace Stride\Core\Queue;

use Stride\Core\Support\Logger;

class UniqueJob
{
    /**
     * Push a unique job to the queue
     *
     * @param string $key Unique identifier for the job
     * @param mixed $job The job object
     * @param int $ttl Lock duration in seconds
     * @return bool True if queued, False if duplicate
     */
    public static function push(string $key, $job, int $ttl = 300): bool
    {
        $redis = app()->redis;
        $lockKey = "job_unique:{$key}";

        // SET key value NX EX ttl
        $acquired = $redis->set($lockKey, 1, ['nx', 'ex' => $ttl]);

        if (!$acquired) {
            Logger::info("UniqueJob skipped: {$key}");
            return false;
        }

        return app()->queue->push($job);
    }
}

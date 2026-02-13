<?php

namespace Stride\Core\Lock;

class DistributedLock
{
    /**
     * Acquire a distributed lock
     *
     * @param string $key Lock key
     * @param int $ttl Lock TTL in seconds
     * @return bool True if lock acquired, false otherwise
     */
    public static function acquire(string $key, int $ttl = 30): bool
    {
        // SET key value NX EX ttl
        return (bool) app()->redis->set(
            "lock:{$key}",
            1,
            ['nx', 'ex' => $ttl]
        );
    }

    /**
     * Release a distributed lock
     *
     * @param string $key Lock key
     */
    public static function release(string $key): void
    {
        app()->redis->del("lock:{$key}");
    }
}

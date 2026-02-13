<?php

namespace Stride\Core\Resilience;

class RateLimiter
{
    private $redis;

    public function __construct($redis = null)
    {
        // Allow null for testing or fallback? Spec implies Redis dependence.
        // App injects it.
        $this->redis = $redis;
    }

    /**
     * Check if action is allowed
     *
     * @param string $key Rate limit key
     * @param int $limit Max requests
     * @param int $seconds Time window
     * @return bool True if allowed
     */
    public function check(string $key, int $limit, int $seconds): bool
    {
        if (!$this->redis) {
            return true; // Fail open if no redis
        }

        // Simple fixed window counter using INCR and EXPIRE
        // Key should be unique per window if strict, or rely on TTL refresh behavior.
        // Spec implementation:
        /*
        $count = $this->redis->incr($key);
        if ($count === 1) {
            $this->redis->expire($key, $ttl);
        }
        return $count <= $limit;
        */
        
        $current = (int) $this->redis->incr($key);
        
        if ($current === 1) {
            $this->redis->expire($key, $seconds);
        }
        
        return $current <= $limit;
    }
}

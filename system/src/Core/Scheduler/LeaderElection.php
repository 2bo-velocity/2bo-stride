<?php

namespace Stride\Core\Scheduler;

class LeaderElection
{
    /**
     * Check if this instance is the leader
     *
     * @return bool
     */
    public static function isLeader(): bool
    {
        // Try to SET key specific to this hostname with NX (only if not exists)
        // TTL 10 seconds (heartbeat needed)
        return (bool) app()->redis->set(
            "scheduler:leader",
            gethostname(),
            ['nx', 'ex' => 10]
        );
    }

    /**
     * Refresh leadership (heartbeat)
     */
    public static function refresh(): void
    {
        // Extend TTL
        app()->redis->expire("scheduler:leader", 10);
    }
}

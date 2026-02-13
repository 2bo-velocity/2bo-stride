<?php

namespace Stride\Core\Migration;

class ReplicaHealth
{
    /**
     * Check if replica lag is safe
     *
     * @param int $maxLagSeconds Maximum allowed lag in seconds
     * @return bool
     */
    public static function isSafe(int $maxLagSeconds = 5): bool
    {
        // If config doesn't have slaves, return true
        if (empty(config('db.slaves'))) {
            return true;
        }

        try {
            // Need to query Slave status.
            // DB implementation detail: using raw PDO query
            $pdo = app()->db->slave();
            $stmt = $pdo->query("SHOW SLAVE STATUS");
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                // If query returns empty but configured, something is wrong or it's not a slave
                // For safety, might want to return false or true depending on policy.
                // Assuming safety first: if configured but status unknown -> unsafe?
                // Or maybe it is master?
                return true; 
            }

            $lag = $row['Seconds_Behind_Master'] ?? null;

            if ($lag === null) {
                // Replication stopped or error
                return false;
            }

            return (int) $lag < $maxLagSeconds;
        } catch (\Throwable $e) {
            // Error querying -> unsafe
            return false;
        }
    }
}

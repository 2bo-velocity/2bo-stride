<?php

namespace Stride\Core\CLI\Commands;

use Stride\Core\Migration\ReplicaHealth;

class ReplicaCommand
{
    public function check(): void
    {
        echo "Checking Replica Health...\n";
        
        // This relies on ReplicaHealth class and configured slaves
        if (empty(config('db.slaves'))) {
            echo "No replicas configured. Skipping check.\n";
            return;
        }

        if (ReplicaHealth::isSafe(5)) {
            echo "Replica Status: OK (Safe for operations)\n";
        } else {
            echo "Replica Status: UNHEALTHY (High lag or disconnected)\n";
            exit(1);
        }
    }
}

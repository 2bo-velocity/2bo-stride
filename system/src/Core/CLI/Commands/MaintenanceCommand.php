<?php

namespace Stride\Core\CLI\Commands;

use Stride\Core\Support\EnvWriter;
use Stride\Core\Support\Logger;
use Stride\Core\Config\Env;

class MaintenanceCommand
{
    public function on(): void
    {
        echo "Enabling Maintenance Mode...\n";
        EnvWriter::set('APP_MAINTENANCE', 'true');
        Logger::notice("Maintenance Mode Enabled via CLI");
        echo "Maintenance Mode is now ON.\n";
    }

    public function off(): void
    {
        echo "Disabling Maintenance Mode...\n";
        EnvWriter::set('APP_MAINTENANCE', 'false');
        Logger::notice("Maintenance Mode Disabled via CLI");
        echo "Maintenance Mode is now OFF.\n";
    }

    public function status(): void
    {
        $status = Env::get('APP_MAINTENANCE') === 'true' ? 'ON' : 'OFF';
        echo "Maintenance Mode: {$status}\n";
    }
}

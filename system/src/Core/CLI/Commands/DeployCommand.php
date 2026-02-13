<?php

namespace Stride\Core\CLI\Commands;

use Stride\Core\Support\EnvWriter;
use Stride\Core\Support\Logger;

class DeployCommand
{
    /**
     * Enable Deploy Mode (Safe Mode ON)
     */
    public function on(): void
    {
        echo "Enabling Deploy Safe Mode...\n";
        EnvWriter::set('APP_SAFE_MODE', 'true');
        Logger::notice("Deploy Safe Mode Enabled via CLI");
        echo "Deploy Safe Mode is now ON.\n";
    }

    /**
     * Disable Deploy Mode (Safe Mode OFF)
     */
    public function off(): void
    {
        echo "Disabling Deploy Safe Mode...\n";
        EnvWriter::set('APP_SAFE_MODE', 'false');
        Logger::notice("Deploy Safe Mode Disabled via CLI");
        echo "Deploy Safe Mode is now OFF.\n";
    }

    public function status(): void
    {
        $status = \Stride\Core\Config\Env::get('APP_SAFE_MODE') === 'true' ? 'ON' : 'OFF';
        echo "Deploy Safe Mode: {$status}\n";
    }
}

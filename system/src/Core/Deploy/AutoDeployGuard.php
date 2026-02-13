<?php

namespace Stride\Core\Deploy;

use Stride\Core\Support\Logger;
use Stride\Core\Support\EnvWriter;

class AutoDeployGuard
{
    /**
     * Wrap execution with auto safe mode
     * 
     * @param callable $fn
     * @return mixed
     */
    public static function wrap(callable $fn)
    {
        // Don't enable if already enabled? 
        // Spec says: AutoDeployGuard::wrap(function() { ... });
        // It should implicitly handle ON/OFF.
        
        Logger::notice("Auto deploy guard ON");
        echo "Enabling Deploy Safe Mode...\n";
        EnvWriter::set('APP_SAFE_MODE', 'true');

        try {
            return $fn();
        } finally {
            EnvWriter::set('APP_SAFE_MODE', 'false');
            echo "Disabling Deploy Safe Mode...\n";
            Logger::notice("Auto deploy guard OFF");
        }
    }
}

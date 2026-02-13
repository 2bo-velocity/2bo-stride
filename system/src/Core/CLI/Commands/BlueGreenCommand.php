<?php

namespace Stride\Core\CLI\Commands;

use Stride\Core\Support\EnvWriter;
use Stride\Core\Support\Logger;
use Stride\Core\Config\Env;

class BlueGreenCommand
{
    /**
     * Set the active environment color
     * 
     * @param array $args Arguments, expecting the color as first argument
     */
    public function set(array $args): void
    {
        $color = $args[0] ?? null;
        
        if (!in_array($color, ['blue', 'green'])) {
            echo "Usage: stride bluegreen:set <blue|green>\n";
            return;
        }

        echo "Switching active environment to: {$color}...\n";
        
        // This assumes we have a config or env var for this.
        // The spec implies config('app.bluegreen') which typically reads from env.
        EnvWriter::set('APP_BLUEGREEN', $color);
        
        Logger::notice("Blue/Green Switched to {$color} via CLI");
        echo "Environment is now: {$color}\n";
    }
    
    public function status(): void
    {
        $color = Env::get('APP_BLUEGREEN', 'blue');
        echo "Current Active Environment: {$color}\n";
    }
}

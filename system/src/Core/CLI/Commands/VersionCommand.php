<?php

namespace Stride\Core\CLI\Commands;

use Stride\Core\Foundation\Version;

class VersionCommand
{
    /**
     * Show basic version
     */
    public function handle(): void
    {
        echo Version::full() . PHP_EOL;
        echo 'PHP ' . PHP_VERSION . PHP_EOL;
    }

    /**
     * Show full version details
     */
    public function handleFull(): void
    {
        $details = Version::details();

        echo Version::FRAMEWORK_NAME . PHP_EOL;
        echo str_repeat('-', 50) . PHP_EOL;
        echo sprintf("Version:          %s\n", $details['version']);
        echo sprintf("PHP Version:      %s\n", $details['php_version']);
        echo sprintf("PHP Required:     %s\n", $details['php_required']);
        echo sprintf("Environment:      %s\n", $details['environment']);
        echo sprintf("Safe Mode:        %s\n", $details['safe_mode'] ? 'ON' : 'OFF');
        echo sprintf("Maintenance Mode: %s\n", $details['maintenance'] ? 'ON' : 'OFF');
    }
}

<?php

namespace Stride\Core\CLI;

use Stride\Core\CLI\Commands\VersionCommand;
use Stride\Core\Foundation\Version;

class Console
{
    private array $commands = [];

    public function __construct()
    {
        // Check PHP Version requirements
        Version::checkPhpVersion();

        // Register Version Commands
        $this->registerVersionCommands();
    }

    /**
     * Register version related commands
     */
    private function registerVersionCommands(): void
    {
        $versionCmd = new VersionCommand();

        // Basic Version
        $this->commands['version'] = [$versionCmd, 'handle'];
        $this->commands['--version'] = [$versionCmd, 'handle'];
        $this->commands['-V'] = [$versionCmd, 'handle'];

        // Full Version
        $this->commands['version:full'] = [$versionCmd, 'handleFull'];
        $this->commands['--version-full'] = [$versionCmd, 'handleFull'];

        // Deploy Commands
        $deployCmd = new \Stride\Core\CLI\Commands\DeployCommand();
        $this->commands['deploy:on'] = [$deployCmd, 'on'];
        $this->commands['deploy:off'] = [$deployCmd, 'off'];
        $this->commands['deploy:status'] = [$deployCmd, 'status'];

        // Maintenance Commands
        $maintCmd = new \Stride\Core\CLI\Commands\MaintenanceCommand();
        $this->commands['maintenance:on'] = [$maintCmd, 'on'];
        $this->commands['maintenance:off'] = [$maintCmd, 'off'];
        $this->commands['maintenance:status'] = [$maintCmd, 'status'];

        // Core Util Commands
        $schemaCmd = new \Stride\Core\CLI\Commands\SchemaCommand();
        $this->commands['schema:version'] = [$schemaCmd, 'version'];

        $replicaCmd = new \Stride\Core\CLI\Commands\ReplicaCommand();
        $this->commands['replica:check'] = [$replicaCmd, 'check'];
        
        // Blue/Green Commands
        $bgCmd = new \Stride\Core\CLI\Commands\BlueGreenCommand();
        $this->commands['bluegreen:set'] = [$bgCmd, 'set'];
        $this->commands['bluegreen:status'] = [$bgCmd, 'status'];

        // Config Clear - Mapping to static method in existing Command located in commands/
        // BUT wait, ConfigCacheCommand in commands/ extends Command which usually has `run`.
        // The `stride` script adapts `run` automatically for `commands/*.php`.
        // But `config:clear` logic was just added as `clear` static method to `ConfigCacheCommand` class.
        // We need to register it manually here if we want `stride config:clear` to work 
        // AND not rely on `stride` script's glob loop for this specific sub-command.
        // The `stride` script registers `config:cache` via `commandName` property.
        // `config:clear` is NOT the main command name of that class.
        // So we register it manually here.
        $this->commands['config:clear'] = [\Stride\Commands\ConfigCacheCommand::class, 'clear'];
    }

    /**
     * Register a command handler
     * 
     * @param string $name Command name
     * @param callable $handler Callable handler
     */
    public function registerCommand(string $name, callable $handler): void
    {
        $this->commands[$name] = $handler;
    }

    /**
     * Run the console application
     * 
     * @param array $argv Command line arguments
     */
    public function run(array $argv): void
    {
        // If no arguments, show help
        if (count($argv) < 2) {
            $this->showHelp();
            return;
        }

        $command = $argv[1];

        // Execute command
        if (isset($this->commands[$command])) {
            call_user_func($this->commands[$command], array_slice($argv, 2));
            return;
        }

        echo "Unknown command: {$command}\n";
        $this->showHelp();
        exit(1);
    }

    /**
     * Show Help
     */
    private function showHelp(): void
    {
        echo Version::full() . PHP_EOL . PHP_EOL;
        echo "Usage: stride <command> [options]\n\n";
        echo "Available commands:\n";
        echo "  version, --version, -V    Show framework version\n";
        echo "  version:full              Show detailed version info\n";
        echo "  deploy:on                 Enable Deploy Safe Mode\n";
        echo "  deploy:off                Disable Deploy Safe Mode\n";
        echo "  deploy:status             Show Deploy Safe Mode status\n";
        
        echo "  maintenance:on            Enable Maintenance Mode\n";
        echo "  maintenance:off           Disable Maintenance Mode\n";
        echo "  maintenance:status        Show Maintenance Mode status\n";

        echo "  config:cache              Cache configuration\n";
        echo "  config:clear              Clear configuration cache\n";

        echo "  schema:version            Check DB schema version\n";
        echo "  replica:check             Check DB replica health\n";
        
        echo "  batch:list                List available batch jobs\n";
        echo "  batch:run <name>          Run a batch job\n";
        echo "  migrate                   Run database migrations\n";
        echo "  make:controller           Create a new controller\n";
        echo "  route:cache               Cache routes\n";
        
        echo "\n";
    }
}

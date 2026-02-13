<?php

namespace Stride\Commands;

use Stride\Core\Console\Command;
use Stride\Core\Batch\BatchRegistry;
use Stride\Core\Batch\BatchRunner;
use Stride\Core\Config\Config;

class BatchRunCommand extends Command
{
    public static string $description = 'Run a specific batch job';
    public static ?string $commandName = 'batch:run';
    
    private BatchRegistry $registry;

    public function __construct()
    {
        $batchConfig = Config::get('batch', []);
        $this->registry = new BatchRegistry($batchConfig);
    }
    
    public function run(array $args = []): void
    {
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Usage: stride batch:run <name>\n";
            exit(1);
        }
        
        try {
            $class = $this->registry->resolve($name);
            $code = BatchRunner::run($class);
            exit($code);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}

<?php

namespace Stride\Commands;

use Stride\Core\Console\Command;
use Stride\Core\Batch\BatchRegistry;
use Stride\Core\Config\Config;

class BatchListCommand extends Command
{
    public static string $description = 'List available batch jobs';
    public static ?string $commandName = 'batch:list';
    
    private BatchRegistry $registry;

    public function __construct()
    {
        $batchConfig = Config::get('batch', []);
        $this->registry = new BatchRegistry($batchConfig);
    }
    
    public function run(array $args = []): void
    {
        echo "Available batches:\n";
        foreach ($this->registry->list() as $name) {
            echo "  - {$name}\n";
        }
    }
}

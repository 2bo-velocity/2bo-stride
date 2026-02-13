<?php

namespace Stride\Core\Console;

abstract class Command
{
    public static string $description = '';
    public static ?string $commandName = null;
    
    abstract public function run(array $args = []): void;
    
    protected function loadEnv(): array
    {
        // Helper to load env for commands
        return []; 
    }
    
    protected function loadConfigFiles(): array
    {
        // Helper to load config
        return [];
    }
}

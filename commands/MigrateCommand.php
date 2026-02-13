<?php

namespace Stride\Commands;

use Stride\Core\Console\Command;
use Stride\Core\Support\Logger;
use PDO;

class MigrateCommand extends Command
{
    public static string $description = 'Run database migrations';
    public static ?string $commandName = 'migrate';

    public function run(array $args = []): void
    {
        \Stride\Core\Deploy\AutoDeployGuard::wrap(function() use ($args) {
            $this->execute($args);
        });
    }

    private function execute(array $args): void
    {
        Logger::info("Starting migration...");
        
        $pdo = app()->db->master();
        $this->ensureMigrationsTable($pdo);
        
        $executed = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        $files = glob(base_path('database/migrations/*.php'));
        
        $batch = (int) $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() + 1;
        
        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            if (in_array($migrationName, $executed)) {
                continue;
            }
            
            require_once $file;
            
            // Simple class resolution
            $content = file_get_contents($file);
            if (preg_match('/class\s+(\w+)\s+extends\s+Migration/i', $content, $matches)) {
                $className = $matches[1];
                if (preg_match('/namespace\s+([\w\\\\]+);/i', $content, $nsMatches)) {
                    $className = $nsMatches[1] . '\\' . $className;
                }
                
                $instance = new $className();
                
                echo "Migrating: {$migrationName}\n";
                
                // Replica Health Check
                if (!\Stride\Core\Migration\ReplicaHealth::isSafe()) {
                    Logger::error("Replica lag too high. Aborting migration: {$migrationName}");
                    echo "Replica lag too high. Aborting.\n";
                    return;
                }
                
                $pdo->beginTransaction();
                try {
                    $instance->up();
                    $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                    $stmt->execute([$migrationName, $batch]);
                    $pdo->commit();
                    Logger::info("Migrated: {$migrationName}");
                    echo "Migrated: {$migrationName}\n";
                } catch (\Throwable $e) {
                    $pdo->rollBack();
                    Logger::error("Migration failed: {$migrationName}. Error: " . $e->getMessage());
                    echo "Migration failed: {$migrationName}\n";
                    throw $e;
                }
            }
        }
        
        Logger::info("Migration finished.");
    }
    
    private function ensureMigrationsTable(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                batch INT
            )
        ");
    }
}

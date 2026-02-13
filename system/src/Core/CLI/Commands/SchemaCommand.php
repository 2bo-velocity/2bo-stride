<?php

namespace Stride\Core\CLI\Commands;

class SchemaCommand
{
    public function version(): void
    {
        try {
            $pdo = app()->db->master();
            // Check if schema_meta exists
            $exists = $pdo->query("SHOW TABLES LIKE 'schema_meta'")->rowCount() > 0;
            
            if (!$exists) {
                echo "Schema Version: N/A (table schema_meta not found)\n";
                return;
            }

            $current = $pdo->query("SELECT version FROM schema_meta LIMIT 1")->fetchColumn();
            $expected = config('schema.expected_version', 'Not setup');

            echo "Current DB Schema Version: " . ($current ?: 'None') . "\n";
            echo "Expected/Configured Version: " . $expected . "\n";

            if ($expected && $current != $expected) {
                echo "WARNING: Schema version mismatch!\n";
            }
        } catch (\Throwable $e) {
            echo "Error checking schema version: " . $e->getMessage() . "\n";
        }
    }
}

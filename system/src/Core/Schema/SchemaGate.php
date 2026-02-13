<?php

namespace Stride\Core\Schema;

use RuntimeException;

class SchemaGate
{
    /**
     * Check if database schema version matches application expectation
     *
     * @throws RuntimeException
     */
    public static function check(): void
    {
        $expected = config('schema.expected_version');

        if (!$expected) {
            return;
        }

        try {
            $pdo = app()->db->master();
            $stmt = $pdo->query("SELECT version FROM schema_meta LIMIT 1");
            $dbVersion = $stmt->fetchColumn();

            if ($dbVersion != $expected) {
                 throw new RuntimeException(
                    "Schema version mismatch.\n" .
                    "Database: {$dbVersion}\n" .
                    "Application: {$expected}\n" .
                    "Run migration or rollback application."
                );
            }
        } catch (\Throwable $e) {
            // Table might not exist yet -> mismatch if expected is set
             throw new RuntimeException(
                "Schema version check failed: " . $e->getMessage()
            );
        }
    }
}

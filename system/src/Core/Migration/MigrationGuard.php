<?php

namespace Stride\Core\Migration;

use RuntimeException;

class MigrationGuard
{
    /**
     * Check for dangerous migration operations
     *
     * @param string $sql SQL to execute
     * @throws RuntimeException if dangerous operation detected
     */
    public static function check(string $sql): void
    {
        $dangerous = [
            'drop column',
            'alter column',
            'set not null',
            'drop table',
            'drop index',
        ];

        $sqlLower = strtolower($sql);

        foreach ($dangerous as $pattern) {
            if (str_contains($sqlLower, $pattern)) {
                throw new RuntimeException(
                    "Dangerous migration detected: {$pattern}\n" .
                    "SQL: {$sql}"
                );
            }
        }
    }
}

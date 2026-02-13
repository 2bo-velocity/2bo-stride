<?php

namespace Stride\Core\Support;

class EnvWriter
{
    /**
     * Update or add an environment variable in .env file
     * 
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set(string $key, string $value): void
    {
        $path = base_path('.env');
        
        if (!file_exists($path)) {
            // Create if not exists
            file_put_contents($path, "{$key}={$value}\n");
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $newLines = [];
        $found = false;

        foreach ($lines as $line) {
            // Check if line starts with key=
            if (str_starts_with(trim($line), $key . '=')) {
                $newLines[] = "{$key}={$value}";
                $found = true;
            } else {
                $newLines[] = $line;
            }
        }

        if (!$found) {
            $newLines[] = "{$key}={$value}";
        }

        file_put_contents($path, implode("\n", $newLines) . "\n");
    }
}

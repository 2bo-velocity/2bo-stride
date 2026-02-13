<?php

namespace Stride\Core\Config;

class Config
{
    private static array $items = [];

    public static function load(string $path): void
    {
        $files = glob($path . '/*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            self::$items[$key] = require $file;
        }
    }

    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $data = self::$items;

        foreach ($segments as $segment) {
            if (!isset($data[$segment])) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }

    public static function set(string $key, $value): void
    {
        $segments = explode('.', $key);
        $data = &self::$items;

        foreach ($segments as $segment) {
            if (!isset($data[$segment])) {
                $data[$segment] = [];
            }
            $data = &$data[$segment];
        }

        $data = $value;
    }
    
    public static function all(): array
    {
        return self::$items;
    }
}

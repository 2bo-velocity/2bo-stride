<?php

namespace Stride\Core\Session;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Check for production stateless guard or config?
            // Spec says just start.
            session_start();
        }
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }
}

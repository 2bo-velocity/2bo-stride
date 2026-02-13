<?php

namespace Stride\Core\Security;

use Stride\Core\Session\Session;

class Csrf
{
    private const KEY = '_csrf';

    /**
     * Get or generate a CSRF token
     *
     * @return string
     */
    public static function token(): string
    {
        Session::start();
        
        $token = Session::get(self::KEY);
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::KEY, $token);
        }
        
        return $token;
    }

    /**
     * Check if the provided token matches the session token
     *
     * @param string|null $token
     * @return bool
     */
    public static function check(?string $token): bool
    {
        Session::start();
        
        return hash_equals(
            Session::get(self::KEY) ?? '',
            $token ?? ''
        );
    }
}

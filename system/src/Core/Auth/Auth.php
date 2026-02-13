<?php

namespace Stride\Core\Auth;

use Stride\Core\Session\Session;

class Auth
{
    /**
     * Get the currently logged in user ID
     *
     * @return int|null
     */
    public static function id(): ?int
    {
        return Session::get('uid');
    }

    /**
     * Log in a user by ID
     *
     * @param int $uid
     */
    public static function login(int $uid): void
    {
        Session::set('uid', $uid);
    }

    /**
     * Log out the current user
     */
    public static function logout(): void
    {
        Session::remove('uid');
    }

    /**
     * Check if a user is logged in
     *
     * @return bool
     */
    public static function check(): bool
    {
        return self::id() !== null;
    }
}

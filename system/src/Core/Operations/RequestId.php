<?php

namespace Stride\Core\Operations;

class RequestId
{
    private static ?string $id = null;

    /**
     * Get current Request ID, generating if not set
     *
     * @return string
     */
    public static function get(): string
    {
        return self::$id ??= bin2hex(random_bytes(8));
    }

    /**
     * Set Request ID (e.g. from upstream header)
     *
     * @param string $id
     */
    public static function set(string $id): void
    {
        self::$id = $id;
    }
}

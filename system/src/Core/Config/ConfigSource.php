<?php

namespace Stride\Core\Config;

class ConfigSource
{
    /**
     * Get config value from Environment variables first, then .env/config
     *
     * @param string $key Config key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Check actual environment variable first (for Kubernetes ConfigMap/Secrets)
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        // Fallback to Env class (loaded from .env)
        return Env::get($key, $default);
    }
}

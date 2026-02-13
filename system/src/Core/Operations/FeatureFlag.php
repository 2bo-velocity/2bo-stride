<?php

namespace Stride\Core\Operations;

class FeatureFlag
{
    private array $flags = [];

    public function __construct(array $flags = [])
    {
        $this->flags = $flags;
    }

    /**
     * Check if a feature is enabled
     *
     * @param string $key Feature key
     * @return bool
     */
    public function isEnabled(string $key): bool
    {
        return !empty($this->flags[$key]);
    }
    
    /**
     * Load flags from array
     */
    public function load(array $flags): void
    {
        $this->flags = $flags;
    }
}

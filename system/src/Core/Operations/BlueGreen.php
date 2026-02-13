<?php

namespace Stride\Core\Operations;

class BlueGreen
{
    public function getActive(): string
    {
        return config('app.bluegreen', 'blue');
    }

    public function isBlue(): bool
    {
        return $this->getActive() === 'blue';
    }

    public function isGreen(): bool
    {
        return $this->getActive() === 'green';
    }
}

<?php

namespace Stride\Core\Container;

use Stride\Core\App;

interface ServiceProvider
{
    public function register(App $app): void;
}

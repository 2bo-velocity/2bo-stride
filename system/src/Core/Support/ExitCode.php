<?php

namespace Stride\Core\Support;

class ExitCode
{
    public const SUCCESS = 0;
    public const ERROR = 1;
    public const LOCKED = 2;
    public const MAINTENANCE = 3;
    public const SAFE_MODE = 4;
    public const CONFIG_ERROR = 10;
    public const EXTENSION_MISSING = 20;
    public const STORAGE_ERROR = 21;
}

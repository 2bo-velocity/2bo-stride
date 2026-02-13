<?php

namespace Stride\Core\Database;

use Stride\Core\App;
use Stride\Core\Container\ServiceProvider;
use Stride\Core\Config\Config;
use Stride\Core\Database\ConnectionManager;

class DatabaseProvider implements ServiceProvider
{
    public function register(App $app): void
    {
        $dbConfig = Config::get('db');
        
        if ($dbConfig) {
            $app->db = new ConnectionManager($dbConfig);
        }
    }
}

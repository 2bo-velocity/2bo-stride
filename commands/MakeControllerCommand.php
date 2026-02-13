<?php

use Stride\Core\Console\Command;

class MakeControllerCommand extends Command
{
    public static string $description = 'Create a new controller';

    public function run(array $args = []): void
    {
        $name = $args[0] ?? null;
        if (!$name) {
            echo "Usage: stride make:controller <name>\n";
            return;
        }

        $template = <<<PHP
<?php

namespace App\Controllers;

use Stride\Core\Http\Request;
use Stride\Core\Http\Response;

class {$name}
{
    public function index(Request \$request)
    {
        return view('home');
    }
}
PHP;

        $path = base_path("app/Controllers/{$name}.php");
        
        if (file_exists($path)) {
            echo "Controller already exists!\n";
            return;
        }

        file_put_contents($path, $template);
        echo "Controller created: {$path}\n";
    }
}

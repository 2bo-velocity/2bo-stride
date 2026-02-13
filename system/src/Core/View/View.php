<?php

namespace Stride\Core\View;

use RuntimeException;

class View
{
    private string $basePath;
    private string $layout = 'layout/main';

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function render(string $file, array $vars = []): string
    {
        $content = $this->renderFile($file, $vars);

        if ($this->layout) {
            return $this->renderFile($this->layout, [
                ...$vars,
                'content' => $content
            ]);
        }

        return $content;
    }

    private function renderFile(string $file, array $vars): string
    {
        $path = $this->basePath . '/' . $file . '.php';

        if (!file_exists($path)) {
            throw new RuntimeException("View not found: $file");
        }

        extract($vars, EXTR_SKIP);

        ob_start();
        require $path;
        return ob_get_clean();
    }
}

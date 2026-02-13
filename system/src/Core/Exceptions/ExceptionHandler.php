<?php

namespace Stride\Core\Exceptions;

use Stride\Core\Support\Logger;
use Stride\Core\Http\Response;

class ExceptionHandler
{
    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($level, $message, $file = '', $line = 0): void
    {
        if (error_reporting() & $level) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public function handleException(\Throwable $e): void
    {
        Logger::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => explode(PHP_EOL, $e->getTraceAsString())
        ]);

        if (php_sapi_name() === 'cli') {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            return;
        }

        $debug = config('app.debug', false);

        if ($debug) {
            echo "<h1>Error</h1>";
            echo "<p>{$e->getMessage()}</p>";
            echo "<pre>{$e->getTraceAsString()}</pre>";
        } else {
            // Friendly error page
            http_response_code(500);
            if (request()->header('Accept') === 'application/json') {
                echo json_encode(['error' => 'Server Error']);
            } else {
                echo "<h1>500 Server Error</h1>";
            }
        }
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->handleException(new \ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }
}

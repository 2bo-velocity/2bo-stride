<?php

namespace App\Batch;

use Stride\Core\Batch\BatchJob;
use Stride\Core\Support\Logger;
use Stride\Core\Support\ExitCode;

class ExampleBatch implements BatchJob
{
    public function handle(): int
    {
        Logger::info("Example batch started.");
        
        // Simulating some work
        sleep(1);
        
        Logger::info("Example batch finished.");
        
        return ExitCode::SUCCESS;
    }
}

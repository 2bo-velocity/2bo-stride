<?php

return [
    'max_jobs' => (int) env('WORKER_MAX_JOBS', 1000),
    'max_memory_mb' => (int) env('WORKER_MAX_MEMORY_MB', 256),
    'max_runtime_sec' => (int) env('WORKER_MAX_RUNTIME_SEC', 3600),
];

<?php

namespace Stride\Core\Resilience;

use RuntimeException;
use Throwable;

class CircuitBreaker
{
    private array $failures = [];
    private int $threshold;
    private int $timeout;

    public function __construct(int $threshold = 5, int $timeout = 60)
    {
        $this->threshold = $threshold;
        $this->timeout = $timeout;
    }

    /**
     * Execute a callback with circuit breaker protection
     *
     * @param string $key Service identifier
     * @param callable $fn Function to execute
     * @return mixed
     * @throws Throwable
     */
    public function call(string $key, callable $fn): mixed
    {
        $now = time();

        // Check if open (timeout active)
        if (isset($this->failures[$key]['until'])) {
            if ($this->failures[$key]['until'] > $now) {
                throw new RuntimeException("Circuit breaker open for $key");
            }
            // Timeout expired, try again (half-open state effectively)
            unset($this->failures[$key]['until']);
            // Keep count? Or reset? Spec implies simple logic.
            // Usually reset on success.
        }

        try {
            $result = $fn();
            // Success: reset failures
            unset($this->failures[$key]);
            return $result;
        } catch (Throwable $e) {
            $this->failures[$key]['count'] = ($this->failures[$key]['count'] ?? 0) + 1;

            if ($this->failures[$key]['count'] >= $this->threshold) {
                $this->failures[$key]['until'] = $now + $this->timeout;
            }

            throw $e;
        }
    }
}

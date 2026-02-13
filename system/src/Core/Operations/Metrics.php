<?php

namespace Stride\Core\Operations;

class Metrics
{
    private array $counters = [];

    /**
     * Increment a counter
     *
     * @param string $name Metric name
     * @param int $value Value to increment by
     */
    public function inc(string $name, int $value = 1): void
    {
        $this->counters[$name] = ($this->counters[$name] ?? 0) + $value;
    }

    /**
     * Render metrics in Prometheus text format
     *
     * @return string
     */
    public function render(): string
    {
        $out = '';
        foreach ($this->counters as $key => $value) {
            $out .= "{$key} {$value}\n";
        }
        return $out;
    }
}

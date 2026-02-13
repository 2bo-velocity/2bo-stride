<?php

namespace Stride\Core\Http;

class Pipeline
{
    private array $pipes = [];
    private Request $request;

    public function send(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function through(array $pipes): self
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function then(callable $destination): Response
    {
        $pipeline = $destination;

        foreach (array_reverse($this->pipes) as $pipe) {
            $pipeline = function ($request) use ($pipe, $pipeline) {
                if (is_string($pipe)) {
                    // Simple instantiation
                    $pipeInstance = new $pipe();
                    return $pipeInstance($request, $pipeline);
                } elseif (is_callable($pipe)) {
                    return $pipe($request, $pipeline);
                } elseif (is_object($pipe) && method_exists($pipe, '__invoke')) {
                    return $pipe($request, $pipeline);
                }
                
                // Invalid pipe, skip or throw
                throw new \RuntimeException("Invalid middleware in pipeline");
            };
        }

        return $pipeline($this->request);
    }
}

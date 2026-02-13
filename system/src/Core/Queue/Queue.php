<?php

namespace Stride\Core\Queue;

class Queue
{
    private $redis;
    private string $key = 'queue:default';

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * Push a job to the queue
     *
     * @param mixed $job Serializable job object
     * @return bool
     */
    public function push($job): bool
    {
        $payload = serialize($job);
        return (bool) $this->redis->rpush($this->key, $payload);
    }

    /**
     * Pop a job from the queue
     *
     * @return mixed|null Unserialized job or null
     */
    public function pop()
    {
        // BLPOP is better but for simplicity and non-blocking worker loop in spec
        // we use lpop. Spec uses `Queue::pop()` in worker.
        $payload = $this->redis->lpop($this->key);
        
        if (!$payload) {
            return null;
        }

        return unserialize($payload);
    }
}

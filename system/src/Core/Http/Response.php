<?php

namespace Stride\Core\Http;

class Response
{
    private int $status = 200;
    private array $headers = [];
    private string $body = '';

    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function setStatus(int $code): self
    {
        $this->status = $code;
        return $this;
    }

    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function json(array $data): self
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setBody(json_encode($data));
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->body;
    }
    
    public function status(): int
    {
        return $this->status;
    }
    
    public function body(): string
    {
        return $this->body;
    }
}

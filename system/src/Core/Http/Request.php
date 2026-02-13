<?php

namespace Stride\Core\Http;

class Request
{
    private array $server;
    private array $headers;
    private array $query;
    private array $post;
    private string $body;
    private array $cookies;
    private array $files;

    public function __construct(
        array $query = [],
        array $post = [],
        array $server = [],
        string $body = '',
        array $cookies = [],
        array $files = []
    ) {
        $this->query = $query;
        $this->post = $post;
        $this->server = $server;
        $this->body = $body;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->headers = $this->parseHeaders($server);
    }
    
    public static function capture(): self
    {
        return new self(
            $_GET,
            $_POST,
            $_SERVER,
            file_get_contents('php://input'),
            $_COOKIE,
            $_FILES
        );
    }

    private function parseHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$headerName] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = str_replace('_', '-', strtolower($key));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function header(string $key, $default = null)
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return parse_url($uri, PHP_URL_PATH) ?: '/';
    }
    
    public function withHeader(string $key, string $value): self
    {
        $clone = clone $this;
        $clone->headers[strtolower($key)] = $value;
        return $clone;
    }
}

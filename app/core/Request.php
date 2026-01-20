<?php

namespace core;

class Request
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function capture(): self
    {
        $data = array_merge($_GET, $_POST);
        return new self($data);
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }
}
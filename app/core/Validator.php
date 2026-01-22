<?php

namespace core;

class Validator
{
    private array $errors = [];

    public function required(string $field, $value): self
    {
        if (empty($value)) {
            $this->errors[$field] = ucfirst($field) . ' is required';
        }
        return $this;
    }

    public function email(string $field, $value): self
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email format';
        }
        return $this;
    }

    public function min(string $field, $value, int $min): self
    {
        if (!empty($value) && strlen($value) < $min) {
            $this->errors[$field] = ucfirst($field) . " must be at least {$min} characters";
        }
        return $this;
    }

    public function max(string $field, $value, int $max): self
    {
        if (!empty($value) && strlen($value) > $max) {
            $this->errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
        }
        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Tiny rule-based validator. Rules: required|email|min:n|max:n|numeric|slug|url|confirmed.
 */
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $ruleset) {
            $value = trim((string) ($this->data[$field] ?? ''));
            foreach (explode('|', $ruleset) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->apply($field, $value, $name, $param);
            }
        }
        return empty($this->errors);
    }

    private function apply(string $field, string $value, string $rule, ?string $param): void
    {
        $label = ucwords(str_replace('_', ' ', $field));
        switch ($rule) {
            case 'required':
                if ($value === '') {
                    $this->add($field, "$label is required.");
                }
                break;
            case 'email':
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->add($field, "$label must be a valid email.");
                }
                break;
            case 'url':
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->add($field, "$label must be a valid URL.");
                }
                break;
            case 'numeric':
                if ($value !== '' && !is_numeric($value)) {
                    $this->add($field, "$label must be numeric.");
                }
                break;
            case 'min':
                if (mb_strlen($value) < (int) $param) {
                    $this->add($field, "$label must be at least {$param} characters.");
                }
                break;
            case 'max':
                if (mb_strlen($value) > (int) $param) {
                    $this->add($field, "$label may not exceed {$param} characters.");
                }
                break;
            case 'slug':
                if ($value !== '' && !preg_match('~^[a-z0-9-]+$~', $value)) {
                    $this->add($field, "$label must contain only lowercase letters, numbers, and hyphens.");
                }
                break;
            case 'confirmed':
                if ($value !== trim((string) ($this->data[$field . '_confirmation'] ?? ''))) {
                    $this->add($field, "$label confirmation does not match.");
                }
                break;
        }
    }

    private function add(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors ? reset($this->errors) : null;
    }
}

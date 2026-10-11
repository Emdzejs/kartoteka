<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use InvalidArgumentException;

final readonly class Email
{
    public const int MAX_LENGTH = 254;

    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '' || mb_strlen($value) > self::MAX_LENGTH || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Email is invalid');
        }

        return new self($value);
    }
}

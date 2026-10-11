<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    public const int MAX_LENGTH = 16;

    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (strlen($value) > self::MAX_LENGTH || preg_match('/\A\+[1-9][0-9]+\z/', $value) !== 1) {
            throw new InvalidArgumentException('Phone number is invalid - must start with +');
        }

        return new self($value);
    }
}

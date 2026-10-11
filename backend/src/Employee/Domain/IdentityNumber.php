<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use InvalidArgumentException;

final readonly class IdentityNumber
{
    public const int LENGTH = 11;

    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = self::normalize(trim($value));

        if (strlen($value) !== self::LENGTH || !ctype_digit($value)) {
            throw new InvalidArgumentException('Identity number must have 11 digits');
        }

        return new self($value);
    }

    private static function normalize(string $value): string
    {
        // Remove hyphen, if present
        if (preg_match('/\A[0-9]{6}-[0-9]{5}\z/', $value) === 1) {
            return str_replace('-', '', $value);
        }

        return $value;
    }
}

<?php

declare(strict_types=1);

namespace App\Employee\Domain\PreviousEmployment;

use InvalidArgumentException;

final readonly class JobRole
{
    public const int MAX_LENGTH = 100;

    private function __construct(
        public string $value,
    ) {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '' || mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf("Job role can't be empty or longer than %d characters", self::MAX_LENGTH)
            );
        }

        return new self($value);
    }
}

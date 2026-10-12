<?php

declare(strict_types=1);


namespace App\Employee\Application;

use DateTimeImmutable;
use InvalidArgumentException;

final class DateParser
{
    public static function parse(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        // Preventing cases when date is formatted but as a different date
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Date must be in Y-m-d format');
        }

        return $date;
    }
}

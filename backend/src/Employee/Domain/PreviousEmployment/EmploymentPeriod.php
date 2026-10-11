<?php

declare(strict_types=1);

namespace App\Employee\Domain\PreviousEmployment;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class EmploymentPeriod
{
    private function __construct(
        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private DateTimeImmutable $startDate,

        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private DateTimeImmutable $endDate,
    ) {
    }

    public static function create(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
    ): self {
        $start = $startDate->format('Y-m-d');
        $end = $endDate->format('Y-m-d');
        $today = new DateTimeImmutable()->format('Y-m-d');

        if ($end < $start) {
            throw new InvalidArgumentException('End date cannot be before the start date');
        }

        if ($end > $today) {
            throw new InvalidArgumentException('End date cannot be in the future');
        }

        return new self($startDate, $endDate);
    }

    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
}

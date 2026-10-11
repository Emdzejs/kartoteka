<?php

namespace App\Tests\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\PreviousEmployment\EmploymentPeriod;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmploymentPeriodTest extends TestCase
{
    public function testCreatesValidEmploymentPeriod(): void
    {
        $startDate = '2024-01-01';
        $endDate = '2025-10-10';

        $period = EmploymentPeriod::create(
            startDate: new DateTimeImmutable($startDate),
            endDate: new DateTimeImmutable($endDate),
        );

        self::assertSame($startDate, $period->startDate()->format('Y-m-d'));
        self::assertSame($endDate, $period->endDate()->format('Y-m-d'));
    }

    public function testCreateThrowsExceptionWhenEndDateBeforeStart(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EmploymentPeriod::create(
            startDate: new DateTimeImmutable( '2025-10-10'),
            endDate: new DateTimeImmutable('2024-01-01'),
        );
    }

    public function testCreateThrowsExceptionWhenEndDateInFuture(): void
    {
        $this->expectException(InvalidArgumentException::class);

        EmploymentPeriod::create(
            startDate: new DateTimeImmutable( '2025-10-10'),
            endDate: new DateTimeImmutable('tomorrow'),
        );
    }
}

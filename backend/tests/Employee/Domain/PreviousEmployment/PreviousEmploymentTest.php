<?php

namespace App\Tests\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\Address;
use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\Gender;
use App\Employee\Domain\IdentityNumber;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
use App\Employee\Domain\PreviousEmployment\BusinessName;
use App\Employee\Domain\PreviousEmployment\BusinessRegistrationNumber;
use App\Employee\Domain\PreviousEmployment\EmploymentPeriod;
use App\Employee\Domain\PreviousEmployment\JobRole;
use App\Employee\Domain\PreviousEmployment\PreviousEmployment;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class PreviousEmploymentTest extends TestCase
{
    public function testCreatesPreviousEmployment(): void
    {
        $registrationNumber = 'LV55555222222';
        $businessName = 'SIA Moov';
        $role = 'Programmēšanas inženieris';

        $startDate = '2024-01-01';
        $endDate = '2025-10-10';
        $period = EmploymentPeriod::create(
            startDate: new DateTimeImmutable($startDate),
            endDate: new DateTimeImmutable($endDate),
        );

        $employee = Employee::create(
            identityNumber: IdentityNumber::fromString('11111122222'),
            firstName: FirstName::fromString('Emīls'),
            lastName: LastName::fromString('Liepa'),
            gender: Gender::MALE,
            email: Email::fromString('emils@example.test'),
            phoneNumber: PhoneNumber::fromString('+37122222222'),
            address: Address::create(
                countryCode: 'lv',
                postalCode: 'lv-1111',
                city: 'Rīga',
                street: 'Piena iela',
                streetNumber: '5A',
            ),
        );

        $businessAddress = Address::create(
            countryCode: 'LV',
            postalCode: 'LV-1111',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );

        $employment = PreviousEmployment::create(
            employee: $employee,
            businessRegistrationNumber: BusinessRegistrationNumber::fromString($registrationNumber),
            businessName: BusinessName::fromString($businessName),
            role: JobRole::fromString($role),
            period: $period,
            businessAddress: $businessAddress,
        );

        self::assertInstanceOf(Ulid::class, $employment->id());
        self::assertSame($businessName, $employment->businessName());
        self::assertSame($registrationNumber, $employment->businessRegistrationNumber());
        self::assertSame($role, $employment->role());

        self::assertSame($period, $employment->period());
        self::assertSame($startDate, $employment->period()->startDate()->format('Y-m-d'));
        self::assertSame($endDate, $employment->period()->endDate()->format('Y-m-d'));

        self::assertSame($businessAddress, $employment->businessAddress());
        self::assertInstanceOf(DateTimeImmutable::class, $employment->createdAt());
        self::assertSame($employment->createdAt(), $employment->updatedAt());
    }
}

<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\Address;
use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\Gender;
use App\Employee\Domain\IdentityNumber;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class EmployeeTest extends TestCase
{
    public function testCreateCreatesValidEmployee(): void
    {
        $identityNumber = '11111122222';
        $firstName = 'Emīls';
        $lastName = 'Liepa';
        $gender = Gender::MALE;
        $email = 'emils@example.test';
        $phoneNumber = '+37122222222';

        $addressCity = 'Rīga';
        $addressStreet = 'Piena iela';
        $addressStreetNumber = '5A';

        $employee = Employee::create(
            identityNumber: IdentityNumber::fromString($identityNumber),
            firstName: FirstName::fromString($firstName),
            lastName: LastName::fromString($lastName),
            gender: $gender,
            email: Email::fromString($email),
            phoneNumber: PhoneNumber::fromString($phoneNumber),
            address: Address::create(
                countryCode: 'lv',
                postalCode: 'lv-1111',
                city: $addressCity,
                street: $addressStreet,
                streetNumber: $addressStreetNumber,
            ),
        );

        self::assertInstanceOf(Ulid::class, $employee->id());
        self::assertSame($identityNumber, $employee->identityNumber());
        self::assertSame($firstName, $employee->firstName());
        self::assertSame($lastName, $employee->lastName());
        self::assertSame($gender, $employee->gender());
        self::assertSame($email, $employee->email());
        self::assertSame($phoneNumber, $employee->phoneNumber());
        self::assertSame('LV', $employee->address()->countryCode());
        self::assertSame('LV-1111', $employee->address()->postalCode());
        self::assertSame($addressCity, $employee->address()->city());
        self::assertSame($addressStreet, $employee->address()->street());
        self::assertSame($addressStreetNumber, $employee->address()->streetNumber());
        self::assertInstanceOf(DateTimeImmutable::class, $employee->createdAt());
        self::assertSame($employee->createdAt(), $employee->updatedAt());
        self::assertSame('UTC', $employee->createdAt()->getTimezone()->getName());
    }
}

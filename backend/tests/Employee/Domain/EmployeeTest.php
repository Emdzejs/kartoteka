<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\Gender;
use App\Employee\Domain\IdentityNumber;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
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

        $employee = Employee::create(
            identityNumber: IdentityNumber::fromString($identityNumber),
            firstName: FirstName::fromString($firstName),
            lastName: LastName::fromString($lastName),
            gender: $gender,
            email: Email::fromString($email),
            phoneNumber: PhoneNumber::fromString($phoneNumber),
        );

        self::assertInstanceOf(Ulid::class, $employee->id());
        self::assertSame($identityNumber, $employee->identityNumber());
        self::assertSame($firstName, $employee->firstName());
        self::assertSame($lastName, $employee->lastName());
        self::assertSame($gender, $employee->gender());
        self::assertSame($email, $employee->email());
        self::assertSame($phoneNumber, $employee->phoneNumber());
    }
}

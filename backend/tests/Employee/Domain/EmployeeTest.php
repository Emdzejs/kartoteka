<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class EmployeeTest extends TestCase
{
    public function testCreateCreatesValidEmployee(): void
    {
        $firstName = 'Emīls';
        $lastName = 'Liepa';
        $email = 'emils@example.test';
        $phoneNumber = '+37122222222';

        $employee = Employee::create(
            firstName: FirstName::fromString($firstName),
            lastName: LastName::fromString($lastName),
            email: Email::fromString($email),
            phoneNumber: PhoneNumber::fromString($phoneNumber),
        );

        self::assertInstanceOf(Ulid::class, $employee->id());
        self::assertSame($firstName, $employee->firstName());
        self::assertSame($lastName, $employee->lastName());
        self::assertSame($email, $employee->email());
        self::assertSame($phoneNumber, $employee->phoneNumber());
    }
}

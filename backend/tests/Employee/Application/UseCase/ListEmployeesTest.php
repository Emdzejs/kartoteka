<?php

namespace App\Tests\Employee\Application\UseCase;

use App\Employee\Application\UseCase\ListEmployees;
use App\Employee\Domain\Address;
use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\Gender;
use App\Employee\Domain\IdentityNumber;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
use PHPUnit\Framework\TestCase;

class ListEmployeesTest extends TestCase
{
    public function testListReturnsEmployees(): void
    {
        $identityNumber = '11111122222';
        $firstName = 'Emīls';
        $lastName = 'Liepa';
        $email = 'emils@example.test';
        $phoneNumber = '+37122222222';

        $employee = Employee::create(
            identityNumber: IdentityNumber::fromString($identityNumber),
            firstName: FirstName::fromString($firstName),
            lastName: LastName::fromString($lastName),
            gender: Gender::MALE,
            email: Email::fromString($email),
            phoneNumber: PhoneNumber::fromString($phoneNumber),
            address: Address::create(
                countryCode: 'lv',
                postalCode: 'lv-1111',
                city: 'Rīga',
                street: 'Piena iela',
                streetNumber: '5A',
            ),
        );

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([$employee]);

        $useCase = new ListEmployees($repository);
        $result = $useCase->list();

        self::assertCount(1, $result);
        $summary = $result[0];
        self::assertIsArray($summary);

        self::assertSame((string) $employee->id(), $summary['id']);
        self::assertSame($identityNumber, $summary['identityNumber']);
        self::assertSame("$firstName $lastName", $summary['fullName']);
        self::assertSame($phoneNumber, $summary['phoneNumber']);
        self::assertSame($email, $summary['email']);
    }

    public function testListReturnsEmptyArrayWhenNoEmployeesExist(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn([]);

        $useCase = new ListEmployees($repository);

        self::assertSame([], $useCase->list());
    }
}

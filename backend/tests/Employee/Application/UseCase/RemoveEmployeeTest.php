<?php

namespace App\Tests\Employee\Application\UseCase;

use App\Employee\Application\UseCase\EmployeeNotFoundException;
use App\Employee\Application\UseCase\RemoveEmployee;
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
use Symfony\Component\Uid\Ulid;

class RemoveEmployeeTest extends TestCase
{
    public function testRemoveRemovesEmployee(): void
    {
        $employee = self::createEmployee();

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findById')
            ->with($employee->id())
            ->willReturn($employee);

        $repository
            ->expects(self::once())
            ->method('delete')
            ->with($employee);

        new RemoveEmployee($repository)->remove($employee->id());
    }

    public function testThrowsWhenEmployeeDoesNotExist(): void
    {
        $id = new Ulid();

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $repository
            ->expects(self::never())
            ->method('delete');

        $this->expectException(EmployeeNotFoundException::class);

        new RemoveEmployee($repository)->remove($id);
    }

    private static function createEmployee(): Employee
    {
        return Employee::create(
            identityNumber: IdentityNumber::fromString('111111-22222'),
            firstName: FirstName::fromString('Emīls'),
            lastName: LastName::fromString('Liepa'),
            gender: Gender::MALE,
            email: Email::fromString('emils@example.test'),
            phoneNumber: PhoneNumber::fromString('+37122222222'),
            address: Address::create(
                countryCode: 'LV',
                postalCode: 'LV1010',
                city: 'Riga',
                street: 'Brivibas',
                streetNumber: '10',
            ),
        );
    }
}

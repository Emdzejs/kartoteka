<?php

namespace App\Tests\Employee\Application\UseCase;

use App\Employee\Application\UseCase\EmployeeNotFoundException;
use App\Employee\Application\UseCase\EditEmployee;
use App\Employee\Application\UseCase\EditEmployeeInput;
use App\Employee\Application\UseCase\EditPreviousEmploymentInput;
use App\Employee\Domain\Address;
use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
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
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

final class EditEmployeeTest extends TestCase
{
    public function testUpdatesEmployeeDetails(): void
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
            ->method('save')
            ->with($employee);

        $useCase = new EditEmployee($repository);

        $useCase->execute($employee->id(), self::validInput());

        self::assertSame('Anna', $employee->firstName());
        self::assertSame('Panna', $employee->lastName());
        self::assertSame(Gender::FEMALE, $employee->gender());
        self::assertSame('anna@example.test', $employee->email());
        self::assertSame('+37144444444', $employee->phoneNumber());
        self::assertSame('LV-1111', $employee->address()->postalCode());
        self::assertSame('Peļķes iela', $employee->address()->street());
    }

    public function testSynchronizesPreviousEmployments(): void
    {
        $employee = self::createEmployee();

        $existingEmployment = self::addEmployment(
            $employee,
            'LV11111555555',
            'SIA Vero Solutions',
            'Jaunākais Programmētājs',
        );

        $removedEmployment = self::addEmployment(
            $employee,
            'LV0000000001',
            'SIA Remove Me',
            'Programmēšanas praktikants',
        );

        $existingId = (string) $existingEmployment->id();
        $originalCreatedAt = $existingEmployment->createdAt();

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findById')
            ->willReturn($employee);

        $repository
            ->expects(self::once())
            ->method('save')
            ->with($employee);

        $input = self::validInput([
            // Updating existing
            self::employmentInput(
                id: $existingId,
                role: 'Vecākais Programmēšanas Inženieris',
            ),

            // New
            self::employmentInput(
                businessRegistrationNumber: 'LV2821811911',
                businessName: 'SIA New',
                role: 'Projekta vadītājs',
                startDate: '2026-01-01',
                endDate: '2026-10-10',
            ),
        ]);

        new EditEmployee($repository)->execute($employee->id(), $input);

        $employments = [];
        foreach ($employee->previousEmployments() as $employment) {
            $employments[$employment->businessRegistrationNumber()] = $employment;
        }

        self::assertCount(2, $employments);

        // Existing employment was updated, not recreated
        $updatedEmployment = $employments['LV11111555555'];
        self::assertSame($existingEmployment, $updatedEmployment);
        self::assertSame($existingId, (string) $updatedEmployment->id());
        self::assertEquals($originalCreatedAt, $updatedEmployment->createdAt());
        self::assertSame('Vecākais Programmēšanas Inženieris', $updatedEmployment->role());

        // New employment was created
        $newEmployment = $employments['LV2821811911'];
        self::assertSame('SIA New', $newEmployment->businessName());
        self::assertSame('Projekta vadītājs', $newEmployment->role());
        self::assertNotSame($existingId, (string) $newEmployment->id());
        self::assertSame(
            '2026-01-01',
            $newEmployment->period()->startDate()->format('Y-m-d'),
        );
        self::assertSame(
            '2026-10-10',
            $newEmployment->period()->endDate()->format('Y-m-d'),
        );

        // Missing employment was removed
        self::assertArrayNotHasKey(
            $removedEmployment->businessRegistrationNumber(),
            $employments,
        );
    }

    public function testRejectsUnknownPreviousEmploymentId(): void
    {
        $employee = self::createEmployee();

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->method('findById')
            ->willReturn($employee);

        $repository
            ->expects(self::never())
            ->method('save');

        $input = self::validInput([
            self::employmentInput(id: (string) new Ulid()),
        ]);

        $this->expectException(InvalidArgumentException::class);

        new EditEmployee($repository)->execute($employee->id(), $input);
    }

    public function testRejectsDuplicatePreviousEmploymentIds(): void
    {
        $employee = self::createEmployee();

        $employment = self::addEmployment(
            $employee,
            '40003000000',
            'SIA IeO',
            'Testētājs',
        );

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->method('findById')
            ->willReturn($employee);

        $repository
            ->expects(self::never())
            ->method('save');

        $id = (string) $employment->id();

        $input = self::validInput([
            self::employmentInput(id: $id),
            self::employmentInput(id: $id),
        ]);

        $this->expectException(InvalidArgumentException::class);

        new EditEmployee($repository)->execute($employee->id(), $input);
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
            ->method('save');

        $this->expectException(EmployeeNotFoundException::class);

        new EditEmployee($repository)->execute($id, self::validInput());
    }

    private static function createEmployee(): Employee
    {
        return Employee::create(
            identityNumber: IdentityNumber::fromString('320000-00000'),
            firstName: FirstName::fromString('Jānis'),
            lastName: LastName::fromString('Bērziņš'),
            gender: Gender::MALE,
            email: Email::fromString('janis@example.com'),
            phoneNumber: PhoneNumber::fromString('+37120000000'),
            address: Address::create(
                countryCode: 'LV',
                postalCode: 'LV1010',
                city: 'Riga',
                street: 'Brivibas',
                streetNumber: '10',
            ),
        );
    }

    private static function addEmployment(
        Employee $employee,
        string $registrationNumber,
        string $businessName,
        string $role,
    ): PreviousEmployment {
        return $employee->addPreviousEmployment(
            businessRegistrationNumber: BusinessRegistrationNumber::fromString($registrationNumber),
            businessName: BusinessName::fromString($businessName),
            role: JobRole::fromString($role),
            period: EmploymentPeriod::create(
                new DateTimeImmutable('2020-01-01'),
                new DateTimeImmutable('2023-12-31'),
            ),
            businessAddress: Address::create(
                countryCode: 'LV',
                postalCode: 'LV1050',
                city: 'Riga',
                street: 'Dzirnavu',
                streetNumber: '20',
            ),
        );
    }

    /**
     * @param EditPreviousEmploymentInput[] $previousEmployments
     */
    private static function validInput(
        array $previousEmployments = [],
    ): EditEmployeeInput {
        return new EditEmployeeInput(
            identityNumber: '320000-00000',
            firstName: 'Anna',
            lastName: 'Panna',
            gender: 'female',
            email: 'anna@example.test',
            phoneNumber: '+37144444444',
            countryCode: 'LV',
            postalCode: 'LV1111',
            city: 'Rīga',
            street: 'Peļķes iela',
            streetNumber: '12',
            previousEmployments: $previousEmployments,
        );
    }

    private static function employmentInput(
        ?string $id = null,
        string $businessRegistrationNumber = 'LV11111555555',
        string $businessName = 'SIA Moov',
        string $role = 'Programmēšanas inženieris',
        string $startDate = '2024-01-01',
        string $endDate = '2025-10-10',
    ): EditPreviousEmploymentInput {
        return new EditPreviousEmploymentInput(
            businessRegistrationNumber: $businessRegistrationNumber,
            businessName: $businessName,
            role: $role,
            startDate: $startDate,
            endDate: $endDate,
            businessCountryCode: 'LV',
            businessPostalCode: 'LV1000',
            businessCity: 'Rīga',
            businessStreet: 'Dzirnu iela',
            businessStreetNumber: '999',
            id: $id,
        );
    }
}

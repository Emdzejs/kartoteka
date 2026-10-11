<?php

namespace App\Tests\Employee\Application\UseCase;

use App\Employee\Application\UseCase\CreateEmployee;
use App\Employee\Application\UseCase\CreateEmployeeInput;
use App\Employee\Application\UseCase\DuplicateIdentityNumberException;
use App\Employee\Application\UseCase\PreviousEmploymentInput;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\IdentityNumber;
use PHPUnit\Framework\TestCase;

class CreateEmployeeTest extends TestCase
{
    public function testCreateCreatesEmployee(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('existsByIdentityNumber')
            ->with(self::callback(
                static fn (IdentityNumber $number): bool =>
                    $number->value === '11111122222',
            ))
            ->willReturn(false);

        $savedEmployee = null;

        $repository
            ->expects(self::once())
            ->method('save')
            ->willReturnCallback(
                static function (Employee $employee) use (&$savedEmployee): void {
                    $savedEmployee = $employee;
                },
            );

        $useCase = new CreateEmployee($repository);

        $id = $useCase->create(self::validInput());

        self::assertInstanceOf(Employee::class, $savedEmployee);
        self::assertEquals($savedEmployee->id(), $id);
        self::assertSame('11111122222', $savedEmployee->identityNumber());
        self::assertSame('Emīls', $savedEmployee->firstName());
        self::assertSame('Liepa', $savedEmployee->lastName());

        $previousEmployments = $savedEmployee->previousEmployments();
        self::assertCount(2, $previousEmployments);
        self::assertSame('LV55555222222', $previousEmployments[0]->businessRegistrationNumber());
        self::assertSame('SIA Moov', $previousEmployments[0]->businessName());
        self::assertSame('Programmēšanas inženieris', $previousEmployments[0]->role());
        self::assertSame(
            '2024-01-01',
            $previousEmployments[0]->period()->startDate()->format('Y-m-d')
        );


        self::assertSame('LV88888854554', $previousEmployments[1]->businessRegistrationNumber());
        self::assertSame('SIA Vero Solutions', $previousEmployments[1]->businessName());
        self::assertSame('Jaunākais programmētājs', $previousEmployments[1]->role());
        self::assertSame(
            '2025-08-08',
            $previousEmployments[1]->period()->endDate()->format('Y-m-d')
        );
    }

    public function testCreateCreatesEmployeeWithoutPreviousEmployments(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('existsByIdentityNumber')
            ->with(self::callback(
                static fn (IdentityNumber $number): bool =>
                    $number->value === '11111122222',
            ))
            ->willReturn(false);

        $savedEmployee = null;

        $repository
            ->expects(self::once())
            ->method('save')
            ->willReturnCallback(
                static function (Employee $employee) use (&$savedEmployee): void {
                    $savedEmployee = $employee;
                },
            );

        $useCase = new CreateEmployee($repository);

        $id = $useCase->create(self::validInput(false));

        self::assertInstanceOf(Employee::class, $savedEmployee);
        self::assertEquals($savedEmployee->id(), $id);
        self::assertSame('11111122222', $savedEmployee->identityNumber());
        self::assertSame('Emīls', $savedEmployee->firstName());
        self::assertSame('Liepa', $savedEmployee->lastName());

        $previousEmployments = $savedEmployee->previousEmployments();
        self::assertCount(0, $previousEmployments);
    }

    public function testCreatesThrowsExceptionWhenDuplicateIdentityNumber(): void
    {
        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('existsByIdentityNumber')
            ->willReturn(true);

        $repository
            ->expects(self::never())
            ->method('save');

        $useCase = new CreateEmployee($repository);

        $this->expectException(DuplicateIdentityNumberException::class);

        $useCase->create(self::validInput());
    }

    private static function validInput(bool $includePreviousEmployment = true): CreateEmployeeInput
    {
        $previousEmployments = $includePreviousEmployment ? [
            new PreviousEmploymentInput(
                businessRegistrationNumber: 'LV55555222222',
                businessName: 'SIA Moov',
                role: 'Programmēšanas inženieris',
                startDate: '2024-01-01',
                endDate: '2025-10-10',
                businessCountryCode: 'LV',
                businessPostalCode: 'LV-1122',
                businessCity: 'Valmiera',
                businessStreet: 'Lielā iela',
                businessStreetNumber: '88',
            ),
            new PreviousEmploymentInput(
                businessRegistrationNumber: 'LV88888854554',
                businessName: 'SIA Vero Solutions',
                role: 'Jaunākais programmētājs',
                startDate: '2023-02-02',
                endDate: '2025-08-08',
                businessCountryCode: 'LV',
                businessPostalCode: 'LV-2312',
                businessCity: 'Rīga',
                businessStreet: 'Zaļā iela',
                businessStreetNumber: '1',
            ),
        ] : [];


        return new CreateEmployeeInput(
            identityNumber: '111111-22222',
            firstName: 'Emīls',
            lastName: 'Liepa',
            gender: 'male',
            email: 'emils@example.test',
            phoneNumber: '+37122222222',
            countryCode: 'lv',
            postalCode: 'lv-1111',
            city: 'Riga',
            street: 'Piena iela',
            streetNumber: '5A',
            previousEmployments: $previousEmployments,
        );
    }
}

<?php

namespace App\Tests\Employee\Adapter\Secondary\Persistence;

use App\Employee\Adapter\Secondary\Persistence\DoctrineEmployeeRepository;
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
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Ulid;

class DoctrineEmployeeRepositoryTest extends KernelTestCase
{
    public function testSaveAndFindByIdSavesAndFindsEmployeeWithPreviousEmployment(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = new DoctrineEmployeeRepository($entityManager);

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

        $employment = $employee->addPreviousEmployment(
            businessRegistrationNumber: BusinessRegistrationNumber::fromString('LV55555222222'),
            businessName: BusinessName::fromString('SIA Moov'),
            role: JobRole::fromString('Programmēšanas inženieris'),
            period: EmploymentPeriod::create(
                startDate: new DateTimeImmutable('2024-01-01'),
                endDate: new DateTimeImmutable('2025-10-10'),
            ),
            businessAddress: Address::create(
                countryCode: 'LV',
                postalCode: 'LV-1111',
                city: 'Rīga',
                street: 'Piena iela',
                streetNumber: '5A',
            ),
        );

        $repository->save($employee);

        $entityManager->clear();

        $savedEmployee = $repository->findById($employee->id());

        self::assertNotNull($savedEmployee);
        self::assertNotSame($employee, $savedEmployee);

        self::assertEquals($employee->id(), $savedEmployee->id());
        self::assertSame($employee->identityNumber(), $savedEmployee->identityNumber());
        self::assertSame($employee->firstName(), $savedEmployee->firstName());
        self::assertSame($employee->lastName(), $savedEmployee->lastName());
        self::assertSame($employee->gender(), $savedEmployee->gender());
        self::assertSame($employee->email(), $savedEmployee->email());
        self::assertSame($employee->phoneNumber(), $savedEmployee->phoneNumber());

        self::assertEquals($employee->address(), $savedEmployee->address());

        self::assertSame(
            $employee->createdAt()->format('Y-m-d H:i:s'),
            $savedEmployee->createdAt()->format('Y-m-d H:i:s'),
        );

        self::assertSame(
            $employee->updatedAt()->format('Y-m-d H:i:s'),
            $savedEmployee->updatedAt()->format('Y-m-d H:i:s'),
        );

        $savedEmployments = $savedEmployee->previousEmployments();
        self::assertCount(1, $savedEmployments);

        $savedEmployment = $savedEmployments[0];

        self::assertEquals($employment->id(), $savedEmployment->id());
        self::assertSame($employment->businessName(), $savedEmployment->businessName());
        self::assertSame(
            $employment->businessRegistrationNumber(),
            $savedEmployment->businessRegistrationNumber(),
        );
        self::assertSame($employment->role(), $savedEmployment->role());
        self::assertEquals($employment->period(), $savedEmployment->period());
        self::assertEquals($employment->businessAddress(), $savedEmployment->businessAddress());
        self::assertSame(
            $employment->createdAt()->format('Y-m-d H:i:s'),
            $savedEmployment->createdAt()->format('Y-m-d H:i:s'),
        );
        self::assertSame(
            $employment->updatedAt()->format('Y-m-d H:i:s'),
            $savedEmployment->updatedAt()->format('Y-m-d H:i:s'),
        );
    }

    public function testFindByIdReturnsNullWhenEmployeeDoesNotExist(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = new DoctrineEmployeeRepository($entityManager);

        $employee = $repository->findById(new Ulid());

        self::assertNull($employee);
    }

    public function testExistsByIdentityNumberReturnsExpected(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $repository = new DoctrineEmployeeRepository($entityManager);

        $identityNumber = IdentityNumber::fromString('111111-22222');

        self::assertFalse($repository->existsByIdentityNumber($identityNumber));

        $employee = Employee::create(
            identityNumber: $identityNumber,
            firstName: FirstName::fromString('Emīls'),
            lastName: LastName::fromString('Liepa'),
            gender: Gender::MALE,
            email: Email::fromString('emils@example.test'),
            phoneNumber: PhoneNumber::fromString('+37122222222'),
            address: Address::create(
                countryCode: 'LV',
                postalCode: 'LV1111',
                city: 'Rīga',
                street: 'Piena iela',
                streetNumber: '5A',
            ),
        );

        $repository->save($employee);
        $entityManager->clear();

        self::assertTrue($repository->existsByIdentityNumber($identityNumber));

        self::assertFalse(
            $repository->existsByIdentityNumber(
                IdentityNumber::fromString('000000-00000'),
            ),
        );
    }
}

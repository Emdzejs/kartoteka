<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

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
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Uid\Ulid;

final readonly class CreateEmployee
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function create(CreateEmployeeInput $input): Ulid
    {
        $identityNumber = IdentityNumber::fromString($input->identityNumber);

        if ($this->employeeRepository->existsByIdentityNumber($identityNumber)) {
            throw new DuplicateIdentityNumberException('An employee with this identity number already exists');
        }

        $employee = Employee::create(
            identityNumber: $identityNumber,
            firstName: FirstName::fromString($input->firstName),
            lastName: LastName::fromString($input->lastName),
            gender: Gender::from($input->gender),
            email: Email::fromString($input->email),
            phoneNumber: PhoneNumber::fromString($input->phoneNumber),
            address: Address::create(
                countryCode: $input->countryCode,
                postalCode: $input->postalCode,
                city: $input->city,
                street: $input->street,
                streetNumber: $input->streetNumber,
            ),
        );

        foreach ($input->previousEmployments as $previousEmployment) {
            $employee->addPreviousEmployment(
                businessRegistrationNumber: BusinessRegistrationNumber::fromString($previousEmployment->businessRegistrationNumber),
                businessName: BusinessName::fromString($previousEmployment->businessName),
                role: JobRole::fromString($previousEmployment->role),
                period: EmploymentPeriod::create(
                    self::parseDate($previousEmployment->startDate),
                    self::parseDate($previousEmployment->endDate),
                ),
                businessAddress: Address::create(
                    countryCode: $previousEmployment->businessCountryCode,
                    postalCode: $previousEmployment->businessPostalCode,
                    city: $previousEmployment->businessCity,
                    street: $previousEmployment->businessStreet,
                    streetNumber: $previousEmployment->businessStreetNumber,
                ),
            );
        }

        $this->employeeRepository->save($employee);

        return $employee->id();
    }

    private static function parseDate(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date) {
            throw new InvalidArgumentException('Date must be in Y-m-d format');
        }

        return $date;
    }
}

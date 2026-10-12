<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Employee\Application\Exception\EmployeeNotFoundException;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\PreviousEmployment\PreviousEmployment;
use Symfony\Component\Uid\Ulid;

final readonly class ShowEmployee
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function show(Ulid $id): array
    {
        $employee = $this->employeeRepository->findById($id) ?? throw new EmployeeNotFoundException();

        return [
            'id' => (string) $employee->id(),
            'identityNumber' => $employee->identityNumber(),
            'fullName' => $employee->fullName(),
            'firstName' => $employee->firstName(),
            'lastName' => $employee->lastName(),
            'gender' => $employee->gender()->value,
            'email' => $employee->email(),
            'phoneNumber' => $employee->phoneNumber(),
            'countryCode' => $employee->address()->countryCode(),
            'postalCode' => $employee->address()->postalCode(),
            'city' => $employee->address()->city(),
            'street' => $employee->address()->street(),
            'streetNumber' => $employee->address()->streetNumber(),
            'createdAt' => $employee->createdAt()->format(DATE_ATOM),
            'updatedAt' => $employee->updatedAt()->format(DATE_ATOM),
            'previousEmployments' => array_map(
                static fn (PreviousEmployment $employment): array => [
                    'id' => (string) $employment->id(),
                    'businessRegistrationNumber' => $employment->businessRegistrationNumber(),
                    'businessName' => $employment->businessName(),
                    'role' => $employment->role(),
                    'startDate' => $employment->period()->startDate()->format('Y-m-d'),
                    'endDate' => $employment->period()->endDate()->format('Y-m-d'),
                    'businessCountryCode' => $employment->businessAddress()->countryCode(),
                    'businessPostalCode' => $employment->businessAddress()->postalCode(),
                    'businessCity' => $employment->businessAddress()->city(),
                    'businessStreet' => $employment->businessAddress()->street(),
                    'businessStreetNumber' => $employment->businessAddress()->streetNumber(),
                ],
                $employee->previousEmployments(),
            ),
        ];
    }
}

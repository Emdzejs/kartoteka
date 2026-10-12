<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;

final readonly class ListEmployees
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function list(): array
    {
        return array_map(
            static fn (Employee $employee): array => [
                'id' => (string) $employee->id(),
                'identityNumber' => $employee->identityNumber(),
                'fullName' => $employee->fullName(),
                'email' => $employee->email(),
                'phoneNumber' => $employee->phoneNumber(),
                'createdAt' => $employee->createdAt()->format(DATE_ATOM),
                'updatedAt' => $employee->updatedAt()->format(DATE_ATOM),
            ],
            $this->employeeRepository->findAll(),
        );
    }
}

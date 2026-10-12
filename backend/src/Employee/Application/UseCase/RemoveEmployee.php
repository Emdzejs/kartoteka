<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Employee\Application\Exception\EmployeeNotFoundException;
use App\Employee\Domain\EmployeeRepository;
use Symfony\Component\Uid\Ulid;

final readonly class RemoveEmployee
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function remove(Ulid $id): void
    {
        $employee = $this->employeeRepository->findById($id) ?? throw new EmployeeNotFoundException();

        $this->employeeRepository->delete($employee);
    }
}

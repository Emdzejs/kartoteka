<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use Symfony\Component\Uid\Ulid;

final readonly class ShowEmployee
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function get(Ulid $id): Employee
    {
        return $this->employeeRepository->findById($id) ?? throw new EmployeeNotFoundException();
    }
}

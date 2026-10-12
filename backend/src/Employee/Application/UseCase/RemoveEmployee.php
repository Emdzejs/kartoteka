<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Employee\Application\DateParser;
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
use InvalidArgumentException;
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

<?php

namespace App\Employee\Domain;

use Symfony\Component\Uid\Ulid;

interface EmployeeRepository
{
    public function save(Employee $employee): void;
    public function findById(Ulid $id): ?Employee;
    public function existsByIdentityNumber(IdentityNumber $identityNumber): bool;
}

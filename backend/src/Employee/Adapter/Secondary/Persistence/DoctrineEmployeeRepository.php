<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Secondary\Persistence;

use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Ulid;

final readonly class DoctrineEmployeeRepository implements EmployeeRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Employee $employee): void
    {
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }

    public function findById(Ulid $id): ?Employee
    {
        return $this->entityManager->find(Employee::class, $id);
    }
}

<?php

declare(strict_types=1);


namespace App\Employee\Application\UseCase;

final readonly class EmployeeSummary
{
    public function __construct(
        public string $id,
        public string $identityNumber,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public string $createdAt,
    ) {
    }
}

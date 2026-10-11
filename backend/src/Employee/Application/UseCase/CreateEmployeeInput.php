<?php

declare(strict_types=1);


namespace App\Employee\Application\UseCase;

final class CreateEmployeeInput
{
    public function __construct(
        public string $identityNumber,
        public string $firstName,
        public string $lastName,
        public string $gender,
        public string $email,
        public string $phoneNumber,
        public string $countryCode,
        public string $postalCode,
        public string $city,
        public string $street,
        public string $streetNumber,
        /**
         * @var PreviousEmploymentInput[]
         */
        public array $previousEmployments = [],
    ) {
    }
}

<?php

declare(strict_types=1);


namespace App\Employee\Application\Input;

final class EditPreviousEmploymentInput
{
    public function __construct(
        public string $businessRegistrationNumber,
        public string $businessName,
        public string $role,
        public string $startDate,
        public string $endDate,
        public string $businessCountryCode,
        public string $businessPostalCode,
        public string $businessCity,
        public string $businessStreet,
        public string $businessStreetNumber,
        public ?string $id = null,
    ) {
    }
}

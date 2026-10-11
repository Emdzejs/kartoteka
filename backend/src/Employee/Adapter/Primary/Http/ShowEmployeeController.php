<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\UseCase\EmployeeNotFoundException;
use App\Employee\Application\UseCase\ShowEmployee;
use App\Employee\Domain\PreviousEmployment\PreviousEmployment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

final readonly class ShowEmployeeController
{
    public function __construct(
        private ShowEmployee $getEmployee,
    ) {
    }

    #[Route('/api/employees/{id}', name: 'api_employees_get', methods: ['GET'], format: 'json')]
    public function __invoke(string $id): JsonResponse
    {
        // RFC_4122 support to allow searching by the format the ID is stored in DB
        if (!Ulid::isValid($id, Ulid::FORMAT_BASE_32 | Ulid::FORMAT_RFC_4122)) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $employee = $this->getEmployee->get(Ulid::fromString($id));
        } catch (EmployeeNotFoundException) {
            return new JsonResponse(
                ['error' => 'Employee not found.'],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new JsonResponse([
            'id' => (string) $employee->id(),
            'identityNumber' => $employee->identityNumber(),
            'fullName' => $employee->fullName(),
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
        ]);
    }
}

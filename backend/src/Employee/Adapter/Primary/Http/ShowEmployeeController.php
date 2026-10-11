<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\UseCase\EmployeeNotFoundException;
use App\Employee\Application\UseCase\ShowEmployee;
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
            return new JsonResponse($this->getEmployee->show(Ulid::fromString($id)));
        } catch (EmployeeNotFoundException) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
        }
    }
}

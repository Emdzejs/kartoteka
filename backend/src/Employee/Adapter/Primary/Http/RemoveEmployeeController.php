<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\Exception\EmployeeNotFoundException;
use App\Employee\Application\UseCase\RemoveEmployee;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

final readonly class RemoveEmployeeController
{
    public function __construct(
        private RemoveEmployee $removeEmployee,
    ) {
    }

    #[Route('/api/employees/{id}', methods: ['DELETE'], format: 'json')]
    public function __invoke(string $id): Response
    {
        if (!Ulid::isValid($id, Ulid::FORMAT_BASE_32 | Ulid::FORMAT_RFC_4122)) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $this->removeEmployee->remove(Ulid::fromString($id));
        } catch (EmployeeNotFoundException) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}

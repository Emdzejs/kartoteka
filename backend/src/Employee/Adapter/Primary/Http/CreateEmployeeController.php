<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\Exception\DuplicateIdentityNumberException;
use App\Employee\Application\Input\CreateEmployeeInput;
use App\Employee\Application\UseCase\CreateEmployee;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final readonly class CreateEmployeeController
{
    public function __construct(
        private CreateEmployee $createEmployee,
    ) {
    }

    #[Route('/api/employees', methods: ['POST'], format: 'json')]
    public function __invoke(#[MapRequestPayload(acceptFormat: 'json')] CreateEmployeeInput $input): JsonResponse {
        try {
            $id = $this->createEmployee->create($input);
        } catch (DuplicateIdentityNumberException $exception) {
            return new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_CONFLICT,
            );
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        return new JsonResponse(
            ['id' => (string) $id],
            Response::HTTP_CREATED,
        );
    }
}

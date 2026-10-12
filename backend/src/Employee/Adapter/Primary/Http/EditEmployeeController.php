<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\UseCase\DuplicateIdentityNumberException;
use App\Employee\Application\UseCase\EditEmployee;
use App\Employee\Application\UseCase\EditEmployeeInput;
use App\Employee\Application\UseCase\EmployeeNotFoundException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Ulid;

final readonly class EditEmployeeController
{
    public function __construct(
        private EditEmployee $editEmployee,
    ) {
    }

    #[Route('/api/employees/{id}', name: 'api_employees_edit', methods: ['PUT'], format: 'json',)]
    public function __invoke(string $id, #[MapRequestPayload(acceptFormat: 'json')] EditEmployeeInput $input): Response
    {
        if (!Ulid::isValid($id, Ulid::FORMAT_BASE_32 | Ulid::FORMAT_RFC_4122)) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
        }

        try {
            $this->editEmployee->execute(Ulid::fromString($id), $input);
        } catch (EmployeeNotFoundException) {
            return new JsonResponse(
                ['error' => 'Employee not found'],
                Response::HTTP_NOT_FOUND,
            );
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

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}

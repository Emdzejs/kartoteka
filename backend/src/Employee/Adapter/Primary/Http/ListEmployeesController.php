<?php

declare(strict_types=1);


namespace App\Employee\Adapter\Primary\Http;

use App\Employee\Application\UseCase\ListEmployees;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final readonly class ListEmployeesController
{
    public function __construct(
        private ListEmployees $listEmployees,
    ) {
    }
    #[Route('/api/employees', methods: ['GET'], format: 'json')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->listEmployees->list());
    }
}

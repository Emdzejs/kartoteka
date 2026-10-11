<?php

namespace App\Tests\Employee\Application\UseCase;

use App\Employee\Application\UseCase\EmployeeNotFoundException;
use App\Employee\Application\UseCase\ShowEmployee;
use App\Employee\Domain\EmployeeRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class ShowEmployeeTest extends TestCase
{
    public function testShowThrowsWhenEmployeeDoesNotExist(): void
    {
        $id = new Ulid();

        $repository = $this->createMock(EmployeeRepository::class);

        $repository
            ->expects(self::once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $useCase = new ShowEmployee($repository);

        $this->expectException(EmployeeNotFoundException::class);

        $useCase->show($id);
    }
}

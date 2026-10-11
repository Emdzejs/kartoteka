<?php

declare(strict_types=1);

namespace App\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\Employee;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'previous_employments')]
class PreviousEmployment
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    private Ulid $id;

    #[ORM\ManyToOne(inversedBy: 'previousEmployments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Employee $employee;

    #[ORM\Column(length: BusinessRegistrationNumber::MAX_LENGTH)]
    private string $businessRegistrationNumber;

    private function __construct(
        Employee $employee,
        BusinessRegistrationNumber $businessRegistrationNumber,
    ) {
        $this->id = new Ulid();
        $this->employee = $employee;
        $this->businessRegistrationNumber = $businessRegistrationNumber->value;
    }

    public static function create(
        Employee $employee,
        BusinessRegistrationNumber $businessRegistrationNumber,
    ): self {
        return new self(
            employee: $employee,
            businessRegistrationNumber: $businessRegistrationNumber,
        );
    }

    public function id(): Ulid
    {
        return $this->id;
    }

    public function businessRegistrationNumber(): string
    {
        return $this->businessRegistrationNumber;
    }
}

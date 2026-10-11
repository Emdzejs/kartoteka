<?php

declare(strict_types=1);

namespace App\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\Address;
use App\Employee\Domain\Employee;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: BusinessRegistrationNumber::MAX_LENGTH)]
    private string $businessRegistrationNumber;

    #[ORM\Column(length: BusinessName::MAX_LENGTH)]
    private string $businessName;

    #[ORM\Column(length: JobRole::MAX_LENGTH)]
    private string $role;

    #[ORM\Embedded(class: EmploymentPeriod::class, columnPrefix: false)]
    private EmploymentPeriod $period;

    #[ORM\Embedded(class: Address::class)]
    private Address $businessAddress;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'previousEmployments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Employee $employee;

    private function __construct(
        Employee $employee,
        BusinessRegistrationNumber $businessRegistrationNumber,
        BusinessName $businessName,
        JobRole $role,
        EmploymentPeriod $period,
        Address $businessAddress,
    ) {
        $this->id = new Ulid();
        $this->employee = $employee;
        $this->businessRegistrationNumber = $businessRegistrationNumber->value;
        $this->businessName = $businessName->value;
        $this->role = $role->value;
        $this->period = $period;
        $this->businessAddress = $businessAddress;

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public static function create(
        Employee $employee,
        BusinessRegistrationNumber $businessRegistrationNumber,
        BusinessName $businessName,
        JobRole $role,
        EmploymentPeriod $period,
        Address $businessAddress
    ): self {
        return new self(
            employee: $employee,
            businessRegistrationNumber: $businessRegistrationNumber,
            businessName: $businessName,
            role: $role,
            period: $period,
            businessAddress: $businessAddress,
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

    public function businessName(): string
    {
        return $this->businessName;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function period(): EmploymentPeriod
    {
        return $this->period;
    }

    public function businessAddress(): Address
    {
        return $this->businessAddress;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

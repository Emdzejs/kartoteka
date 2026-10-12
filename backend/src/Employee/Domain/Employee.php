<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use App\Employee\Domain\PreviousEmployment\BusinessName;
use App\Employee\Domain\PreviousEmployment\BusinessRegistrationNumber;
use App\Employee\Domain\PreviousEmployment\EmploymentPeriod;
use App\Employee\Domain\PreviousEmployment\JobRole;
use App\Employee\Domain\PreviousEmployment\PreviousEmployment;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'employees')]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME)]
    private Ulid $id;

    #[ORM\Column(length: IdentityNumber::LENGTH, unique: true)]
    private string $identityNumber;

    #[ORM\Column(length: FirstName::MAX_LENGTH)]
    private string $firstName;

    #[ORM\Column(length: LastName::MAX_LENGTH)]
    private string $lastName;

    #[ORM\Column(length: 6, enumType: Gender::class)]
    private Gender $gender;

    #[ORM\Column(length: Email::MAX_LENGTH)]
    private string $email;

    #[ORM\Column(length: PhoneNumber::MAX_LENGTH)]
    private string $phoneNumber;

    #[ORM\Embedded(class: Address::class)]
    private Address $address;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, PreviousEmployment> */
    #[ORM\OneToMany(
        targetEntity: PreviousEmployment::class,
        mappedBy: 'employee',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private Collection $previousEmployments;

    private function __construct(
        IdentityNumber $identityNumber,
        FirstName $firstName,
        LastName $lastName,
        Gender $gender,
        Email $email,
        PhoneNumber $phoneNumber,
        Address $address,
    ) {
        $this->id = new Ulid();
        $this->identityNumber = $identityNumber->value;
        $this->firstName = $firstName->value;
        $this->lastName = $lastName->value;
        $this->gender = $gender;
        $this->email = $email->value;
        $this->phoneNumber = $phoneNumber->value;
        $this->address = $address;

        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->previousEmployments = new ArrayCollection();
    }

    public static function create(
        IdentityNumber $identityNumber,
        FirstName $firstName,
        LastName $lastName,
        Gender $gender,
        Email $email,
        PhoneNumber $phoneNumber,
        Address $address,
    ): self {
        return new self(
            identityNumber: $identityNumber,
            firstName: $firstName,
            lastName: $lastName,
            gender: $gender,
            email: $email,
            phoneNumber: $phoneNumber,
            address: $address,
        );
    }

    public function update(
        IdentityNumber $identityNumber,
        FirstName $firstName,
        LastName $lastName,
        Gender $gender,
        Email $email,
        PhoneNumber $phoneNumber,
        Address $address,
    ): void {
        $this->identityNumber = $identityNumber->value;
        $this->firstName = $firstName->value;
        $this->lastName = $lastName->value;
        $this->gender = $gender;
        $this->email = $email->value;
        $this->phoneNumber = $phoneNumber->value;
        $this->address = $address;

        $this->updatedAt = new DateTimeImmutable();
    }

    public function id(): Ulid
    {
        return $this->id;
    }

    public function identityNumber(): string
    {
        return $this->identityNumber;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function fullName(): string
    {
        return "$this->firstName $this->lastName";
    }

    public function gender(): Gender
    {
        return $this->gender;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /** @return PreviousEmployment[] */
    public function previousEmployments(): array
    {
        return array_values($this->previousEmployments->toArray());
    }

    public function addPreviousEmployment(
        BusinessRegistrationNumber $businessRegistrationNumber,
        BusinessName $businessName,
        JobRole $role,
        EmploymentPeriod $period,
        Address $businessAddress,
    ): PreviousEmployment {
        $employment = PreviousEmployment::create(
            employee: $this,
            businessRegistrationNumber: $businessRegistrationNumber,
            businessName: $businessName,
            role: $role,
            period: $period,
            businessAddress: $businessAddress,
        );

        $this->previousEmployments->add($employment);
        $this->updatedAt = new DateTimeImmutable();

        return $employment;
    }

    public function updatePreviousEmployment(
        Ulid $id,
        BusinessName $businessName,
        BusinessRegistrationNumber $businessRegistrationNumber,
        JobRole $role,
        EmploymentPeriod $period,
        Address $businessAddress,
    ): void {
        foreach ($this->previousEmployments as $employment) {
            if ((string) $employment->id() === (string) $id) {
                $employment->update(
                    $businessName,
                    $businessRegistrationNumber,
                    $role,
                    $period,
                    $businessAddress,
                );

                $this->updatedAt = new DateTimeImmutable();

                return;
            }
        }

        throw new PreviousEmploymentNotFound();
    }

    public function removePreviousEmployment(Ulid $id): void
    {
        foreach ($this->previousEmployments as $employment) {
            if ((string) $employment->id() === (string) $id) {
                $this->previousEmployments->removeElement($employment);
                $this->updatedAt = new DateTimeImmutable();

                return;
            }
        }

        throw new PreviousEmploymentNotFound();
    }
}

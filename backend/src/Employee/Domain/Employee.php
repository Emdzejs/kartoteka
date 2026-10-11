<?php

declare(strict_types=1);

namespace App\Employee\Domain;

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

    #[ORM\Column(length: Email::MAX_LENGTH)]
    private string $email;

    #[ORM\Column(length: PhoneNumber::MAX_LENGTH)]
    private string $phoneNumber;

    private function __construct(
        IdentityNumber $identityNumber,
        FirstName $firstName,
        LastName $lastName,
        Email $email,
        PhoneNumber $phoneNumber,
    ) {
        $this->id = new Ulid();
        $this->identityNumber = $identityNumber->value;
        $this->firstName = $firstName->value;
        $this->lastName = $lastName->value;
        $this->email = $email->value;
        $this->phoneNumber = $phoneNumber->value;
    }

    public static function create(
        IdentityNumber $identityNumber,
        FirstName $firstName,
        LastName $lastName,
        Email $email,
        PhoneNumber $phoneNumber,
    ): self {
        return new self(
            $identityNumber,
            $firstName,
            $lastName,
            $email,
            $phoneNumber,
        );
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

    public function email(): string
    {
        return $this->email;
    }

    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }
}

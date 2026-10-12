<?php

declare(strict_types=1);


namespace App\Employee\Application\UseCase;

use App\Employee\Application\DateParser;
use App\Employee\Application\Exception\DuplicateIdentityNumberException;
use App\Employee\Application\Exception\EmployeeNotFoundException;
use App\Employee\Application\Input\EditEmployeeInput;
use App\Employee\Application\Input\EditPreviousEmploymentInput;
use App\Employee\Domain\Address;
use App\Employee\Domain\Email;
use App\Employee\Domain\Employee;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\FirstName;
use App\Employee\Domain\Gender;
use App\Employee\Domain\IdentityNumber;
use App\Employee\Domain\LastName;
use App\Employee\Domain\PhoneNumber;
use App\Employee\Domain\PreviousEmployment\BusinessName;
use App\Employee\Domain\PreviousEmployment\BusinessRegistrationNumber;
use App\Employee\Domain\PreviousEmployment\EmploymentPeriod;
use App\Employee\Domain\PreviousEmployment\JobRole;
use InvalidArgumentException;
use Symfony\Component\Uid\Ulid;

final readonly class EditEmployee
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
    ) {
    }

    public function execute(Ulid $id, EditEmployeeInput $input): void
    {
        $employee = $this->employeeRepository->findById($id) ?? throw new EmployeeNotFoundException();
        $identityNumber = IdentityNumber::fromString($input->identityNumber);

        $this->validateIdentityNumberIsAvailable($employee, $identityNumber);

        $inputIds = $this->validateInputEmploymentIds($employee, $input->previousEmployments);

        $this->updateEmployeeDetails($employee, $input, $identityNumber);

        $this->updatePreviousEmployments($employee, $input->previousEmployments, $inputIds);

        $this->employeeRepository->save($employee);
    }

    private function validateIdentityNumberIsAvailable(Employee $employee, IdentityNumber $identityNumber): void {
        $isIdentityNumberChanged = $employee->identityNumber() !== $identityNumber->value;
        if ($isIdentityNumberChanged && $this->employeeRepository->existsByIdentityNumber($identityNumber)) {
            throw new DuplicateIdentityNumberException();
        }
    }

    /**
     * @param EditPreviousEmploymentInput[] $inputs
     * @return array<string, true>
     */
    private function validateInputEmploymentIds(Employee $employee, array $inputs): array {
        $existingIds = [];
        foreach ($employee->previousEmployments() as $employment) {
            $existingIds[(string) $employment->id()] = true;
        }

        $inputIds = [];
        foreach ($inputs as $input) {
            // new record
            if ($input->id === null) {
                continue;
            }

            if (!Ulid::isValid($input->id, Ulid::FORMAT_BASE_32 | Ulid::FORMAT_RFC_4122)) {
                throw new InvalidArgumentException('Invalid previous employment ID');
            }

            $id = (string) Ulid::fromString($input->id);
            if (!isset($existingIds[$id])) {
                throw new InvalidArgumentException('Previous employment does not belong to this employee');
            }

            if (isset($inputIds[$id])) {
                throw new InvalidArgumentException('Duplicate previous employment ID');
            }

            $inputIds[$id] = true;
        }

        return $inputIds;
    }

    private function updateEmployeeDetails(
        Employee          $employee,
        EditEmployeeInput $input,
        IdentityNumber    $identityNumber,
    ): void {
        $gender = Gender::tryFrom($input->gender) ?? throw new InvalidArgumentException('Invalid gender');

        $employee->update(
            identityNumber: $identityNumber,
            firstName: FirstName::fromString($input->firstName),
            lastName: LastName::fromString($input->lastName),
            gender: $gender,
            email: Email::fromString($input->email),
            phoneNumber: PhoneNumber::fromString($input->phoneNumber),
            address: Address::create(
                countryCode: $input->countryCode,
                postalCode: $input->postalCode,
                city: $input->city,
                street: $input->street,
                streetNumber: $input->streetNumber,
            ),
        );
    }

    /**
     * @param EditPreviousEmploymentInput[] $inputs
     * @param array<string, true> $submittedIds
     */
    private function updatePreviousEmployments(Employee $employee, array $inputs, array $submittedIds): void {
        $existingEmployments = $employee->previousEmployments();
        foreach ($inputs as $input) {
            $this->savePreviousEmployment($employee, $input);
        }

        foreach ($existingEmployments as $employment) {
            if (isset($submittedIds[(string) $employment->id()])) {
                continue;
            }

            $employee->removePreviousEmployment($employment->id());
        }
    }

    private function savePreviousEmployment(Employee $employee, EditPreviousEmploymentInput $input): void {
        $businessName = BusinessName::fromString($input->businessName);
        $registrationNumber = BusinessRegistrationNumber::fromString($input->businessRegistrationNumber);
        $role = JobRole::fromString($input->role);
        $period = EmploymentPeriod::create(DateParser::parse($input->startDate), DateParser::parse($input->endDate));
        $businessAddress = Address::create(
            countryCode: $input->businessCountryCode,
            postalCode: $input->businessPostalCode,
            city: $input->businessCity,
            street: $input->businessStreet,
            streetNumber: $input->businessStreetNumber,
        );

        if ($input->id === null) {
            $employee->addPreviousEmployment(
                businessRegistrationNumber: $registrationNumber,
                businessName: $businessName,
                role: $role,
                period: $period,
                businessAddress: $businessAddress,
            );

            return;
        }

        $employee->updatePreviousEmployment(
            id: Ulid::fromString($input->id),
            businessName: $businessName,
            businessRegistrationNumber: $registrationNumber,
            role: $role,
            period: $period,
            businessAddress: $businessAddress,
        );
    }
}

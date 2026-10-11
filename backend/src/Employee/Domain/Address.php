<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class Address
{
    public const int COUNTRY_CODE_LENGTH = 2;
    public const int MAX_POSTAL_CODE_LENGTH = 20;
    public const int MAX_CITY_LENGTH = 120;
    public const int MAX_STREET_LENGTH = 255;
    public const int MAX_STREET_NUMBER_LENGTH = 20;

    private const string LATVIA_COUNTRY_CODE = 'LV';
    private const int LATVIAN_POSTAL_CODE_DIGIT_LENGTH = 4;

    private function __construct(
        #[ORM\Column(name: 'country_code', length: self::COUNTRY_CODE_LENGTH)]
        private string $countryCode,

        #[ORM\Column(name: 'postal_code', length: self::MAX_POSTAL_CODE_LENGTH)]
        private string $postalCode,

        #[ORM\Column(name: 'city', length: self::MAX_CITY_LENGTH)]
        private string $city,

        #[ORM\Column(name: 'street', length: self::MAX_STREET_LENGTH)]
        private string $street,

        #[ORM\Column(name: 'street_number', length: self::MAX_STREET_NUMBER_LENGTH)]
        private string $streetNumber,
    ) {
    }

    public static function create(
        string $countryCode,
        string $postalCode,
        string $city,
        string $street,
        string $streetNumber,
    ): self {
        $countryCode = self::validateCountryCode($countryCode);
        $postalCode = self::validate(
            value: $postalCode,
            maxLength: self::MAX_POSTAL_CODE_LENGTH,
            fieldName: 'Postal code',
        );

        if ($countryCode === self::LATVIA_COUNTRY_CODE) {
            $postalCode = self::normalizeLatvianPostalCode($postalCode);
        }

        return new self(
            countryCode: self::validateCountryCode($countryCode),
            postalCode: self::validate($postalCode, self::MAX_POSTAL_CODE_LENGTH, 'Postal code'),
            city: self::validate($city, self::MAX_CITY_LENGTH, 'City'),
            street: self::validate($street, self::MAX_STREET_LENGTH, 'Street'),
            streetNumber: self::validate($streetNumber, self::MAX_STREET_NUMBER_LENGTH, 'Street number'),
        );
    }

    private static function validateCountryCode(string $value): string
    {
        $value = trim($value);

        if (strlen($value) !== self::COUNTRY_CODE_LENGTH || !ctype_alpha($value)) {
            throw new InvalidArgumentException(
                sprintf('Country code number must have %d letters', self::COUNTRY_CODE_LENGTH)
            );
        }

        return strtoupper($value);
    }

    private static function validate(string $value, int $maxLength, string $fieldName): string
    {
        $value = trim($value);

        if ($value === '' || mb_strlen($value) > $maxLength) {
            throw new InvalidArgumentException(
                sprintf(
                    "%s can't be empty or longer than %d characters",
                    $fieldName,
                    $maxLength,
                ),
            );
        }

        return $value;
    }

    private static function normalizeLatvianPostalCode(string $postalCode): string
    {
        $value = strtoupper($postalCode);

        if (!str_starts_with($value, self::LATVIA_COUNTRY_CODE)) {
            throw new InvalidArgumentException('Invalid Latvian postal code');
        }

        $digits = substr($value, self::COUNTRY_CODE_LENGTH);

        if (str_starts_with($digits, '-')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) !== self::LATVIAN_POSTAL_CODE_DIGIT_LENGTH || !ctype_digit($digits)) {
            throw new InvalidArgumentException('Invalid Latvian postal code');
        }

        return self::LATVIA_COUNTRY_CODE . '-' . $digits;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function streetNumber(): string
    {
        return $this->streetNumber;
    }
}

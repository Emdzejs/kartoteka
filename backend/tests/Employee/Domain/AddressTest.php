<?php

namespace App\Tests\Employee\Domain;

use App\Employee\Domain\Address;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testCreateCreatesValidAddress(): void
    {
        $countryCode = 'LV';
        $postalCode = 'LV-1111';
        $city = 'Rīga';
        $street = 'Piena iela';
        $streetNumber = '5A';

        $address = Address::create(
            countryCode: $countryCode,
            postalCode: $postalCode,
            city: $city,
            street: $street,
            streetNumber: $streetNumber,
        );

        self::assertSame($countryCode, $address->countryCode());
        self::assertSame($postalCode, $address->postalCode());
        self::assertSame($city, $address->city());
        self::assertSame($street, $address->street());
        self::assertSame($streetNumber, $address->streetNumber());
    }


    public function testCreateTrimsWhitespace(): void
    {
        $countryCode = 'LV';
        $postalCode = 'LV-1111';
        $city = 'Rīga';
        $street = 'Piena iela';
        $streetNumber = '5A';

        $address = Address::create(
            countryCode: "  $countryCode  ",
            postalCode: "  $postalCode  ",
            city: "  $city  ",
            street: "  $street  ",
            streetNumber: "  $streetNumber  ",
        );

        self::assertSame($countryCode, $address->countryCode());
        self::assertSame($postalCode, $address->postalCode());
        self::assertSame($city, $address->city());
        self::assertSame($street, $address->street());
        self::assertSame($streetNumber, $address->streetNumber());
    }

    public function testCreateNormalizesToUppercase(): void
    {
        $address = Address::create(
            countryCode: 'lv',
            postalCode: 'lv-1111',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );

        self::assertSame('LV', $address->countryCode());
        self::assertSame('LV-1111', $address->postalCode());
    }

    public function testCreateNormalizesLatvianPostalCodeWithoutHyphen(): void
    {
        $address = Address::create(
            countryCode: 'lv',
            postalCode: 'lv-1111',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );

        self::assertSame('LV-1111', $address->postalCode());
    }

    public function testCreateDoesNotNormalizeInternationalPostalCodes(): void
    {
        $address = Address::create(
            countryCode: 'gb',
            postalCode: 'GB1111',
            city: 'London',
            street: 'Milk Street',
            streetNumber: '1X',
        );

        self::assertSame('GB', $address->countryCode());
        self::assertSame('GB1111', $address->postalCode());
    }

    public function testCreateThrowsExceptionWhenLatvianPostalCodeTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'LV',
            postalCode: 'LV-12345',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );
    }

    public function testCreateThrowsExceptionWhenLatvianPostalCodeWrongHyphenPosition(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'LV',
            postalCode: 'LV11-11',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );
    }

    public function testCreateThrowsExceptionWhenCountryCodeTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'lvv',
            postalCode: 'lv-1111',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );
    }

    public function testCreateThrowsExceptionWhenCountryCodeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: '',
            postalCode: 'lv-1111',
            city: 'Rīga',
            street: 'Piena iela',
            streetNumber: '5A',
        );
    }

    public function testCreateThrowsExceptionWhenPostalCodeTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: str_repeat('g', Address::MAX_POSTAL_CODE_LENGTH + 1),
            city: 'London',
            street: 'Milk Street',
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenPostalCodeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: '',
            city: 'London',
            street: 'Milk Street',
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenCityTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: str_repeat('g', Address::MAX_CITY_LENGTH + 1),
            street: 'Milk Street',
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenCityEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: '',
            street: 'Milk Street',
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenStreetTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: 'London',
            street: str_repeat('g', Address::MAX_STREET_LENGTH + 1),
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenStreetEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: 'London',
            street: '',
            streetNumber: '1X',
        );
    }

    public function testCreateThrowsExceptionWhenStreetNumberTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: 'London',
            street: 'Milk Street',
            streetNumber: str_repeat('g', Address::MAX_STREET_LENGTH + 1),
        );
    }

    public function testCreateThrowsExceptionWhenStreetNumberEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::create(
            countryCode: 'GB',
            postalCode: 'gg',
            city: 'London',
            street: 'Milk Street',
            streetNumber: '',
        );
    }
}

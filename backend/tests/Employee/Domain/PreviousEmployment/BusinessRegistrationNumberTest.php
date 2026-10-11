<?php

namespace App\Tests\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\PreviousEmployment\BusinessRegistrationNumber;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BusinessRegistrationNumberTest extends TestCase
{
    public function testFromStringCreatesValidBusinessRegistrationNumber(): void
    {
        $input = 'LV5555551111';
        $registrationNumber = BusinessRegistrationNumber::fromString($input);

        self::assertSame($input, $registrationNumber->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $input = 'LV5555551111';
        $registrationNumber = BusinessRegistrationNumber::fromString(sprintf('     %s        ', $input));

        self::assertSame($input, $registrationNumber->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BusinessRegistrationNumber::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BusinessRegistrationNumber::fromString(str_repeat('e', BusinessRegistrationNumber::MAX_LENGTH + 1));
    }
}

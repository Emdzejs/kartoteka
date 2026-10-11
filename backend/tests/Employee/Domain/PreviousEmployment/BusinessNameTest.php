<?php

namespace App\Tests\Employee\Domain\PreviousEmployment;

use App\Employee\Domain\PreviousEmployment\BusinessName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BusinessNameTest extends TestCase
{
    public function testFromStringCreatesValidBusinessName(): void
    {
        $inputName = 'SIA Moov';
        $businessName = BusinessName::fromString($inputName);

        self::assertSame($inputName, $businessName->value);
    }


    public function testFromStringTrimsWhitespace(): void
    {
        $inputName = 'SIA Moov';
        $businessName = BusinessName::fromString(sprintf('     %s        ', $inputName));

        self::assertSame($inputName, $businessName->value);
    }

    public function testFromStringThrowsExceptionWhenEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BusinessName::fromString('  ');
    }

    public function testFromStringThrowsExceptionWhenTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BusinessName::fromString(str_repeat('e', BusinessName::MAX_LENGTH + 1));
    }
}
